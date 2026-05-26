<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Logger;

class ApiScopeMiddleware
{
    private array $requiredScopes;
    
    public function __construct(array $requiredScopes = [])
    {
        $this->requiredScopes = $requiredScopes;
    }
    
    public function handle(Request $request): ?Response
    {
        // Get user from request (set by ApiAuthMiddleware)
        $user = $request->user ?? null;
        
        if (!$user) {
            return (new Response())->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }
        
        // Get token scopes
        $tokenScopes = $user['token_scopes'] ?? [];
        
        // Check if token has required scopes
        if (!$this->hasRequiredScopes($tokenScopes, $this->requiredScopes)) {
            Logger::warning('API scope denied', [
                'user_id' => $user['id'],
                'required_scopes' => $this->requiredScopes,
                'token_scopes' => $tokenScopes,
                'path' => $request->path(),
            ]);
            
            return (new Response())->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'required_scopes' => $this->requiredScopes,
            ], 403);
        }
        
        return null; // Allow request to proceed
    }
    
    private function hasRequiredScopes(array $tokenScopes, array $requiredScopes): bool
    {
        // If no scopes required, allow
        if (empty($requiredScopes)) {
            return true;
        }
        
        // Check for wildcard scope
        if (in_array('*', $tokenScopes)) {
            return true;
        }
        
        // Check if token has all required scopes
        foreach ($requiredScopes as $required) {
            if (!$this->hasScope($tokenScopes, $required)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function hasScope(array $tokenScopes, string $required): bool
    {
        // Direct match
        if (in_array($required, $tokenScopes)) {
            return true;
        }
        
        // Check for wildcard patterns
        // e.g., "medicines:*" matches "medicines:read" and "medicines:write"
        foreach ($tokenScopes as $scope) {
            if ($this->scopeMatches($scope, $required)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function scopeMatches(string $scope, string $required): bool
    {
        // Exact match
        if ($scope === $required) {
            return true;
        }
        
        // Wildcard match
        if (strpos($scope, '*') !== false) {
            $pattern = str_replace('*', '.*', preg_quote($scope, '/'));
            return preg_match('/^' . $pattern . '$/', $required) === 1;
        }
        
        return false;
    }
}
