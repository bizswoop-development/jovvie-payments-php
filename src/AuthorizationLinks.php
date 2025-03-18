<?php

namespace JovviePayments;

use JovviePayments\Http\Error\ResponseError;
use JovviePayments\Schema\AuthorizationLink;

class AuthorizationLinks
{
	protected JovviePaymentsClient $client;

	public function __construct(JovviePaymentsClient $client)
	{
		$this->client = $client;
	}

	/**
	 * Generates an authorization link with optional parameters for customization.
	 *
	 * @param array $options options:
	 *   - redirectUrl (string, optional): The URL the user will be redirected to after successful authorization.
	 *   - locationId (string, optional): The location ID of terminal to be used for payments.
	 *   - paymentMethodTypes (true|array, optional): Specifies the payment methods the user is allowed to use.
	 *     Accepted values: ['card', 'terminal', 'request-terminal'].
	 *     If set to `true`, 'request-terminal' in payment options will be automatically replaced with 'terminal'.
	 * @param string|null $expiresAt : The expiration date and time of the link in ISO 8601 format.
	 *
	 * @return AuthorizationLink The API response.
	 *
	 * @throws \Exception If an unexpected error occurs.
	 * @throws ResponseError If the API request fails.
	 */
	public function create(array $options, string $expiresAt = null): AuthorizationLink
	{
		$data = [
			'options' => $options,
		];

		if ($expiresAt) {
			$data['expiresAt'] = $expiresAt;
		}

		$response = $this->client->request('POST', 'authorization-links', [], $data);
		$body = $response->getBody();
		return new AuthorizationLink($body);
	}
}
