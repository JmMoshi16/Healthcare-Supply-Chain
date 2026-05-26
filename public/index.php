<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Session;

// Load environment
load_env(__DIR__ . '/../.env');

// Start session
Session::start();

// Set security headers
set_security_headers();

// Silently update expired batches on page load
try {
    \App\Models\Batch::updateExpiredStatuses();
} catch (\Throwable $e) {
    // Fail silently in case database is not migrated/connected yet
}

// Create router and load routes
$router = new Router();
require_once __DIR__ . '/../config/routes.php';

// Dispatch
$request  = new Request();
$response = $router->dispatch($request);
$response->send();
