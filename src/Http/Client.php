<?php

namespace JovviePayments\Http;

use InvalidArgumentException;
use JovviePayments\Http\Error\BadRequestError;
use JovviePayments\Http\Error\ResponseError;
use JovviePayments\Http\Error\UnauthorizedError;

abstract class Client
{
	protected string $publicKey;
	protected string $secretKey;
	protected string $platformProvider;
	protected string $mode;
	protected string $hostUrl;
	protected string $userAgent;

	/**
	 * Sends an HTTP request using cURL.
	 *
	 * @param array|string $methodOrConfig Either a config array or an HTTP method.
	 * @param string|null $url The API endpoint (if method is passed separately).
	 * @param array|null $params Optional query parameters.
	 * @param array|null $data Optional request payload.
	 * @param array|null $config Optional configuration settings (e.g., headers).
	 *
	 * @return Response Contains the response data and HTTP status code.
	 *
	 * @throws \InvalidArgumentException If required parameters are missing or invalid.
	 * @throws BadRequestError If the request is invalid.
	 * @throws UnauthorizedError If the request is unauthorized.
	 * @throws ResponseError If the response status code is not in the 2xx range.
	 * @throws \Exception For any other errors.
	 */
	public function request($methodOrConfig, ?string $url = null, ?array $params = [], ?array $data = [], ?array $config = []): Response
	{
		$ch = null;

		try {
			if (is_array($methodOrConfig)) {
				$config = $methodOrConfig;
				$method = strtoupper($config['method'] ?? '');
				$url = $config['url'] ?? null;
				$params = $config['params'] ?? [];
				$data = $config['data'] ?? [];
			} else {
				$method = strtoupper($methodOrConfig);

				if ($url === null) {
					throw new \InvalidArgumentException('URL is required.');
				}
			}

			if (empty($method)) {
				throw new \InvalidArgumentException('HTTP method is required.');
			}
			if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
				throw new \InvalidArgumentException('Invalid HTTP method: ' . $method);
			}

			$baseHeaders = [
				'Authorization: Bearer ' . $this->generateAuthorizationHeader(),
				'Content-Type: application/json',
				'Accept: application/json',
				'Platform-Provider: ' . $this->platformProvider,
			];

			$customHeaders = $config['headers'] ?? [];
			$headers = array_merge($baseHeaders, $customHeaders);


			// Add query parameters if provided
			if (!empty($params)) {
				$queryString = http_build_query($params);
				$separator = strpos($url, '?') !== false ? '&' : '?';
				$url .= $separator . $queryString;
			}

			$url = $this->getBaseUrl() . $url;

			$ch = curl_init($url);

			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_USERAGENT => $this->userAgent,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_HEADER => true,
			]);

			if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
				$data = $this->jsonEncode($data);

				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				$headers[] = 'Content-Length: ' . strlen($data);
			}

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);

			$response = curl_exec($ch);

			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$responseHeadersRaw = substr($response, 0, $headerSize);
			$responseBody = substr($response, $headerSize);
			$responseHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$responseHeaders = $this->parseHeaders($responseHeadersRaw);

			if ($response === false) {
				throw new \Exception('cURL Error (' . curl_errno($ch) . '): ' . curl_error($ch));
			}

			if (strpos($responseHeaders['Content-Type'], 'application/json') >= 0) {
				$responseBody = json_decode($responseBody);
			}

			$httpResponse = new Response($responseBody, $responseHttpCode, $responseHeaders);

			if ($responseHttpCode >= 400) {
				switch ($responseHttpCode) {
					case 401:
						throw new UnauthorizedError($httpResponse, 'Unauthorized request, url: ' . $url);
					case 400:
						throw new BadRequestError($httpResponse, 'Bad request, url: ' . $url);
					default:
						throw new ResponseError($httpResponse, 'Request failed, url: ' . $url);
				}
			}

			return $httpResponse;
		} catch (InvalidArgumentException|ResponseError $e) {
			throw $e;
		} catch (\Exception $e) {
			throw new \Exception('Failed to make request: ' . $e->getMessage(), 0, $e);
		} finally {
			if (is_resource($ch)) {
				curl_close($ch);
			}
		}
	}

	/**
	 * Sends a GET request.
	 *
	 * @param array|string $configOrUrl Either a config array (must include 'url') or a URL string.
	 * @param array|null $params Optional query parameters (if URL is passed separately).
	 * @param array|null $config Optional headers and settings.
	 *
	 * @return Response The API response.
	 *
	 * @throws \InvalidArgumentException If required parameters are missing or invalid.
	 * @throws ResponseError If the response status code is not in the 2xx range.
	 * @throws \Exception For any other errors.
	 */
	public function get($configOrUrl, ?array $params = [], ?array $config = []): Response
	{
		if (is_array($configOrUrl)) {
			$configOrUrl['method'] = 'GET';
			return $this->request($configOrUrl);
		}

		return $this->request('GET', $configOrUrl, $params, [], $config);
	}

	/**
	 * Sends a POST request.
	 *
	 * @param array|string $configOrUrl Either a config array (must include 'url') or a URL string.
	 * @param array|null $params Optional query parameters.
	 * @param array|null $data The request payload.
	 * @param array|null $config Optional headers and settings.
	 *
	 * @return Response The API response.
	 *
	 * @throws \InvalidArgumentException If required parameters are missing or invalid.
	 * @throws ResponseError If the response status code is not in the 2xx range.
	 * @throws \Exception For any other errors.
	 */
	public function post($configOrUrl, ?array $params = [], ?array $data = [], ?array $config = []): Response
	{
		if (is_array($configOrUrl)) {
			$configOrUrl['method'] = 'POST';
			return $this->request($configOrUrl);
		}

		return $this->request('POST', $configOrUrl, $params, $data, $config);
	}

	/**
	 * Sends a PUT request.
	 *
	 * @param array|string $configOrUrl Either a config array (must include 'url') or a URL string.
	 * @param array|null $params Optional query parameters.
	 * @param array|null $data The request payload.
	 * @param array|null $config Optional headers and settings.
	 *
	 * @return Response The API response.
	 *
	 * @throws \InvalidArgumentException If required parameters are missing or invalid.
	 * @throws ResponseError If the response status code is not in the 2xx range.
	 * @throws \Exception For any other errors.
	 */
	public function put($configOrUrl, ?array $params = [], ?array $data = [], ?array $config = []): Response
	{
		if (is_array($configOrUrl)) {
			$configOrUrl['method'] = 'PUT';
			return $this->request($configOrUrl);
		}

		return $this->request('PUT', $configOrUrl, $params, $data, $config);
	}

	/**
	 * Sends a PATCH request.
	 *
	 * @param array|string $configOrUrl Either a config array (must include 'url') or a URL string.
	 * @param array|null $params Optional query parameters.
	 * @param array|null $data The request payload.
	 * @param array|null $config Optional headers and settings.
	 *
	 * @return Response The API response.
	 *
	 * @throws \InvalidArgumentException If required parameters are missing or invalid.
	 * @throws ResponseError If the response status code is not in the 2xx range.
	 * @throws \Exception For any other errors.
	 */
	public function patch($configOrUrl, ?array $params = [], ?array $data = [], ?array $config = []): Response
	{
		if (is_array($configOrUrl)) {
			$configOrUrl['method'] = 'PATCH';
			return $this->request($configOrUrl);
		}

		return $this->request('PATCH', $configOrUrl, $params, $data, $config);
	}

	/**
	 * Sends a DELETE request.
	 *
	 * @param array|string $configOrUrl Either a config array (must include 'url') or a URL string.
	 * @param array|null $params Optional query parameters.
	 * @param array|null $config Optional headers and settings.
	 *
	 * @return Response The API response.
	 *
	 * @throws \InvalidArgumentException If required parameters are missing or invalid.
	 * @throws ResponseError If the response status code is not in the 2xx range.
	 * @throws \Exception For any other errors.
	 */
	public function delete($configOrUrl, ?array $params = [], ?array $config = []): Response
	{
		if (is_array($configOrUrl)) {
			$configOrUrl['method'] = 'DELETE';
			return $this->request($configOrUrl);
		}

		return $this->request('DELETE', $configOrUrl, $params, [], $config);
	}

	/**
	 * Generates a JWT Authorization header.
	 *
	 * @return string A JWT token.
	 * @throws \Exception If the JWT token cannot be generated.
	 */
	protected function generateAuthorizationHeader(): string
	{
		try {
			$signingSecret = $this->secretKey;

			$header = [
				'alg' => 'HS256',
				'typ' => 'JWT',
			];

			$payload = [
				'publicKey' => $this->publicKey,
				'iat' => time(),
			];

			$header = $this->base64UrlEncode($this->jsonEncode($header));
			$payload = $this->base64UrlEncode($this->jsonEncode($payload));
			$signature = $this->base64UrlEncode(hash_hmac('sha256', $header . '.' . $payload, $signingSecret, true));

			return $header . '.' . $payload . '.' . $signature;
		} catch (\Exception $e) {
			throw new \Exception('Failed to create JWT token', 0, $e);
		}
	}

	/**
	 * Encodes data using Base64 URL encoding.
	 *
	 * This function replaces `+` with `-`, `/` with `_`, and removes `=` padding
	 * to make the encoding URL-safe, as required for JWT.
	 *
	 * @param string $data The input data to encode.
	 * @return string The Base64 URL-encoded string.
	 */
	protected function base64UrlEncode(string $data): string
	{
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	/**
	 * Encodes data to JSON with proper flags.
	 *
	 * Uses `JSON_UNESCAPED_SLASHES` and `JSON_UNESCAPED_UNICODE`
	 * to ensure a clean JSON output without unnecessary escaping.
	 *
	 * @param mixed $data The data to encode.
	 * @return string The JSON-encoded string.
	 * @throws \JsonException If encoding fails.
	 */
	protected function jsonEncode($data): string
	{
		return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
	}

	/**
	 * Parses raw HTTP headers into an associative array.
	 *
	 * @param string $headers The raw headers string.
	 * @return array The parsed headers as key-value pairs.
	 */
	protected function parseHeaders(string $headers): array
	{
		$parsedHeaders = [];
		$lines = explode("\r\n", trim($headers));

		foreach ($lines as $line) {
			if (strpos($line, ': ') !== false) {
				[$key, $value] = explode(': ', $line, 2);
				$parsedHeaders[$key] = trim($value);
			}
		}

		return $parsedHeaders;
	}
}
