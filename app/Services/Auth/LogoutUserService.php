<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LogoutUserService
{
    public function handle(User $user): void
    {
        Auth::guard('web')->logout();
    }
}
