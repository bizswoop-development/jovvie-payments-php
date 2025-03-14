<?php

namespace JovviePayments\Schema\SubscriptionPayment;

class GeneratedSubscriptionItemPrice implements \JsonSerializable
{
	public string $id;
	public string $product;
	public string $name;

	public function __construct($data)
	{
		$this->id = $data->id;
		$this->product = $data->product;
		$this->name = $data->name;
	}

	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'product' => $this->product,
			'name' => $this->name,
		];
	}
}
