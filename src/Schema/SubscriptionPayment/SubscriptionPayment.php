<?php

namespace JovviePayments\Schema\SubscriptionPayment;

use JovviePayments\Schema\Payment\Payment;

class SubscriptionPayment extends Payment implements \JsonSerializable
{
	/** @var SubscriptionPaymentLineItem[] */
	public array $lineItems;
	public SubscriptionGeneratedData $generatedData;

	public function __construct($data)
	{
		parent::__construct($data);
		$this->lineItems = array_map(function ($item) {
			return new SubscriptionPaymentLineItem($item);
		}, $data->lineItems);
		$this->generatedData = new SubscriptionGeneratedData($data->generatedData);
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
