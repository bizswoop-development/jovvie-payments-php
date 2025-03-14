<?php

namespace JovviePayments\Http;

class Response
{
	protected mixed $body;
	protected int $statusCode;
	protected array $headers;

	public function __construct(mixed $body, int $statusCode, array $headers)
	{
		$this->body = $body;
		$this->statusCode = $statusCode;
		$this->headers = $headers;
	}

	public function getStatusCode(): int
	{
		return $this->statusCode;
	}

	public function getHeaders(): array
	{
		return $this->headers;
	}

	public function getBody(): mixed
	{
		return $this->body;
	}
}
