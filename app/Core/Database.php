<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;
    
    public static function connect(): PDO
    {
        if (self::$connection === null) {
            try {
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $port = $_ENV['DB_PORT'] ?? '3306';
                $dbname = $_ENV['DB_NAME'] ?? 'healthcare_supply';
                $username = $_ENV['DB_USER'] ?? 'root';
                $password = $_ENV['DB_PASS'] ?? '';
                
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_TIMEOUT => 10,
                ]);
            } catch (PDOException $e) {
                // Log the full error details securely
                Logger::critical('Database connection failed', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'host' => $host ?? 'unknown',
                    'database' => $dbname ?? 'unknown',
                ]);
                
                // Show generic error to user based on environment
                $isProduction = ($_ENV['APP_ENV'] ?? 'production') === 'production';
                
                if ($isProduction) {
                    // Production: Generic error message
                    self::showErrorPage(
                        'Service Unavailable',
                        'We are experiencing technical difficulties. Please try again later.',
                        503
                    );
                } else {
                    // Development: More details but still no credentials
                    self::showErrorPage(
                        'Database Connection Failed',
                        'Unable to connect to the database. Check logs for details.',
                        500
                    );
                }
                
                exit(1);
            }
        }
        
        return self::$connection;
    }
    
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = self::connect()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Log the error securely
            Logger::error('Database query failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'sql' => $sql,
            ]);
            
            // Re-throw with generic message
            throw new \Exception('A database error occurred. Please try again.');
        }
    }
    
    public static function lastInsertId(): string
    {
        return self::connect()->lastInsertId();
    }
    
    private static function showErrorPage(string $title, string $message, int $httpCode = 500): void
    {
        http_response_code($httpCode);
        
        // Check if we're in CLI mode
        if (php_sapi_name() === 'cli') {
            echo "ERROR: {$title}\n{$message}\n";
            return;
        }
        
        // HTML error page
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 500px;
            text-align: center;
        }
        .error-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        h1 {
            color: #2d3748;
            font-size: 24px;
            margin-bottom: 16px;
        }
        p {
            color: #718096;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .error-code {
            display: inline-block;
            background: #f7fafc;
            color: #4a5568;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5a67d8;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1>' . htmlspecialchars($title) . '</h1>
        <p>' . htmlspecialchars($message) . '</p>
        <div class="error-code">Error Code: ' . $httpCode . '</div>
        <a href="/" class="btn">Return to Home</a>
    </div>
</body>
</html>';
    }
}
