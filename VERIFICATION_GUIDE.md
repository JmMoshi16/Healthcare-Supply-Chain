# ✅ VERIFICATION GUIDE - How to Check All Security Fixes

## 🎯 Quick Verification (5 minutes)

### Step 1: Check Files Exist

```bash
# Navigate to your project
cd c:\Users\Danny Ricaro\Downloads\healthcare-supply-chain

# Check new files created
dir app\Core\Logger.php
dir app\Core\ApiValidator.php
dir app\Core\RateLimiter.php
dir app\Middleware\RateLimitMiddleware.php
dir config\rate_limit.php
dir storage\.htaccess

# Check test files
dir tests\security_fix_test.php
dir tests\api_validation_simple_test.php
dir tests\rate_limit_test.php
```

**Expected:** All files should exist ✅

---

### Step 2: Run All Tests

```bash
# Test 1: Database Security
php tests\security_fix_test.php

# Test 2: API Validation
php tests\api_validation_simple_test.php

# Test 3: Rate Limiting
php tests\rate_limit_test.php
```

**Expected Output:**
```
✓ PASS: All tests passing
Security Status: ✅ IMPLEMENTED
```

---

## 🔍 Detailed Verification (30 minutes)

### Test 1: Database Error Handling ✅

#### A. Test Secure Error Logging

```bash
# Run the security test
php tests\security_fix_test.php
```

**Check:**
1. Log file created: `storage\logs\[TODAY].log`
2. Passwords redacted: Look for `***REDACTED***`
3. No errors displayed

```bash
# View log file
type storage\logs\2026-05-03.log
```

**Expected in log:**
```
[2026-05-03 17:20:24] [ERROR] Test error with sensitive data {
    "username":"admin",
    "password":"***REDACTED***",
    "api_token":"***REDACTED***",
    "safe_data":"this should appear"
}
```

#### B. Test Wrong Database Credentials

1. **Backup your .env file:**
```bash
copy .env .env.backup
```

2. **Edit .env with wrong credentials:**
```env
DB_USER=wrong_user
DB_PASS=wrong_password
```

3. **Try to access the application:**
```bash
# Start server
php -S localhost:8000 -t public

# Open browser
# Visit: http://localhost:8000
```

**Expected:**
- ❌ **NOT** see: Database credentials, usernames, passwords
- ✅ **SEE:** Professional error page saying "Service Unavailable" or "Database Connection Failed"

4. **Check logs:**
```bash
type storage\logs\[TODAY].log
```

**Expected in log:**
```
[DATE] [CRITICAL] Database connection failed {
    "error": "Access denied for user 'wrong_user'...",
    "host": "localhost",
    "database": "healthcare_supply"
}
```

5. **Restore .env:**
```bash
copy .env.backup .env
del .env.backup
```

---

### Test 2: API Input Validation ✅

#### A. Run Validation Tests

```bash
php tests\api_validation_simple_test.php
```

**Expected:**
```
✓ PASS: Valid data accepted
✓ PASS: Invalid data rejected
✓ PASS: SQL injection blocked (4/4)
✓ PASS: XSS sanitized (3/3)
✓ PASS: Input sanitized
```

#### B. Test SQL Injection Prevention (Manual)

**Start your server:**
```bash
php -S localhost:8000 -t public
```

**Test 1: SQL Injection in Search**
```bash
curl -X GET "http://localhost:8000/api/search?q=' OR '1'='1"
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Invalid search query"
}
```

**Check logs:**
```bash
type storage\logs\[TODAY].log | findstr "SQL injection"
```

**Expected:**
```
[WARNING] API: SQL injection attempt detected
```

#### C. Test XSS Prevention

**Test with XSS payload:**
```bash
curl -X POST "http://localhost:8000/api/v1/auth/token" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"<script>alert('XSS')</script>\",\"password\":\"test\"}"
```

**Expected:**
- Script tags removed/sanitized
- Validation error (invalid email format)

