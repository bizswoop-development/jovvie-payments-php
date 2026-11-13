<?php

namespace JovviePayments\WebhookEvents\Events;

/**
 * Webhook event with a type this SDK version does not know.
 * Returned when $throwUnknownEvents is false, otherwise available via UnknownEventError::getEvent().
 * Read the data from getPayload().
 */
class UnknownEvent extends BaseEvent
{
	/**
	 * Constructor
	 *
	 * @param array $payload The webhook payload
	 * @param string $id The unique identifier for the event
	 * @param string $type The event type as received
	 */
	public function __construct(array $payload, string $id, string $type)
	{
		parent::__construct($payload, $id);
		$this->type = $type;
	}
}
