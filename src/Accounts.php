<?php
namespace JovviePayments;

use JovviePayments\Http\Error\ResponseError;
use JovviePayments\Schema\Account;
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
		$response = $this->client->request('get', 'account');

		$body = $response->getBody();

		return new SelfAccount($body);
	}

    /**
     * Retrieves all accounts.
     *
     * @return Account[] Array of Account objects.
     *
     * @throws \Exception
     * @throws ResponseError
     */
    public function getAll(): array
    {
        $response = $this->client->request('get', 'accounts');

        $body = $response->getBody();

        return array_map(function($item) {
            return new Account($item);
        }, $body);
    }
}
