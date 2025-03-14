<?php

namespace JovviePayments\Schema;

class SelfAccount implements \JsonSerializable
{
	public string $id;
	public string $platformPublicKey;

	public function __construct($data)
	{
		$this->id = $data->id;
		$this->platformPublicKey = $data->platformPublicKey;
	}

	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'platformPublicKey' => $this->platformPublicKey
		];
	}
}
