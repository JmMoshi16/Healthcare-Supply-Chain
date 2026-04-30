<?php
$files = [
    'app/Views/auth/login.php',
    'app/Views/auth/register.php',
    'app/Views/users/index.php',
    'public/assets/css/premium.css'
];
foreach($files as $f) {
    $lines = file($f);
    foreach($lines as $i => $line) {
        if(strpos($line, '#1a1a1a') !== false) {
            echo "$f:" . ($i+1) . ": " . trim($line) . "\n";
        }
    }
}
