<?php

namespace JovviePayments;

use JovviePayments\Http\Error\ResponseError;

class ConnectionTokens
{
	protected JovviePaymentsClient $client;

	public function __construct(JovviePaymentsClient $client)
	{
		$this->client = $client;
	}

	/**
	 * Creates a new connection token.
	 *
	 * @return Schema\ConnectionToken
	 *
	 * @throws \Exception
	 * @throws ResponseError
	 */
	public function create(): Schema\ConnectionToken
	{
		$response = $this->client->request('POST', 'connection-tokens');

		$body = $response->getBody();

		return new Schema\ConnectionToken($body);
	}

}
