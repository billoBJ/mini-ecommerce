<?php

namespace App\Services\Auth;

use App\Models\User;

class AuthenticationResult
{
    public function __construct(
        public readonly User $user,
    ) {}
}
