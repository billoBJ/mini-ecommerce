<?php

namespace App\Domain\Auth;

class InvalidCredentialsException extends \DomainException
{
    public function __construct()
    {
        parent::__construct(__('messages.errors.invalid_credentials'));
    }
}
