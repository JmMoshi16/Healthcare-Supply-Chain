<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Healthcare Supply Chain',
    'env'  => $_ENV['APP_ENV'] ?? 'development',
    'url'  => $_ENV['APP_URL'] ?? 'http://localhost:8000',
    'key'  => $_ENV['APP_KEY'] ?? '',
];
