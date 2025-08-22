<?php

namespace JovviePayments\WebhookEvents\Events;

use JovviePayments\WebhookEvents\Error\WebhookError;

/**
 * Class for subscription renewal completed webhook events
 */
class SubscriptionRenewalCompleted extends BaseEvent
{
	/**
	 * @var string The event type
	 */
	protected string $type = 'jovvie.subscription.renewal.completed';

	/**
	 * Get the unique identifier of the payment.
	 *
	 * @return string Payment ID
	 * @throws WebhookError If payment ID is missing in the payload
	 */
	public function getPaymentId(): string
	{
		if (!isset($this->payload['payment']['id'])) {
			throw new WebhookError('Missing payment.id in payload');
		}
		return $this->payload['payment']['id'];
	}

	/**
	 * Get the payment mode used for this payment.
	 *
	 * @return string Payment mode
	 * @throws WebhookError If payment mode is missing in the payload
	 */
	public function getPaymentMode(): string
	{
		if (!isset($this->payload['payment']['mode'])) {
			throw new WebhookError('Missing payment.mode in payload');
		}
		return $this->payload['payment']['mode'];
	}

	/**
	 * Get the underlying Payment Intent ID associated with this payment.
	 *
	 * @return string Payment Intent ID
	 * @throws WebhookError If payment intent ID is missing in the payload
	 */
	public function getPaymentIntentId(): string
	{
		if (!isset($this->payload['paymentIntent']['id'])) {
			throw new WebhookError('Missing paymentIntent.id in payload');
		}
		return $this->payload['paymentIntent']['id'];
	}

	/**
	 * Get the unique identifier of the subscription.
	 *
	 * @return string Subscription ID
	 * @throws WebhookError If subscription ID is missing in the payload
	 */
	public function getSubscriptionId(): string
	{
		if (!isset($this->payload['subscription']['id'])) {
			throw new WebhookError('Missing subscription.id in payload');
		}
		return $this->payload['subscription']['id'];
	}

	/**
	 * Get the invoice amount associated with the payment.
	 *
	 * @return int Invoice amount
	 * @throws WebhookError If invoice amount is missing in the payload
	 */
	public function getInvoiceAmount(): int
	{
		if (!isset($this->payload['invoice']['amount'])) {
			throw new WebhookError('Missing invoice.amount in payload');
		}
		return $this->payload['invoice']['amount'];
	}

	/**
	 * Get the currency code for the invoice related to the payment.
	 *
	 * @return string Currency code
	 * @throws WebhookError If invoice currency is missing in the payload
	 */
	public function getInvoiceCurrency(): string
	{
		if (!isset($this->payload['invoice']['currency'])) {
			throw new WebhookError('Missing invoice.currency in payload');
		}
		return $this->payload['invoice']['currency'];
	}

	/**
	 * Get the payment status of the invoice.
	 *
	 * @return bool True if the invoice is paid, false otherwise
	 * @throws WebhookError If invoice paid status is missing in the payload
	 */
	public function getInvoicePaid()
	{
		if (!isset($this->payload['invoice']['paid'])) {
			throw new WebhookError('Missing invoice.paid in payload');
		}
		return (bool)$this->payload['invoice']['paid'];
	}

	/**
	 * Get the timestamp when the invoice was created.
	 *
	 * @return int Timestamp of invoice creation
	 * @throws WebhookError If invoice creation timestamp is missing in the payload
	 */
	public function getInvoiceCreated()
	{
		if (!isset($this->payload['invoice']['created'])) {
			throw new WebhookError('Missing invoice.created in payload');
		}
		return (int)$this->payload['invoice']['created'];
	}
}
