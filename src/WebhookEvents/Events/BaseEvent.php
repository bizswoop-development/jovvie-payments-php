<?php

namespace JovviePayments\WebhookEvents\Events;

/**
 * Base class for webhook events
 */
abstract class BaseEvent implements \JsonSerializable
{
	/**
	 * @var string The event type
	 */
	protected string $type;

	/**
	 * @var string The unique identifier for the event
	 */
	protected string $id;

	/**
	 * @var array The raw payload data
	 */
	protected array $payload;

	/**
	 * Constructor
	 *
	 * @param array $payload The webhook payload
	 */
	public function __construct(array $payload, string $id)
	{
		$this->payload = $payload;
		$this->id = $id;
	}

	/**
	 * Get the event type
	 *
	 * @return string The event type
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * Get the unique identifier for the event
	 *
	 * @return string The event ID
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Get the raw payload
	 *
	 * @return array The raw payload
	 */
	public function getPayload(): array
	{
		return $this->payload;
	}

	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'type' => $this->type,
			'payload' => $this->payload,
			'version' => 'v1',
		];
	}
}
