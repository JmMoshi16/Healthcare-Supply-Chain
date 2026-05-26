<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\ApiAuthMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\MedicineController;
use App\Controllers\BatchController;
use App\Controllers\StockController;
use App\Controllers\UserController;
use App\Controllers\ActivityLogController;
use App\Controllers\Api\AuthApiController;
use App\Controllers\Api\SearchApiController;
use App\Controllers\Api\MedicineApiController;
use App\Controllers\NotificationController;
use App\Controllers\CalendarController;
use App\Controllers\ExpiryCalendarController;
use App\Controllers\ReorderController;
use App\Controllers\EmailController;

// Public routes
$router->get('/', [AuthController::class, 'loginForm']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// Protected web routes
$router->group(['middleware' => [AuthMiddleware::class, CsrfMiddleware::class]], function ($router) {
    $router->get('/dashboard', [DashboardController::class, 'index']);

    $router->resource('medicines', MedicineController::class);
    $router->resource('batches', BatchController::class);
    $router->resource('stocks', StockController::class);
    $router->get('/reorder', [ReorderController::class, 'index']);

    // User management (superadmin only)
    $router->get('/users', [UserController::class, 'index']);
    $router->get('/users/create', [UserController::class, 'create']);
    $router->post('/users', [UserController::class, 'store']);
    $router->get('/users/{id}/edit', [UserController::class, 'edit']);
    $router->put('/users/{id}', [UserController::class, 'update']);
    $router->delete('/users/{id}', [UserController::class, 'destroy']);

    $router->get('/activity-logs', [ActivityLogController::class, 'index']);

    // Email management (superadmin only)
    $router->get('/emails', [EmailController::class, 'index']);
    $router->post('/emails/test', [EmailController::class, 'test']);
    $router->post('/emails/send-expiry-alerts', [EmailController::class, 'sendExpiryAlerts']);
    $router->post('/emails/send-low-stock-alerts', [EmailController::class, 'sendLowStockAlerts']);
    $router->post('/emails/send-daily-report', [EmailController::class, 'sendDailyReport']);
    $router->post('/emails/send-weekly-report', [EmailController::class, 'sendWeeklyReport']);
});

// API routes with rate limiting
$router->group(['prefix' => 'api/v1'], function ($router) {
    // Test route (global rate limit)
    $router->get('/test', function() {
        return new \App\Core\Response(['success' => true, 'message' => 'API is working!']);
    });
    
    // Auth endpoint with strict rate limit (5 per minute)
    $authRateLimit = new RateLimitMiddleware([
        'max_attempts' => 5,
        'decay_seconds' => 60,
        'identifier' => 'ip',
        'prefix' => 'api_auth',
    ]);
    $router->post('/auth/token', [AuthApiController::class, 'token'], ['middleware' => [$authRateLimit]]);

    // Protected API routes with moderate rate limit (60 per minute)
    $apiRateLimit = new RateLimitMiddleware([
        'max_attempts' => 60,
        'decay_seconds' => 60,
        'identifier' => 'token',
        'prefix' => 'api_general',
    ]);
    
    $router->group(['middleware' => [ApiAuthMiddleware::class, $apiRateLimit]], function ($router) {
        $router->get('/medicines', [MedicineApiController::class, 'index']);
        $router->get('/medicines/{id}', [MedicineApiController::class, 'show']);
        $router->get('/medicines/{id}/stock', [MedicineApiController::class, 'stock']);
        $router->get('/alerts/expiring', [MedicineApiController::class, 'expiring']);
        $router->get('/alerts/low-stock', [MedicineApiController::class, 'lowStock']);
    });
});

// Public search API with rate limiting (30 per minute)
$searchRateLimit = new RateLimitMiddleware([
    'max_attempts' => 30,
    'decay_seconds' => 60,
    'identifier' => 'ip',
    'prefix' => 'api_search',
]);

$router->group(['middleware' => [AuthMiddleware::class, $searchRateLimit]], function ($router) {
    $router->get('/api/search', [SearchApiController::class, 'search']);
    $router->post('/api/medicines/{id}/toggle-status', [MedicineApiController::class, 'toggleStatus']);
    
    // Notifications
    $router->get('/api/notifications', [NotificationController::class, 'index']);
    $router->post('/api/notifications/mark-read', [NotificationController::class, 'markAsRead']);
    $router->post('/api/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    
    // Calendar
    $router->get('/api/calendar/notes', [CalendarController::class, 'getNotes']);
    $router->post('/api/calendar/notes', [CalendarController::class, 'store']);
    $router->put('/api/calendar/notes', [CalendarController::class, 'update']);
    $router->delete('/api/calendar/notes', [CalendarController::class, 'delete']);
    
    // Expiry Calendar
    $router->get('/api/calendar/expiring', [ExpiryCalendarController::class, 'getExpiringByMonth']);
});
