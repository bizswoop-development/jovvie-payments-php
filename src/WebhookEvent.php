<?php

namespace JovviePayments;

use JovviePayments\WebhookEvents\Error\WebhookError;
use JovviePayments\WebhookEvents\Events\PaymentCancelled;
use JovviePayments\WebhookEvents\Events\PaymentCompleted;
use JovviePayments\WebhookEvents\Events\PaymentFailed;
use JovviePayments\WebhookEvents\Events\PaymentRefunded;
use JovviePayments\WebhookEvents\Events\SubscriptionCancelled;
use JovviePayments\WebhookEvents\Events\SubscriptionRenewalCompleted;
use JovviePayments\WebhookEvents\Events\SubscriptionRenewalFailed;
use JovviePayments\WebhookEvents\Events\SubscriptionStatusUpdate;
use JovviePayments\WebhookEvents\Events\BaseEvent;
use JovviePayments\WebhookEvents\WebhookPayloadParser;

/**
 * Class for handling webhook events
 */
class WebhookEvent
{
	private const EVENT_MAP = [
		'jovvie.payment.cancelled' => PaymentCancelled::class,
		'jovvie.payment.completed' => PaymentCompleted::class,
		'jovvie.payment.failed' => PaymentFailed::class,
		'jovvie.payment.refunded' => PaymentRefunded::class,
		'jovvie.subscription.renewal.completed' => SubscriptionRenewalCompleted::class,
		'jovvie.subscription.renewal.failed' => SubscriptionRenewalFailed::class,
		'jovvie.subscription.status.update' => SubscriptionStatusUpdate::class,
		'jovvie.subscription.cancelled' => SubscriptionCancelled::class,
	];

	/**
	 * Parse and validate a webhook request
	 *
	 * @param string $data The raw JSON data from the webhook request
	 * @param string $signature The request signature from the headers
	 * @param string $webhookKey The secret key for signature validation
	 *
	 * @return BaseEvent The webhook event instance
	 *
	 * @throws WebhookError
	 */
	public static function constructEvent(string $data, string $signature, string $webhookKey): BaseEvent
	{
		$parsedData = WebhookPayloadParser::validateRequestSignature($data, $signature, $webhookKey);

		return self::constructFrom($parsedData);
	}

	/**
	 * Create an event instance based on the event type
	 *
	 * @param string|array $data The raw JSON data from the webhook request
	 *
	 * @return BaseEvent The webhook event instance
	 * @throws WebhookError
	 */
	public static function constructFrom($data): BaseEvent
	{
		if (is_string($data)) {
			$data = WebhookPayloadParser::parseJson($data);
		} else if (!is_array($data)) {
			throw new WebhookError('Invalid data format. Expected JSON string or array.');
		}

		$id = $data['id'] ?? null;
		if (!$id) {
			throw new WebhookError('Missing event ID in webhook data.');
		}
		$type = $data['type'] ?? null;
		if (!$type) {
			throw new WebhookError('Missing event type in webhook data.');
		}
		$payload = $data['payload'] ?? null;
		if (!$payload) {
			throw new WebhookError('Missing event payload in webhook data.');
		}
		$version = $data['version'] ?? null;

		if ($version !== 'v1') {
			throw new WebhookError('Unsupported webhook version: ' . $version);
		}

		$class = self::EVENT_MAP[$type] ?? null;

		if ($class) {
			return new $class($payload, $id);
		} else {
			throw new WebhookError('Unsupported event type: ' . $type);
		}
	}
}
