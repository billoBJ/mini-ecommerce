<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\LogoutUserService;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request, LogoutUserService $logoutUser)
    {
        $logoutUser->handle($request->user());

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => __('messages.success.logged_out'),
        ]);
    }
}
