<?php

namespace App\Domain\Auth;

class InvalidCredentialsException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('The provided credentials are incorrect.');
    }
}
