<?php

namespace JovviePayments\Http\Error;

use JovviePayments\Http\Response;

class BadRequestError extends ResponseError
{
	public function __construct(Response $response, $message, \Exception $previous = null)
	{
		return parent::__construct($response, $message, 400, $previous);
	}
}
