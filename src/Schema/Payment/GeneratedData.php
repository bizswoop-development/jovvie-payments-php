<?php

namespace JovviePayments\Schema\Payment;

abstract class GeneratedData implements \JsonSerializable
{
	public string $paymentIntentId;

	public function __construct($data)
	{
		$this->paymentIntentId = $data->paymentIntentId;
	}

	public function jsonSerialize(): array
	{
		return [
			'paymentIntentId' => $this->paymentIntentId,
		];
	}
}
