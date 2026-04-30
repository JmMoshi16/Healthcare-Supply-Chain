<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\ApiToken;

class ApiAuthMiddleware
{
    public function handle(Request $request): ?Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return (new Response())->json([
                'success' => false,
                'message' => 'Authorization token required',
            ], 401);
        }

        $user = (new ApiToken())->verify($token);

        if (!$user) {
            return (new Response())->json([
                'success' => false,
                'message' => 'Invalid or expired token',
            ], 401);
        }

        $request->user = $user;

        return null;
    }
}
