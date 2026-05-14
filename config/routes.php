<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\ApiAuthMiddleware;
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

    // User management (superadmin only)
    $router->get('/users', [UserController::class, 'index']);
    $router->get('/users/create', [UserController::class, 'create']);
    $router->post('/users', [UserController::class, 'store']);
    $router->get('/users/{id}/edit', [UserController::class, 'edit']);
    $router->put('/users/{id}', [UserController::class, 'update']);
    $router->delete('/users/{id}', [UserController::class, 'destroy']);

    $router->get('/activity-logs', [ActivityLogController::class, 'index']);
});

// API routes
$router->group(['prefix' => 'api/v1'], function ($router) {
    // Test route
    $router->get('/test', function() {
        return new \App\Core\Response(['success' => true, 'message' => 'API is working!']);
    });
    
    $router->post('/auth/token', [AuthApiController::class, 'token']);

    $router->group(['middleware' => [ApiAuthMiddleware::class]], function ($router) {
        $router->get('/medicines', [MedicineApiController::class, 'index']);
        $router->get('/medicines/{id}', [MedicineApiController::class, 'show']);
        $router->get('/medicines/{id}/stock', [MedicineApiController::class, 'stock']);
        $router->get('/alerts/expiring', [MedicineApiController::class, 'expiring']);
        $router->get('/alerts/low-stock', [MedicineApiController::class, 'lowStock']);
    });
});

// Public search API (requires auth)
$router->group(['middleware' => [AuthMiddleware::class]], function ($router) {
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
