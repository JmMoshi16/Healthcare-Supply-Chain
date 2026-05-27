<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Session;
use App\Core\Response;

// Load environment FIRST — everything depends on it
load_env(__DIR__ . '/../.env');

// Suppress PHP notices/warnings from corrupting HTML output on InfinityFree
// (errors still go to error_log)
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Start session
Session::start();

// Set security headers
set_security_headers();

try {
    // Create router and load routes
    $router = new Router();
    require_once __DIR__ . '/../config/routes.php';

    // Dispatch
    $request  = new Request();
    $response = $router->dispatch($request);
    $response->send();

} catch (\Throwable $e) {
    // Log the error
    error_log('[HealthChain] Uncaught: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    // Redirect to login gracefully — never show InfinityFree's 503 page
    $baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
    if (!headers_sent()) {
        http_response_code(302);
        header('Location: ' . $baseUrl . '/login');
        exit;
    }
    // If headers already sent (partial output), show a minimal message
    echo '<script>window.location="' . htmlspecialchars($baseUrl . '/login') . '"</script>';
    exit;
}
