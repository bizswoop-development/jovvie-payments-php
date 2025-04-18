<?php

namespace JovviePayments\Http;

class Response
{
	protected $body;
	protected int $statusCode;
	protected array $headers;

	public function __construct($body, int $statusCode, array $headers)
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

	public function getBody()
	{
		return $this->body;
	}
}
