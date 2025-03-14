<?php

namespace JovviePayments\Schema\OneTimePayment;

use JovviePayments\Schema\Payment\GeneratedData;

class OneTimeGeneratedData extends GeneratedData implements \JsonSerializable
{
	public string $checkoutLink;

	public function __construct($data)
	{
		$this->checkoutLink = $data->checkoutLink;
		parent::__construct($data);
	}

	public function jsonSerialize(): array
	{
		return array_merge(parent::jsonSerialize(), [
			'checkoutLink' => $this->checkoutLink,
		]);
	}
}
