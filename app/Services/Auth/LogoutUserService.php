<?php

namespace App\Services\Auth;

use App\Models\User;

class LogoutUserService
{
    public function handle(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
