<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class StatusTransitionNotAllowedException extends Exception
{
    protected $message = 'A transição de status não é permitida.';

    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}
