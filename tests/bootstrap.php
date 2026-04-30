<?php

require_once __DIR__ . '/../vendor/autoload.php';

load_env(__DIR__ . '/../.env');

if (!empty($_ENV['TEST_DB_NAME'])) {
    $_ENV['DB_NAME'] = $_ENV['TEST_DB_NAME'];
}
