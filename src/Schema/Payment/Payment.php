<?php

namespace JovviePayments\Schema\Payment;


abstract class Payment implements \JsonSerializable
{
	public string $id;
	public string $type;
	public string $mode;
	public string $description;
	public string $lineTotal;
	public int $amount;
	public string $currency;
	public int $feeAmount;
	public string $status;
	public string $createdAt;
	public string $updatedAt;
	public string $platformProvider;

	public function __construct($data)
	{
		$this->id = $data->id;
		$this->mode = $data->mode;
		$this->type = $data->type;
		$this->description = $data->description;
		$this->lineTotal = $data->lineTotal;
		$this->amount = $data->amount;
		$this->currency = $data->currency;
		$this->feeAmount = $data->feeAmount;
		$this->status = $data->status;
		$this->createdAt = $data->createdAt;
		$this->updatedAt = $data->updatedAt;
		$this->platformProvider = $data->platformProvider;
	}

	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'type' => $this->type,
			'mode' => $this->mode,
			'description' => $this->description,
			'lineTotal' => $this->lineTotal,
			'amount' => $this->amount,
			'currency' => $this->currency,
			'feeAmount' => $this->feeAmount,
			'status' => $this->status,
			'createdAt' => $this->createdAt,
			'updatedAt' => $this->updatedAt,
			'platformProvider' => $this->platformProvider,
		];
	}
}
