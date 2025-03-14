<?php

namespace JovviePayments\Schema\OneTimePayment;

use JovviePayments\Schema\Payment\LineItem;

class OneTimePaymentLineItem extends LineItem implements \JsonSerializable
{
	public string $quantity;

	public function __construct($data)
	{
		$this->quantity = $data->quantity;
		parent::__construct($data);
	}

	public function jsonSerialize(): array
	{
		return array_merge(parent::jsonSerialize(), [
			'quantity' => $this->quantity,
		]);
	}
}
