<?php

namespace App\Core;

class Logger
{
    private static string $logPath = __DIR__ . '/../../storage/logs/';

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        self::log('CRITICAL', $message, $context);
    }

    private static function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logFile = self::$logPath . date('Y-m-d') . '.log';
        
        // Sanitize context to remove sensitive data
        $sanitizedContext = self::sanitizeContext($context);
        
        $logEntry = sprintf(
            "[%s] [%s] %s %s\n",
            $timestamp,
            $level,
            $message,
            !empty($sanitizedContext) ? json_encode($sanitizedContext) : ''
        );

        // Ensure log directory exists
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }

        // Write to log file
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    private static function sanitizeContext(array $context): array
    {
        $sensitiveKeys = ['password', 'token', 'secret', 'key', 'credential', 'auth'];
        
        foreach ($context as $key => $value) {
            foreach ($sensitiveKeys as $sensitive) {
                if (stripos($key, $sensitive) !== false) {
                    $context[$key] = '***REDACTED***';
                }
            }
            
            if (is_array($value)) {
                $context[$key] = self::sanitizeContext($value);
            }
        }
        
        return $context;
    }

    public static function logException(\Throwable $e, string $context = ''): void
    {
        $message = sprintf(
            '%s: %s in %s:%d',
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        self::error($message, [
            'context' => $context,
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
