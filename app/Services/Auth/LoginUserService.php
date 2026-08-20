<?php

namespace App\Services\Auth;

use App\Domain\Auth\InvalidCredentialsException;
use App\DTOs\Auth\LoginUserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginUserService
{
    public function handle(LoginUserDTO $dto): AuthenticationResult
    {
        $user = User::where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        $token = $user->createToken('api')->plainTextToken;

        return new AuthenticationResult($user, $token);
    }
}
