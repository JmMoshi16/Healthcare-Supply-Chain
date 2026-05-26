<?php

namespace App\Middleware;

use App\Core\Cache;
use App\Core\Request;
use App\Core\Response;

class ApiRateLimitMiddleware
{
    private int $maxRequests;
    private int $windowSeconds;

    public function __construct(int $maxRequests = 60, int $windowSeconds = 60)
    {
        $this->maxRequests   = $maxRequests;
        $this->windowSeconds = $windowSeconds;
    }

    public function handle(Request $request): ?Response
    {
        $identifier = $this->resolveIdentifier($request);
        $cacheKey   = 'rate_limit:' . $identifier;

        $data = Cache::get($cacheKey, ['count' => 0, 'reset_at' => time() + $this->windowSeconds]);

        if (time() > $data['reset_at']) {
            $data = ['count' => 0, 'reset_at' => time() + $this->windowSeconds];
        }

        $data['count']++;
        Cache::set($cacheKey, $data, $this->windowSeconds);

        if ($data['count'] > $this->maxRequests) {
            return (new Response())->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $data['reset_at'] - time(),
            ], 429);
        }

        return null;
    }

    private function resolveIdentifier(Request $request): string
    {
        // Use bearer token if present, otherwise fall back to IP
        $token = $request->bearerToken();
        if ($token) {
            return 'token:' . hash('sha256', $token);
        }

        return 'ip:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    }
}
