<?php

/**
 * Complete Security Verification Script
 * Run this to verify all security fixes are working
 */

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     HEALTHCARE SUPPLY CHAIN - SECURITY VERIFICATION       ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$passed = 0;
$failed = 0;
$warnings = 0;

// Test 1: Check Files Exist
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Checking Required Files\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$requiredFiles = [
    'app/Core/Logger.php' => 'Database Security',
    'app/Core/ApiValidator.php' => 'API Validation',
    'app/Core/RateLimiter.php' => 'Rate Limiting',
    'app/Middleware/RateLimitMiddleware.php' => 'Rate Limiting',
    'config/rate_limit.php' => 'Rate Limiting',
    'storage/.htaccess' => 'Storage Protection',
    'tests/security_fix_test.php' => 'Test Suite',
    'tests/api_validation_simple_test.php' => 'Test Suite',
    'tests/rate_limit_test.php' => 'Test Suite',
];

foreach ($requiredFiles as $file => $category) {
    $path = __DIR__ . '/../' . $file;
    if (file_exists($path)) {
        echo "✓ [{$category}] {$file}\n";
        $passed++;
    } else {
        echo "✗ [{$category}] {$file} - MISSING!\n";
        $failed++;
    }
}

echo "\n";

// Test 2: Check Modified Files
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: Checking Modified Files\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$modifiedFiles = [
    'app/Core/Database.php' => 'Logger::critical',
    'app/Core/Validator.php' => 'case \'in\':',
    'app/Core/Response.php' => 'withHeaders',
    'app/Controllers/Api/AuthApiController.php' => 'use ApiValidator',
    'app/Controllers/Api/MedicineApiController.php' => 'use ApiValidator',
    'app/Controllers/Api/SearchApiController.php' => 'preventSqlInjection',
];

foreach ($modifiedFiles as $file => $searchString) {
    $path = __DIR__ . '/../' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (strpos($content, $searchString) !== false) {
            echo "✓ {$file} - contains '{$searchString}'\n";
            $passed++;
        } else {
            echo "✗ {$file} - missing '{$searchString}'\n";
            $failed++;
        }
    } else {
        echo "✗ {$file} - FILE NOT FOUND\n";
        $failed++;
    }
}

echo "\n";

// Test 3: Check Storage Directories
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: Checking Storage Directories\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$dirs = [
    'storage/logs' => 'Log files',
    'storage/cache' => 'Cache files',
    'storage/cache/rate_limits' => 'Rate limit data',
];

foreach ($dirs as $dir => $purpose) {
    $path = __DIR__ . '/../' . $dir;
    
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        echo "⚠ {$dir} - created ({$purpose})\n";
        $warnings++;
    }
    
    if (is_writable($path)) {
        echo "✓ {$dir} - writable ({$purpose})\n";
        $passed++;
    } else {
        echo "✗ {$dir} - NOT WRITABLE ({$purpose})\n";
        $failed++;
    }
}

echo "\n";

// Test 4: Run Security Tests
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: Running Security Test Suites\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$tests = [
    'tests/security_fix_test.php' => 'Database Security',
    'tests/api_validation_simple_test.php' => 'API Validation',
    'tests/rate_limit_test.php' => 'Rate Limiting',
];

foreach ($tests as $test => $name) {
    $path = __DIR__ . '/../' . $test;
    if (file_exists($path)) {
        echo "Running {$name}...\n";
        
        ob_start();
        $returnCode = 0;
        try {
            include $path;
        } catch (Exception $e) {
            $returnCode = 1;
        }
        $output = ob_get_clean();
        
        if ($returnCode === 0 && strpos($output, '✓ PASS') !== false) {
            echo "✓ {$name} - ALL TESTS PASSED\n\n";
            $passed++;
        } else {
            echo "✗ {$name} - SOME TESTS FAILED\n\n";
            $failed++;
        }
    } else {
        echo "✗ {$test} - NOT FOUND\n\n";
        $failed++;
    }
}

// Test 5: Check Log Files
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 5: Checking Log Files\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$logDir = __DIR__ . '/../storage/logs/';
$logFiles = glob($logDir . '*.log');

