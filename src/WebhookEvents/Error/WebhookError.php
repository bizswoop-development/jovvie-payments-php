<?php

namespace JovviePayments\WebhookEvents\Error;

class WebhookError extends \Exception
{
	public function __construct(string $message, $code = 0, \Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
