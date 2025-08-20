<?php

namespace JovviePayments\WebhookEvents;

use JovviePayments\WebhookEvents\Error\WebhookError;

/**
 * Class for parsing and validating webhook payloads
 */
class WebhookPayloadParser
{
	/**
	 * Parse a JSON string
	 *
	 * @param string $json The JSON string to parse
	 * @return array The parsed JSON or null if parsing fails
	 * @throws WebhookError If the JSON is invalid
	 */
	public static function parseJson(string $json): ?array
	{
		$data = json_decode($json, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new WebhookError('Invalid JSON format: ' . json_last_error_msg());
		}

		return $data;
	}

	/**
	 * Validate the webhook signature
	 *
	 * @param string $data The raw data
	 * @param string $signature The request signature from the headers
	 * @param string $webhookKey The secret key for signature validation
	 * @return array The parsed data if validation is successful
	 * @throws WebhookError If validation fails
	 */
	public static function validateRequestSignature(string $data, string $signature, string $webhookKey): array
	{
		$parsedData = self::parseJson($data);

		$id = $parsedData['id'] ?? null;
		$timestamp = $parsedData['timestamp'] ?? null;
		$version = $parsedData['version'] ?? null;

		$missingFields = [];
		if (empty($signature)) $missingFields[] = 'id';
		if (empty($id)) $missingFields[] = 'id';
		if (empty($timestamp)) $missingFields[] = 'timestamp';
		if (empty($version)) $missingFields[] = 'version';

		if (!empty($missingFields)) {
			throw new WebhookError('Missing required fields: ' . implode(', ', $missingFields));
		}

		if ($timestamp < (time() - 300)) {
			throw new WebhookError('Webhook timestamp is too old');
		}

		$rawToSign = $id . '.' . $version . '.' . $timestamp . '.' . $data;

		$expectedSignature = hash_hmac('sha256', $rawToSign, $webhookKey);

		if (hash_equals($expectedSignature, $signature)) {
			return $parsedData;
		} else {
			throw new WebhookError('Invalid webhook signature');
		}
	}
}
