<?php

namespace JovviePayments\WebhookEvents\Events;

use JovviePayments\WebhookEvents\Error\WebhookError;

/**
 * Class for payment abandoned webhook events
 */
class PaymentAbandoned extends BaseEvent
{
	/**
	 * @var string The event type
	 */
	protected string $type = 'jovvie.payment.abandoned';

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
	 * Get the payment status after the event.
	 *
	 * @return string Payment status, e.g. 'completed'
	 * @throws WebhookError If payment status is missing in the payload
	 */
	public function getPaymentStatus(): string
	{
		if (!isset($this->payload['payment']['status'])) {
			throw new WebhookError('Missing payment.status in payload');
		}
		return $this->payload['payment']['status'];
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
}
