<?php

namespace App\Core;

class Cache
{
    private static string $cacheDir = __DIR__ . '/../../storage/cache/';
    
    public static function get(string $key, $default = null)
    {
        $file = self::$cacheDir . md5($key) . '.cache';
        
        if (!file_exists($file)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($file));
        
        if ($data['expires_at'] < time()) {
            unlink($file);
            return $default;
        }
        
        return $data['value'];
    }
    
    public static function set(string $key, $value, int $ttl = 3600): void
    {
        $file = self::$cacheDir . md5($key) . '.cache';
        
        $data = [
            'value' => $value,
            'expires_at' => time() + $ttl
        ];
        
        file_put_contents($file, serialize($data));
    }
    
    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }
    
    public static function forget(string $key): void
    {
        $file = self::$cacheDir . md5($key) . '.cache';
        
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    public static function flush(): void
    {
        $files = glob(self::$cacheDir . '*.cache');
        
        foreach ($files as $file) {
            unlink($file);
        }
    }
}
