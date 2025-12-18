<?php

namespace JovviePayments\Http\Error;

use JovviePayments\Http\Response;

class NotFoundError extends ResponseError
{
	public function __construct(Response $response, $message, \Exception $previous = null)
	{
		return parent::__construct($response, $message, 404, $previous);
	}
}
