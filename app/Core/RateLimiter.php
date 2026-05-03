<?php

namespace App\Core;

class RateLimiter
{
    private string $storageDir;
    private int $cleanupProbability = 10; // 10% chance to cleanup old files
    
    public function __construct()
    {
        $this->storageDir = __DIR__ . '/../../storage/cache/rate_limits/';
        
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
    }
    
    /**
     * Check if request is allowed under rate limit
     * 
     * @param string $key Unique identifier (IP, user_id, token, etc.)
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $decaySeconds Time window in seconds
     * @return bool True if allowed, false if rate limited
     */
    public function attempt(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $cacheKey = $this->getCacheKey($key);
        $data = $this->getData($cacheKey);
        
        $now = time();
        
        // Reset if window expired
        if ($data && ($now - $data['timestamp']) >= $decaySeconds) {
            $data = null;
        }
        
        // Initialize or increment
        if (!$data) {
            $data = [
                'attempts' => 1,
                'timestamp' => $now,
                'reset_at' => $now + $decaySeconds,
            ];
        } else {
            $data['attempts']++;
        }
        
        // Save data
        $this->saveData($cacheKey, $data);
        
        // Cleanup old files occasionally
        if (rand(1, 100) <= $this->cleanupProbability) {
            $this->cleanup();
        }
        
        return $data['attempts'] <= $maxAttempts;
    }
    
    /**
     * Get remaining attempts
     */
    public function remaining(string $key, int $maxAttempts, int $decaySeconds): int
    {
        $data = $this->getData($this->getCacheKey($key));
        
        if (!$data) {
            return $maxAttempts;
        }
        
        $now = time();
        
        // Reset if window expired
        if (($now - $data['timestamp']) >= $decaySeconds) {
            return $maxAttempts;
        }
        
        $remaining = $maxAttempts - $data['attempts'];
        return max(0, $remaining);
    }
    
    /**
     * Get seconds until reset
     */
    public function availableIn(string $key): int
    {
        $data = $this->getData($this->getCacheKey($key));
        
        if (!$data) {
            return 0;
        }
        
        $resetAt = $data['reset_at'] ?? ($data['timestamp'] + 60);
        $availableIn = $resetAt - time();
        
        return max(0, $availableIn);
    }
    
    /**
     * Clear rate limit for a key
     */
    public function clear(string $key): void
    {
        $cacheKey = $this->getCacheKey($key);
        $file = $this->storageDir . $cacheKey;
        
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    /**
     * Check if key is currently rate limited
     */
    public function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        $data = $this->getData($this->getCacheKey($key));
        
        if (!$data) {
            return false;
        }
        
        return $data['attempts'] > $maxAttempts;
    }
    
    /**
     * Hit the rate limiter (increment without checking)
     */
    public function hit(string $key, int $decaySeconds = 60): int
    {
        $cacheKey = $this->getCacheKey($key);
        $data = $this->getData($cacheKey);
        
        $now = time();
        
        if (!$data || ($now - $data['timestamp']) >= $decaySeconds) {
            $data = [
                'attempts' => 1,
                'timestamp' => $now,
                'reset_at' => $now + $decaySeconds,
            ];
        } else {
            $data['attempts']++;
        }
        
        $this->saveData($cacheKey, $data);
        
        return $data['attempts'];
    }
    
    /**
     * Get attempts count
     */
    public function attempts(string $key): int
    {
        $data = $this->getData($this->getCacheKey($key));
        return $data['attempts'] ?? 0;
    }
    
    /**
     * Reset attempts for a key
     */
    public function resetAttempts(string $key): void
    {
        $this->clear($key);
    }
    
    private function getCacheKey(string $key): string
    {
        return 'rate_limit_' . md5($key);
    }
    
    private function getData(string $cacheKey): ?array
    {
        $file = $this->storageDir . $cacheKey;
        
        if (!file_exists($file)) {
            return null;
        }
        
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        
        return $data ?: null;
    }
    
    private function saveData(string $cacheKey, array $data): void
    {
        $file = $this->storageDir . $cacheKey;
        file_put_contents($file, json_encode($data), LOCK_EX);
    }
    
    private function cleanup(): void
    {
        $files = glob($this->storageDir . 'rate_limit_*');
        $now = time();
        
        foreach ($files as $file) {
            // Delete files older than 1 hour
            if (($now - filemtime($file)) > 3600) {
                unlink($file);
            }
        }
    }
    
    /**
     * Get rate limit info for headers
     */
    public function getRateLimitInfo(string $key, int $maxAttempts, int $decaySeconds): array
    {
        $data = $this->getData($this->getCacheKey($key));
        
        if (!$data) {
            return [
                'limit' => $maxAttempts,
                'remaining' => $maxAttempts,
                'reset' => time() + $decaySeconds,
            ];
        }
        
        $now = time();
        
        // Reset if window expired
        if (($now - $data['timestamp']) >= $decaySeconds) {
            return [
                'limit' => $maxAttempts,
                'remaining' => $maxAttempts,
                'reset' => $now + $decaySeconds,
            ];
        }
        
        return [
            'limit' => $maxAttempts,
            'remaining' => max(0, $maxAttempts - $data['attempts']),
            'reset' => $data['reset_at'] ?? ($data['timestamp'] + $decaySeconds),
        ];
    }
}
