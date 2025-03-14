<?php

namespace JovviePayments\Schema\OneTimePayment;

use JovviePayments\Schema\Payment\Payment;

class OneTimePayment extends Payment implements \JsonSerializable
{
	/** @var OneTimePaymentLineItem[] */
	public array $lineItems;
	public OneTimeGeneratedData $generatedData;

	public function __construct($data)
	{
		parent::__construct($data);
		$this->lineItems = array_map(function ($item) {
			return new OneTimePaymentLineItem($item);
		}, $data->lineItems);
		$this->generatedData = new OneTimeGeneratedData($data->generatedData);
	}

	public function jsonSerialize(): array
	{
		return array_merge(parent::jsonSerialize(), [
			'lineItems' => array_map(function ($item) {
				return $item->jsonSerialize();
			}, $this->lineItems),
			'generatedData' => $this->generatedData->jsonSerialize(),
		]);
	}
}
