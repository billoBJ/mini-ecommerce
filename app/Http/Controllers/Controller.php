<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class Controller
{
    use AuthorizesRequests;

    protected function withMessage(JsonResource $resource, string $key): JsonResource
    {
        return $resource->additional(['message' => __($key)]);
    }
}
