<?php

namespace JovviePayments\Http\Error;

use JovviePayments\Http\Response;

class ResponseError extends \Exception
{
	protected Response $response;

	public function __construct(Response $response, $message, $code = 0, \Exception $previous = null)
	{
		$this->response = $response;
		if ($code === 0) {
			$code = $this->response->getStatusCode();
		}
		parent::__construct($message, $code, $previous);
	}

	public function getResponse(): Response
	{
		return $this->response;
	}
}