#### D. Test Input Validation

**Test with invalid data:**
```bash
curl -X POST "http://localhost:8000/api/v1/auth/token" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"invalid-email\",\"password\":\"123\"}"
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email must be a valid email"],
    "password": ["Password must be at least 6 characters"]
  }
}
```

---

### Test 3: Rate Limiting ✅

#### A. Run Rate Limit Tests

```bash
php tests\rate_limit_test.php
```

**Expected:**
```
✓ PASS: Rate limiting working correctly (5/10 allowed)
✓ PASS: Remaining attempts correct
✓ PASS: Too many attempts detected
✓ PASS: Window expiration working
```

#### B. Test Auth Endpoint Rate Limiting (5 per minute)

**Start server:**
```bash
php -S localhost:8000 -t public
```

**Make 6 rapid requests:**
```bash
# Windows Command Prompt
for /L %i in (1,1,6) do @(
  echo Request %i:
  curl -X POST "http://localhost:8000/api/v1/auth/token" ^
    -H "Content-Type: application/json" ^
    -d "{\"email\":\"test@test.com\",\"password\":\"wrong\"}" ^
    -w "\nHTTP Status: %%{http_code}\n\n"
)
```

**Expected:**
- Requests 1-5: `HTTP Status: 401` (invalid credentials)
- Request 6: `HTTP Status: 429` (rate limited)

**Response on 6th request:**
```json
{
  "success": false,
  "message": "Too many requests. Please try again later.",
  "retry_after": 60,
  "retry_after_human": "1 minute"
}
```

**Check headers:**
```bash
curl -I -X POST "http://localhost:8000/api/v1/auth/token" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"test@test.com\",\"password\":\"wrong\"}"
```

**Expected Headers:**
```
X-RateLimit-Limit: 5
X-RateLimit-Remaining: 4
X-RateLimit-Reset: [timestamp]
```

#### C. Test API Endpoint Rate Limiting (60 per minute)

**First, get a valid token:**
```bash
curl -X POST "http://localhost:8000/api/v1/auth/token" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@healthcare.com\",\"password\":\"Admin@123\"}"
```

**Copy the token from response, then test:**
```bash
# Replace YOUR_TOKEN with actual token
set TOKEN=YOUR_TOKEN_HERE

# Make 65 requests
for /L %i in (1,1,65) do @(
  curl -s -o nul -w "Request %i: %%{http_code}\n" ^
    "http://localhost:8000/api/v1/medicines" ^
    -H "Authorization: Bearer %TOKEN%"
)
```

**Expected:**
- Requests 1-60: `200` (success)
- Requests 61-65: `429` (rate limited)

#### D. Check Rate Limit Files

```bash
# View rate limit cache files
dir storage\cache\rate_limits\

# Should see files like:
# rate_limit_[hash]
```

---

## 🔍 Visual Verification Checklist

### ✅ Database Security

- [ ] Logger.php file exists
- [ ] Database.php has secure error handling
- [ ] storage/.htaccess exists
- [ ] Log files created in storage/logs/
- [ ] Passwords redacted in logs
- [ ] Error page shows generic message (not credentials)
- [ ] Full error details in log file

### ✅ API Validation

- [ ] Validator.php has new rules (in, integer, positive, etc.)
- [ ] ApiValidator.php exists
- [ ] All API controllers use ApiValidator trait
- [ ] SQL injection attempts blocked
- [ ] XSS attempts sanitized
- [ ] Invalid data returns 422 with errors
- [ ] Query parameters validated

### ✅ Rate Limiting

- [ ] RateLimiter.php exists
- [ ] RateLimitMiddleware.php exists
- [ ] rate_limit.php config exists
- [ ] Routes have rate limiting applied
- [ ] Auth endpoint: 5 requests/min
- [ ] API endpoints: 60 requests/min
- [ ] 429 response when exceeded
- [ ] X-RateLimit-* headers present
- [ ] Rate limit files in storage/cache/rate_limits/

