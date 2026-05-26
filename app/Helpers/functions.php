<?php

use App\Core\Session;
use App\Core\View;

// URL helpers
function base_url(string $path = ''): string
{
    $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:8000';
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function redirect(string $url): void
{
    header("Location: " . base_url($url));
    exit;
}

function back(): void
{
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? base_url()));
    exit;
}

// View helpers
function view(string $view, array $data = []): string
{
    return View::render($view, $data);
}

// Session helpers
function session(string $key = null, $default = null)
{
    if ($key === null) {
        return $_SESSION;
    }
    return Session::get($key, $default);
}

function flash(string $key, $value = null)
{
    if ($value === null) {
        return Session::getFlash($key);
    }
    Session::flash($key, $value);
}

// Security helpers
function csrf_token(): string
{
    if (!Session::has('csrf_token')) {
        Session::set('csrf_token', bin2hex(random_bytes(32)));
    }
    return Session::get('csrf_token');
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(string $token): bool
{
    return hash_equals(csrf_token(), $token);
}

function esc(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function clean(string $string): string
{
    return trim(strip_tags($string));
}

// Auth helpers
function auth(): ?array
{
    return Session::get('user');
}

function is_logged_in(): bool
{
    return Session::has('user');
}

function has_role(string $role): bool
{
    $user = auth();
    return $user && $user['role'] === $role;
}

function can(string $permission): bool
{
    $user = auth();
    if (!$user) return false;
    
    $permissions = [
        'superadmin' => ['*'],
        'manager' => ['medicines.*', 'batches.*', 'stocks.*', 'categories.*'],
        'staff' => ['medicines.view', 'batches.view', 'stocks.view', 'categories.view']
    ];
    
    $userPermissions = $permissions[$user['role']] ?? [];
    
    return in_array('*', $userPermissions) || in_array($permission, $userPermissions);
}

// Date helpers
function format_date(string $date, string $format = 'M d, Y'): string
{
    return date($format, strtotime($date));
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

// String helpers
function str_limit(string $string, int $limit = 100): string
{
    if (strlen($string) <= $limit) {
        return $string;
    }
    return substr($string, 0, $limit) . '...';
}

// Array helpers
function old(string $key, $default = '')
{
    return Session::getFlash('old_input')[$key] ?? $default;
}

// File helpers
function upload_file($file, string $directory = 'uploads'): ?string
{
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }
    
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $uploadPath = __DIR__ . '/../../public/' . $directory . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $filename;
    }
    
    return null;
}

// Pagination helper
function paginate(int $total, int $perPage = 20, int $currentPage = 1): array
{
    $totalPages = ceil($total / $perPage);
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_more' => $currentPage < $totalPages
    ];
}

// Debug helper
function dd(...$vars): void
{
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}

// Password hashing
function hash_password(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verify_password(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

// Generate secure token
function generate_token(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

// API Token generation
function generate_api_token(): string
{
    return base64_encode(random_bytes(64));
}

// Security headers
function set_security_headers(): void
{
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
}
