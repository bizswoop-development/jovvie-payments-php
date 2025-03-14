<?php

namespace JovviePayments\Schema\Payment;

abstract class LineItem implements \JsonSerializable
{
	public string $amount;
	public string $name;

	public function __construct($data)
	{
		$this->amount = $data->amount;
		$this->name = $data->name;
	}

	public function jsonSerialize(): array
	{
		return [
			'amount' => $this->amount,
			'name' => $this->name,
		];
	}
}
