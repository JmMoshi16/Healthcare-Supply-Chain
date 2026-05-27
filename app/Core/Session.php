<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Use project-local session storage so InfinityFree's shared /tmp
            // doesn't wipe sessions between requests
            $savePath = dirname(__DIR__, 2) . '/storage/sessions';
            if (is_dir($savePath) && is_writable($savePath)) {
                session_save_path($savePath);
            }

            // Explicit cookie params — InfinityFree is HTTP-only on free tier
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            session_start();
        }
    }
    
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
    
    public static function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }
    
    public static function getFlash(string $key, $default = null)
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
    
    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }
    
    public static function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
    }
    
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}
