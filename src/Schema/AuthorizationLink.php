<?php

namespace JovviePayments\Schema;

class AuthorizationLink implements \JsonSerializable
{
	public bool $success;
	public string $authorizationLink;

	public function __construct($data)
	{
		$this->success = $data->success;
		$this->authorizationLink = $data->authorizationLink;
	}

	public function jsonSerialize(): array
	{
		return [
			'success' => $this->success,
			'authorizationLink' => $this->authorizationLink
		];
	}
}
