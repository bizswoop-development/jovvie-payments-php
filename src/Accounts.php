<?php
namespace JovviePayments;

use JovviePayments\Http\Error\ResponseError;
use JovviePayments\Schema\SelfAccount;

class Accounts
{
	protected JovviePaymentsClient $client;

	public function __construct(JovviePaymentsClient $client)
	{
		$this->client = $client;
	}

	/**
	 * Retrieves the current account information.
	 *
	 * @return SelfAccount API response.
	 *
	 * @throws \Exception
	 * @throws ResponseError
	 */
	public function self(): SelfAccount
	{
		$response = $this->client->request('get', '/account');

		$body = $response->getBody();

		return new SelfAccount($body);
	}
}
