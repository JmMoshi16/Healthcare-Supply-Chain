<?php

return [
    'csrf_token_name'  => $_ENV['CSRF_TOKEN_NAME'] ?? 'csrf_token',
    'session_lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 7200),
    'bcrypt_cost'      => 12,
    'rate_limit'       => 5,   // max login attempts
    'rate_window'      => 300, // seconds
];
