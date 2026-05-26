<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\ApiToken;

class ApiAuthMiddleware
{
    private string $requiredScope;

    public function __construct(string $requiredScope = ApiToken::SCOPE_READ)
    {
        $this->requiredScope = $requiredScope;
    }

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

        $tokenScope = $user['scope'] ?? ApiToken::SCOPE_READ;

        if (!ApiToken::scopeAllows($tokenScope, $this->requiredScope)) {
            return (new Response())->json([
                'success' => false,
                'message' => "Insufficient scope. Required: {$this->requiredScope}",
            ], 403);
        }

        $request->user = $user;

        return null;
    }
}
