<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTOs\Auth\RegisterUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Services\Auth\RegisterUserService;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request, RegisterUserService $registerUser)
    {
        $dto = new RegisterUserDTO(...$request->validated());

        $result = $registerUser->handle($dto);

        return $this->withMessage(new AuthResource($result), 'messages.success.registered')
            ->response()
            ->setStatusCode(201);
    }
}
