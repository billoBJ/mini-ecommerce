<?php

namespace App\Http\Resources;

use App\Services\Auth\AuthenticationResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AuthenticationResult */
class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->user),
        ];
    }
}
