<?php

namespace JovviePayments\Schema\SubscriptionPayment;

class GeneratedSubscriptionItem implements \JsonSerializable
{
	public string $id;
	public GeneratedSubscriptionItemPrice $price;

	public function __construct($data)
	{
		$this->id = $data->id;
		$this->price = new GeneratedSubscriptionItemPrice($data->price);
	}

	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'price' => $this->price->jsonSerialize(),
		];
	}
}
