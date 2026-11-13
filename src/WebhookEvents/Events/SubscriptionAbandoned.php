<?php

namespace JovviePayments\WebhookEvents\Events;

use JovviePayments\WebhookEvents\Error\WebhookError;

/**
 * Class for subscription abandoned webhook events
 */
class SubscriptionAbandoned extends BaseEvent
{
	/**
	 * @var string The event type
	 */
	protected string $type = 'jovvie.subscription.abandoned';

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
}