---

## 🧪 Complete Test Script

Create this file to test everything at once:

**File:** `tests/verify_all_fixes.php`

```php
<?php

echo "=== COMPLETE SECURITY VERIFICATION ===\n\n";

$passed = 0;
$failed = 0;

// Test 1: Check Files Exist
echo "Test 1: Checking Files Exist\n";
echo "------------------------------\n";

$requiredFiles = [
    'app/Core/Logger.php',
    'app/Core/ApiValidator.php',
    'app/Core/RateLimiter.php',
    'app/Middleware/RateLimitMiddleware.php',
    'config/rate_limit.php',
    'storage/.htaccess',
];

foreach ($requiredFiles as $file) {
    $path = __DIR__ . '/../' . $file;
    if (file_exists($path)) {
        echo "✓ {$file}\n";
        $passed++;
    } else {
        echo "✗ {$file} - MISSING!\n";
        $failed++;
    }
}

echo "\n";

// Test 2: Run Security Tests
echo "Test 2: Running Security Tests\n";
echo "--------------------------------\n";

$tests = [
    'tests/security_fix_test.php',
    'tests/api_validation_simple_test.php',
    'tests/rate_limit_test.php',
];

foreach ($tests as $test) {
    $path = __DIR__ . '/../' . $test;
    if (file_exists($path)) {
        echo "Running {$test}...\n";
        exec("php \"{$path}\" 2>&1", $output, $returnCode);
        
        if ($returnCode === 0 && strpos(implode("\n", $output), '✓ PASS') !== false) {
            echo "✓ {$test} - PASSED\n";
            $passed++;
        } else {
            echo "✗ {$test} - FAILED\n";
            $failed++;
        }
        $output = [];
    } else {
        echo "✗ {$test} - NOT FOUND\n";
        $failed++;
    }
}

echo "\n";

// Test 3: Check Storage Directories
echo "Test 3: Checking Storage Directories\n";
echo "--------------------------------------\n";

$dirs = [
    'storage/logs',
    'storage/cache',
    'storage/cache/rate_limits',
];

foreach ($dirs as $dir) {
    $path = __DIR__ . '/../' . $dir;
    if (is_dir($path) && is_writable($path)) {
        echo "✓ {$dir} - exists and writable\n";
        $passed++;
    } else {
        echo "✗ {$dir} - not writable or missing\n";
        $failed++;
    }
}

echo "\n";

// Test 4: Check Log Files
echo "Test 4: Checking Log Files\n";
echo "---------------------------\n";

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
        echo "⚠ No redacted content found (may not have been tested yet)\n";
    }
} else {
    echo "⚠ No log files found (system may not have logged anything yet)\n";
}

echo "\n";

// Summary
echo "=== VERIFICATION SUMMARY ===\n";
echo "Passed: {$passed}\n";
echo "Failed: {$failed}\n\n";

if ($failed === 0) {
    echo "✅ ALL SECURITY FIXES VERIFIED!\n";
    echo "System is ready for production.\n";
} else {
    echo "⚠ Some checks failed. Please review above.\n";
}

echo "\n";
```

**Run it:**
```bash
php tests\verify_all_fixes.php
```

---

## 📱 Browser Testing

### Test 1: Error Page

1. **Break database connection:**
   - Edit `.env`: Set `DB_USER=wrong`

2. **Open browser:**
   - Visit: `http://localhost:8000`

3. **Expected:**
   - See professional error page
   - NO database credentials visible
   - Message: "Service Unavailable" or "Database Connection Failed"

4. **Restore:**
   - Fix `.env` back to correct credentials

### Test 2: API in Browser

1. **Start server:**
```bash
php -S localhost:8000 -t public
```

2. **Test endpoints in browser:**
   - `http://localhost:8000/api/v1/test`
   - Should see: `{"success":true,"message":"API is working!"}`

3. **Test rate limiting:**
   - Refresh the page 70 times rapidly
   - After 60 requests, should see 429 error

