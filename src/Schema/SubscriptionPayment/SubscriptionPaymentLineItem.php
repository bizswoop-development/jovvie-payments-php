<?php

namespace JovviePayments\Schema\SubscriptionPayment;

use JovviePayments\Schema\Payment\LineItem;

class SubscriptionPaymentLineItem extends LineItem implements \JsonSerializable
{
	public ?string $quantity;
	public string $interval;

	public function __construct($data)
	{
		if (!isset($data->quantity)) {
			$data->quantity = null;
		} else {
			$data->quantity = $data->quantity;
		}
		$this->interval = $data->interval;
		parent::__construct($data);
	}

	public function jsonSerialize(): array
	{
		return array_merge(parent::jsonSerialize(), [
			'quantity' => $this->quantity,
		]);
	}
}
