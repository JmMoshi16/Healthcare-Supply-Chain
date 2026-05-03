<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\RateLimiter;
use App\Core\Logger;

class RateLimitMiddleware
{
    private RateLimiter $limiter;
    private array $config;
    
    public function __construct(array $config = [])
    {
        $this->limiter = new RateLimiter();
        
        // Default configuration
        $this->config = array_merge([
            'max_attempts' => 60,        // 60 requests
            'decay_seconds' => 60,       // per minute
            'identifier' => 'ip',        // 'ip', 'user', 'token', or custom
            'prefix' => 'api',           // Rate limit key prefix
        ], $config);
    }
    
    public function handle(Request $request): ?Response
    {
        // Get identifier for rate limiting
        $identifier = $this->getIdentifier($request);
        $key = $this->config['prefix'] . ':' . $identifier;
        
        // Check rate limit
        $maxAttempts = $this->config['max_attempts'];
        $decaySeconds = $this->config['decay_seconds'];
        
        // Get rate limit info
        $info = $this->limiter->getRateLimitInfo($key, $maxAttempts, $decaySeconds);
        
        // Check if too many attempts
        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $this->limiter->availableIn($key);
            
            // Log rate limit exceeded
            Logger::warning('Rate limit exceeded', [
                'identifier' => $identifier,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'path' => $request->path(),
                'method' => $request->method(),
                'retry_after' => $retryAfter,
            ]);
            
            return $this->buildRateLimitResponse($info, $retryAfter);
        }
        
        // Hit the rate limiter
        $this->limiter->hit($key, $decaySeconds);
        
        // Update info after hit
        $info = $this->limiter->getRateLimitInfo($key, $maxAttempts, $decaySeconds);
        
        // Add rate limit headers to request for controller to use
        $request->rateLimitInfo = $info;
        
        return null; // Allow request to proceed
    }
    
    private function getIdentifier(Request $request): string
    {
        switch ($this->config['identifier']) {
            case 'ip':
                return $this->getClientIp($request);
                
            case 'user':
                return $request->user['id'] ?? $this->getClientIp($request);
                
            case 'token':
                $token = $request->bearerToken();
                return $token ? md5($token) : $this->getClientIp($request);
                
            default:
                // Custom identifier
                if (is_callable($this->config['identifier'])) {
                    return call_user_func($this->config['identifier'], $request);
                }
                return $this->getClientIp($request);
        }
    }
    
    private function getClientIp(Request $request): string
    {
        // Check for proxy headers
        $headers = [
            'HTTP_CF_CONNECTING_IP',    // Cloudflare
            'HTTP_X_FORWARDED_FOR',     // Standard proxy header
            'HTTP_X_REAL_IP',           // Nginx proxy
            'HTTP_CLIENT_IP',           // Some proxies
            'REMOTE_ADDR',              // Direct connection
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                
                // Handle comma-separated IPs (X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }
                
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
    
    private function buildRateLimitResponse(array $info, int $retryAfter): Response
    {
        $response = new Response();
        
        return $response->json([
            'success' => false,
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => $retryAfter,
            'retry_after_human' => $this->formatRetryAfter($retryAfter),
        ], 429)
        ->withHeaders([
            'X-RateLimit-Limit' => (string)$info['limit'],
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset' => (string)$info['reset'],
            'Retry-After' => (string)$retryAfter,
        ]);
    }
    
    private function formatRetryAfter(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }
        
        $minutes = ceil($seconds / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    }
    
    /**
     * Add rate limit headers to response
     */
    public static function addRateLimitHeaders(Response $response, array $info): Response
    {
        return $response->withHeaders([
            'X-RateLimit-Limit' => (string)$info['limit'],
            'X-RateLimit-Remaining' => (string)$info['remaining'],
            'X-RateLimit-Reset' => (string)$info['reset'],
        ]);
    }
}
