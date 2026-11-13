<?php

namespace JovviePayments\WebhookEvents\Error;

use JovviePayments\WebhookEvents\Events\UnknownEvent;

/**
 * Thrown when the event type is unknown to this SDK version and $throwUnknownEvents is true.
 */
class UnknownEventError extends WebhookError
{
	/**
	 * @var UnknownEvent The event with the unknown type
	 */
	private UnknownEvent $event;

	public function __construct(UnknownEvent $event)
	{
		parent::__construct('Unsupported event type: ' . $event->getType());
		$this->event = $event;
	}

	/**
	 * Get the event with the unknown type
	 *
	 * @return UnknownEvent The event
	 */
	public function getEvent(): UnknownEvent
	{
		return $this->event;
	}
}