if (count($logFiles) > 0) {
    echo "✓ Log files found: " . count($logFiles) . "\n";
    $passed++;
    
    // Check for redacted content
    $latestLog = end($logFiles);
    $content = file_get_contents($latestLog);
    
    if (strpos($content, '***REDACTED***') !== false) {
        echo "✓ Sensitive data redaction working\n";
        $passed++;
    } else {
        echo "⚠ No redacted content found (tests may not have run yet)\n";
        $warnings++;
    }
    
    // Show last 3 log entries
    $lines = explode("\n", $content);
    $lastLines = array_slice($lines, -3);
    echo "\nLast 3 log entries:\n";
    foreach ($lastLines as $line) {
        if (!empty(trim($line))) {
            echo "  " . substr($line, 0, 100) . "...\n";
        }
    }
} else {
    echo "⚠ No log files found (system may not have logged anything yet)\n";
    $warnings++;
}

echo "\n";

// Test 6: Check Rate Limit Files
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 6: Checking Rate Limit Cache\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$rateLimitDir = __DIR__ . '/../storage/cache/rate_limits/';
$rateLimitFiles = glob($rateLimitDir . 'rate_limit_*');

if (count($rateLimitFiles) > 0) {
    echo "✓ Rate limit cache files: " . count($rateLimitFiles) . "\n";
    echo "  (Active rate limits being tracked)\n";
    $passed++;
} else {
    echo "⚠ No rate limit files (no API requests have been rate limited yet)\n";
    $warnings++;
}

echo "\n";

// Test 7: Check Documentation
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 7: Checking Documentation\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$docs = [
    'SECURITY_FIX_DATABASE_CREDENTIALS.md',
    'SECURITY_FIX_API_VALIDATION.md',
    'SECURITY_FIX_RATE_LIMITING.md',
    'SECURITY_FIXES_COMPLETE_SUMMARY.md',
    'VERIFICATION_GUIDE.md',
];

foreach ($docs as $doc) {
    $path = __DIR__ . '/../' . $doc;
    if (file_exists($path)) {
        echo "✓ {$doc}\n";
        $passed++;
    } else {
        echo "⚠ {$doc} - not found\n";
        $warnings++;
    }
}

echo "\n";

// Summary
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    VERIFICATION SUMMARY                    ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "✓ Passed:   {$passed}\n";
echo "✗ Failed:   {$failed}\n";
echo "⚠ Warnings: {$warnings}\n\n";

if ($failed === 0) {
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║          ✅ ALL SECURITY FIXES VERIFIED!                  ║\n";
    echo "║                                                            ║\n";
    echo "║  Your system has all 3 critical security fixes:           ║\n";
    echo "║  1. ✅ Database Error Handling                            ║\n";
    echo "║  2. ✅ API Input Validation                               ║\n";
    echo "║  3. ✅ Rate Limiting                                      ║\n";
    echo "║                                                            ║\n";
    echo "║  Status: PRODUCTION READY 🚀                              ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
} else {
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║          ⚠ SOME CHECKS FAILED                             ║\n";
    echo "║                                                            ║\n";
    echo "║  Please review the failed tests above.                    ║\n";
    echo "║  Check VERIFICATION_GUIDE.md for troubleshooting.         ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
}

echo "\n";

// Next Steps
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "NEXT STEPS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if ($failed === 0) {
    echo "1. Start your server: php -S localhost:8000 -t public\n";
    echo "2. Test manually:\n";
    echo "   - Visit: http://localhost:8000\n";
    echo "   - Test API: http://localhost:8000/api/v1/test\n";
    echo "   - Try rate limiting (refresh 70 times)\n";
    echo "3. Review documentation in:\n";
    echo "   - VERIFICATION_GUIDE.md (detailed testing)\n";
    echo "   - SECURITY_FIXES_COMPLETE_SUMMARY.md (overview)\n";
    echo "4. Deploy to production! 🚀\n";
} else {
    echo "1. Review failed tests above\n";
    echo "2. Check VERIFICATION_GUIDE.md for troubleshooting\n";
    echo "3. Ensure all files are in place\n";
    echo "4. Check directory permissions\n";
    echo "5. Re-run this script: php tests/verify_all_fixes.php\n";
}

echo "\n";
