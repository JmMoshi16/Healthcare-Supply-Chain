<?php

/**
 * Rate Limiting Configuration
 * 
 * Define rate limits for different API endpoints and user types
 */

return [
    // Global API rate limit (applies to all API routes unless overridden)
    'global' => [
        'max_attempts' => 60,        // 60 requests
        'decay_seconds' => 60,       // per minute
        'identifier' => 'ip',        // Rate limit by IP address
    ],
    
    // Authentication endpoints (stricter limits to prevent brute force)
    'auth' => [
        'max_attempts' => 5,         // 5 attempts
        'decay_seconds' => 60,       // per minute
        'identifier' => 'ip',        // By IP to prevent distributed attacks
    ],
    
    // Search endpoints (moderate limits)
    'search' => [
        'max_attempts' => 30,        // 30 requests
        'decay_seconds' => 60,       // per minute
        'identifier' => 'ip',
    ],
    
    // Read operations (higher limits)
    'read' => [
        'max_attempts' => 100,       // 100 requests
        'decay_seconds' => 60,       // per minute
        'identifier' => 'token',     // By API token
    ],
    
    // Write operations (moderate limits)
    'write' => [
        'max_attempts' => 30,        // 30 requests
        'decay_seconds' => 60,       // per minute
        'identifier' => 'token',
    ],
    
    // Authenticated users (higher limits)
    'authenticated' => [
        'max_attempts' => 120,       // 120 requests
        'decay_seconds' => 60,       // per minute
        'identifier' => 'user',      // By user ID
    ],
    
    // Guest/unauthenticated (lower limits)
    'guest' => [
        'max_attempts' => 20,        // 20 requests
        'decay_seconds' => 60,       // per minute
        'identifier' => 'ip',
    ],
    
    // Specific endpoint overrides
    'endpoints' => [
        // Very strict for token generation
        'POST:/api/v1/auth/token' => [
            'max_attempts' => 5,
            'decay_seconds' => 60,
            'identifier' => 'ip',
        ],
        
        // Moderate for search
        'GET:/api/search' => [
            'max_attempts' => 30,
            'decay_seconds' => 60,
            'identifier' => 'ip',
        ],
        
        // Higher for medicine list
        'GET:/api/v1/medicines' => [
            'max_attempts' => 100,
            'decay_seconds' => 60,
            'identifier' => 'token',
        ],
    ],
    
    // Response headers
    'headers' => [
        'enabled' => true,           // Add X-RateLimit-* headers
        'prefix' => 'X-RateLimit-',  // Header prefix
    ],
    
    // Logging
    'log_exceeded' => true,          // Log when rate limit is exceeded
    
    // Cleanup
    'cleanup_probability' => 10,     // 10% chance to cleanup old rate limit files
    'cleanup_after' => 3600,         // Delete files older than 1 hour
];