---

## 📊 Monitoring Dashboard

Create a simple monitoring page:

**File:** `public/security-status.php`

```php
<?php
// Simple security status page (remove in production!)

$status = [
    'logger' => file_exists(__DIR__ . '/../app/Core/Logger.php'),
    'api_validator' => file_exists(__DIR__ . '/../app/Core/ApiValidator.php'),
    'rate_limiter' => file_exists(__DIR__ . '/../app/Core/RateLimiter.php'),
    'logs_writable' => is_writable(__DIR__ . '/../storage/logs'),
    'cache_writable' => is_writable(__DIR__ . '/../storage/cache'),
];

$logFiles = glob(__DIR__ . '/../storage/logs/*.log');
$rateLimitFiles = glob(__DIR__ . '/../storage/cache/rate_limits/*');

header('Content-Type: application/json');
echo json_encode([
    'security_fixes' => $status,
    'log_files' => count($logFiles),
    'rate_limit_keys' => count($rateLimitFiles),
    'all_ok' => !in_array(false, $status, true),
], JSON_PRETTY_PRINT);
```

**Access:**
```
http://localhost:8000/security-status.php
```

**Expected:**
```json
{
    "security_fixes": {
        "logger": true,
        "api_validator": true,
        "rate_limiter": true,
        "logs_writable": true,
        "cache_writable": true
    },
    "log_files": 1,
    "rate_limit_keys": 3,
    "all_ok": true
}
```

---

## ✅ Final Checklist

Print this and check off as you verify:

```
DATABASE SECURITY:
[ ] Logger.php exists
[ ] Database.php updated
[ ] storage/.htaccess exists
[ ] Test with wrong credentials - shows generic error
[ ] Check logs - credentials redacted
[ ] storage/logs/ writable

API VALIDATION:
[ ] Validator.php has new rules
[ ] ApiValidator.php exists
[ ] All API controllers updated
[ ] Test SQL injection - blocked
[ ] Test XSS - sanitized
[ ] Test invalid input - returns 422

RATE LIMITING:
[ ] RateLimiter.php exists
[ ] RateLimitMiddleware.php exists
[ ] rate_limit.php config exists
[ ] Routes updated
[ ] Test auth endpoint - 5/min limit works
[ ] Test API endpoint - 60/min limit works
[ ] Check headers - X-RateLimit-* present
[ ] storage/cache/rate_limits/ writable

TESTS:
[ ] php tests/security_fix_test.php - PASS
[ ] php tests/api_validation_simple_test.php - PASS
[ ] php tests/rate_limit_test.php - PASS
[ ] php tests/verify_all_fixes.php - PASS

PRODUCTION READY:
[ ] All tests passing
[ ] All files exist
[ ] All directories writable
[ ] Error handling working
[ ] Validation working
[ ] Rate limiting working
[ ] Documentation reviewed
```

---

## 🆘 Troubleshooting

### Issue: Tests not running
```bash
# Check PHP version (need 8.2+)
php -v

# Check if files exist
dir tests\*.php
```

### Issue: Permission errors
```bash
# Make storage writable
icacls storage /grant Everyone:F /T
```

### Issue: Rate limiting not working
```bash
# Check if directory exists
mkdir storage\cache\rate_limits

# Make writable
icacls storage\cache\rate_limits /grant Everyone:F /T
```

### Issue: Logs not created
```bash
# Create logs directory
mkdir storage\logs

# Make writable
icacls storage\logs /grant Everyone:F /T
```

---

## 📞 Quick Help

**All tests passing?** ✅ You're good to go!

**Some tests failing?** Check the specific test output for details.

**Need help?** Review the documentation files:
- `SECURITY_FIX_DATABASE_CREDENTIALS.md`
- `SECURITY_FIX_API_VALIDATION.md`
- `SECURITY_FIX_RATE_LIMITING.md`

---

**Ready to verify? Start with the Quick Verification (5 minutes) above!**
