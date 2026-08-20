<?php

namespace App\Services\Auth;

use App\DTOs\Auth\RegisterUserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUserService
{
    public function handle(RegisterUserDTO $dto): AuthenticationResult
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return new AuthenticationResult($user, $token);
    }
}
