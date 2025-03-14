<?php

namespace JovviePayments;

use InvalidArgumentException;
use JovviePayments\Http\Client;

class JovviePaymentsClient extends Client
{
	protected array $instanceStorage = [];

	const VERSION = '1.0.0';

	/**
	 * Constructor for initializing the payment gateway.
	 *
	 * @param string $publicKey The public key for authentication.
	 * @param string $secretKey The secret key for authentication.
	 * @param string $platformProvider The platform provider ID.
	 * @param string $mode The mode of operation ('live' or 'test'). Default is 'live'.
	 * @param array|null $config Optional configuration settings. Default is null.
	 *     - hostUrl (string, optional): The base URL for API requests. Defaults to 'https://payments.bizswoop.app'.
	 *     - userAgent (string, optional): The user agent for API requests. Appended to the default user agent ('PHP/8.4.2 JovviePayments/1.0.0').
	 *
	 * @throws InvalidArgumentException If an invalid mode is provided.
	 */
	public function __construct(string $publicKey, string $secretKey, string $platformProvider, string $mode = 'live', array $config = null)
	{
		if (!in_array($mode, ['live', 'test'], true)) {
			throw new InvalidArgumentException("Invalid mode: {$mode}. Allowed values are 'live' or 'test'.");
		}

		if ($mode === 'live' && $platformProvider === 'debug') {
			throw new InvalidArgumentException("Only test mode is allowed for debug platform provider.");
		}

		$this->publicKey = $publicKey;
		$this->secretKey = $secretKey;
		$this->platformProvider = $platformProvider;
		$this->mode = $mode;
		$this->hostUrl = $config['hostUrl'] ?? 'https://payments.bizswoop.app';


		$this->userAgent = implode(' ', array_filter([
			$config['userAgent'],
			'PHP/' . PHP_VERSION,
			'JovviePaymentsClient/' . self::VERSION,
		]));
	}

	protected function getBaseUrl(): string
	{
		return $this->hostUrl . ($this->mode === 'test' ? '/api/connect/v1/stripe-account/test/' : '/api/connect/v1/stripe-account/');
	}

	/**
	 * Universal method to handle singleton service instances.
	 *
	 * @template T
	 * @param class-string<T> $class
	 * @return T
	 */
	protected function getServiceInstance(string $class)
	{
		if (!isset($this->instanceStorage[$class])) {
			$this->instanceStorage[$class] = new $class($this);
		}

		return $this->instanceStorage[$class];
	}

	public function payments(): Payments
	{
		return $this->getServiceInstance(Payments::class);
	}

	public function authorizationLinks(): AuthorizationLinks
	{
		return $this->getServiceInstance(AuthorizationLinks::class);
	}

	public function connectionTokens(): ConnectionTokens
	{
		return $this->getServiceInstance(ConnectionTokens::class);
	}

	public function accounts(): Accounts
	{
		return $this->getServiceInstance(Accounts::class);
	}
}
