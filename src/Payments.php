<?php

namespace JovviePayments;

use JovviePayments\Http\Error\ResponseError;
use JovviePayments\Schema\OneTimePayment\OneTimePayment;
use JovviePayments\Schema\Payment\Payment;
use JovviePayments\Schema\SubscriptionPayment\SubscriptionPayment;

class Payments
{
	protected JovviePaymentsClient $client;

	public function __construct(JovviePaymentsClient $client)
	{
		$this->client = $client;
	}

	/**
	 * Creates a one-time payment.
	 *
	 * @param int $amount The payment amount.
	 * @param string $currency The currency (e.g., usd, eur).
	 * @param string $description The payment description.
	 * @param string $returnUrl The URL to redirect to after payment.
	 * @param string $cancelUrl The URL to redirect to if payment is aborted.
	 * @param array|null $options Optional parameters:
	 *     - lineItems (array, optional): Array of items (name, quantity, amount).
	 *     - lineTotal (string, optional): Custom total representation (e.g., '1000 USD').
	 *     - preferences (array, optional): Custom preferences.
	 *     - metadata (array, optional): Custom metadata.
	 * @return OneTimePayment API response.
	 *
	 * @throws \Exception
	 * @throws ResponseError
	 */
	public function createOneTimePayment(int $amount, string $currency, string $description, string $returnUrl, string $cancelUrl, ?array $options = []): OneTimePayment
	{
		$data = array_merge($options, [
			'amount' => $amount,
			'currency' => strtolower($currency),
			'description' => $description,
			'returnUrl' => $returnUrl,
			'cancelUrl' => $cancelUrl,
		]);

		$response = $this->client->request('POST', 'checkout-page/payments', [], $data);
		$data = $response->getBody();

		/* @var OneTimePayment */
		return $this->prepareReturn($data);
	}

	/**
	 * Creates a subscription payment.
	 *
	 * @param mixed $customer The customer ID or object (email, name, metadata?).
	 * @param string $currency The currency (e.g., usd, eur).
	 * @param array $items Array of items.
	 * @param string $description The payment description.
	 * @param string $returnUrl The URL to redirect to after payment.
	 * @param string $cancelUrl The URL to redirect to if payment is aborted.
	 * @param array|null $options Optional parameters:
	 *     - lineItems (array, optional): Array of items (name, quantity, interval).
	 *     - lineTotal (string, optional): Custom total representation (e.g., '1000 USD').
	 *     - preferences (array, optional): Custom preferences.
	 *     - metadata (array, optional): Custom metadata.
	 * @return SubscriptionPayment API response.
	 *
	 * @throws \Exception
	 * @throws ResponseError
	 */
	public function createSubscriptionsPayment($customer, array $items, string $currency, string $description, string $returnUrl, string $cancelUrl, ?array $options = []): SubscriptionPayment
	{
		$data = array_merge($options, [
			'customer' => $customer,
			'currency' => strtolower($currency),
			'description' => $description,
			'returnUrl' => $returnUrl,
			'cancelUrl' => $cancelUrl,
			'items' => $items,
		]);

		$response = $this->client->request('POST', 'checkout-page/payments/subscriptions', [], $data);

		$data = $response->getBody();

		/* @var SubscriptionPayment */
		return $this->prepareReturn($data);
	}

	/**
	 * @return OneTimePayment|SubscriptionPayment
	 * @throws ResponseError
	 * @throws \Exception
	 */
	public function get($id): Payment
	{
		$response = $this->client->request('GET', 'checkout-page/payments/' . $id);
		$data = $response->getBody();

		return $this->prepareReturn($data);
	}

	protected function prepareReturn($data): Payment
	{
		if ($data->type === 'one-time') {
			return new OneTimePayment($data);
		}
		if ($data->type === 'subscription') {
			return new SubscriptionPayment($data);
		}

		throw new \Exception('Unknown payment type: ' . $data->type);
	}
}
