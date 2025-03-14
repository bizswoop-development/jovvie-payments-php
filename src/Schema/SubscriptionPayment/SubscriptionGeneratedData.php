<?php

namespace JovviePayments\Schema\SubscriptionPayment;

use JovviePayments\Schema\Payment\GeneratedData;

class SubscriptionGeneratedData extends GeneratedData implements \JsonSerializable
{
	public string $checkoutLink;
	public string $subscriptionId;
	public string $setupIntentId;
	/** @var GeneratedSubscriptionItem[] */
	public array $subscriptionItems;

	public function __construct($data)
	{
		parent::__construct($data);

		$this->checkoutLink = $data->checkoutLink;

		$this->subscriptionId = $data->subscriptionId;
		$this->setupIntentId = $data->setupIntentId;

		$this->subscriptionItems = array_map(function ($item) {
			return new GeneratedSubscriptionItem($item);
		}, $data->subscriptionItems);
	}

	public function jsonSerialize(): array
	{
		return array_merge(parent::jsonSerialize(), [
			'checkoutLink' => $this->checkoutLink,
			'subscriptionId' => $this->subscriptionId,
			'setupIntentId' => $this->setupIntentId,
		]);
	}
}
