<?php

namespace JovviePayments\Schema;

class ConnectionToken implements \JsonSerializable
{
	public string $secret;
	public ?string $location;

	public function __construct($data)
	{
		$this->secret = $data->secret;
		$this->location = $data->location ?? null;
	}

	public function jsonSerialize(): array
	{
		return [
			'secret' => $this->secret,
			'location' => $this->location
		];
	}
}
