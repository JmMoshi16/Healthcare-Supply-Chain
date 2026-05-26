# 🔐 API Validation - Quick Reference

## What Was Fixed?

**CRITICAL VULNERABILITY:** API endpoints had no input validation

**BEFORE:**
```php
// No validation - accepts anything!
$data = $request->all();
$user = (new User())->authenticate($data['email'], $data['password']);
```

**AFTER:**
```php
// Comprehensive validation
$data = $this->validateRequest($request, [
    'email' => 'required|email|max:255',
    'password' => 'required|min:6|max:255',
]);
```

---

## Files Changed

1. ✅ `app/Core/Validator.php` - Added 12+ new rules
2. ✅ `app/Core/ApiValidator.php` - NEW (validation trait)
3. ✅ `app/Controllers/Api/AuthApiController.php` - Full validation
4. ✅ `app/Controllers/Api/MedicineApiController.php` - Full validation
5. ✅ `app/Controllers/Api/SearchApiController.php` - SQL/XSS prevention

---

## How to Use

### 1. Add Trait to Controller

```php
use App\Core\ApiValidator;

class MyApiController extends BaseController
{
    use ApiValidator;
    
    // Now you have access to validation methods
}
```

### 2. Validate Request Body

```php
public function create(Request $request)
{
    // Validates and sanitizes input
    $data = $this->validateRequest($request, [
        'name' => 'required|alpha_dash|between:3,50',
        'email' => 'required|email',
        'age' => 'required|integer|positive',
        'status' => 'in:active,inactive',
    ]);
    
    // $data is now safe to use
}
```

**Returns 422 with errors if validation fails:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email must be a valid email"],
    "age": ["Age must be a positive number"]
  }
}
```

### 3. Validate Query Parameters

```php
public function index(Request $request)
{
    // Type, default, min, max
    $page = $this->validateQueryParam($request, 'page', 'integer', 1, 1, 1000);
    $limit = $this->validateQueryParam($request, 'limit', 'integer', 20, 1, 100);
    
    // String with length constraints
    $search = $this->validateQueryParam($request, 'search', 'string', '', 2, 100);
}
```

**Returns 400 if invalid:**
```json
{
  "success": false,
  "message": "Invalid query parameter: page must be at least 1"
}
```

### 4. Validate ID Parameters

```php
public function show(Request $request, string $id)
{
    // Ensures ID is positive integer
    $id = $this->validateId($id);
    
    // Safe to use
    $item = $this->model->find($id);
}
```

**Returns 400 if invalid:**
```json
{
  "success": false,
  "message": "Invalid ID provided"
}
```

---

## Validation Rules

### String Rules
```php
'required'              // Must not be empty
'email'                 // Valid email
'url'                   // Valid URL
'ip'                    // Valid IP address
'alpha'                 // Only letters
'alpha_num'             // Letters and numbers
'alpha_dash'            // Letters, numbers, -, _
'min:8'                 // Min 8 characters
'max:255'               // Max 255 characters
'between:3,20'          // Between 3-20 characters
'regex:/^[A-Z]+$/'      // Custom pattern
```

### Numeric Rules
```php
'numeric'               // Must be numeric
'integer'               // Must be integer
'positive'              // Must be positive
```

### Other Rules
```php
'in:active,inactive'    // Must be in list
'array'                 // Must be array
'boolean'               // Must be boolean
'date'                  // Valid date
'confirmed'             // Matches _confirmation field
```

---

## Security Features

### ✅ SQL Injection Prevention

```php
// Automatically detects and blocks
if (!$this->preventSqlInjection($query)) {
    return $this->json(['success' => false, 'message' => 'Invalid input'], 400);
}
```

**Blocks:**
- `' OR '1'='1`
- `'; DROP TABLE`
- `UNION SELECT`
- SQL comments (`--`, `#`, `/* */`)

### ✅ XSS Prevention

```php
// Sanitizes HTML/JavaScript
$clean = $this->preventXss($userInput);
```

**Removes:**
- `<script>` tags
- `<img>` with onerror
- `javascript:` protocol
- All HTML tags

### ✅ Input Sanitization

```php
// Automatically applied in validateRequest()
$data = $this->sanitizeInput($data);
```

**Removes:**
- Null bytes (`\0`)
- Control characters
- Trims whitespace

---

## Rate Limiting

**Auth endpoint has rate limiting:**

```php
// 5 attempts per minute per email+IP
private function checkRateLimiting(string $email): void
```

**Returns 429 if exceeded:**
```json
{
  "success": false,
  "message": "Too many attempts. Please try again later."
}
```

---

## Error Handling

### Validation Errors (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field": ["Error message"]
  }
}
```

### Bad Request (400)
```json
{
  "success": false,
  "message": "Invalid ID provided"
}
```

### Too Many Requests (429)
```json
{
  "success": false,
  "message": "Too many attempts. Please try again later."
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to fetch data"
}
```

---

## Security Logging

All security events are logged:

```php
// Failed authentication
Logger::warning('API authentication failed', [
    'email' => $email,
    'ip' => $_SERVER['REMOTE_ADDR'],
]);

// SQL injection attempt
Logger::warning('API: SQL injection attempt detected', [
    'query' => $query,
    'ip' => $_SERVER['REMOTE_ADDR'],
]);

// Successful authentication
Logger::info('API token generated', [
    'user_id' => $user['id'],
    'email' => $email,
]);
```

**View logs:**
```bash
tail -f storage/logs/$(date +%Y-%m-%d).log
```

---

## Testing

### Run Tests
```bash
php tests/api_validation_simple_test.php
```

### Expected Output
```
✓ Enhanced validation rules
✓ SQL injection prevention
✓ XSS prevention
✓ Input sanitization
✓ Validation error messages

Security Status: ✅ API VALIDATION IMPLEMENTED
```

### Manual Testing

**Test validation:**
```bash
curl -X POST "http://localhost:8000/api/v1/auth/token" \
  -H "Content-Type: application/json" \
  -d '{"email":"invalid","password":"123"}'
```

**Expected:**
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

**Test SQL injection:**
```bash
curl -X GET "http://localhost:8000/api/v1/search?q=' OR '1'='1"
```

**Expected:**
```json
{
  "success": false,
  "message": "Invalid search query"
}
```

**Test rate limiting:**
```bash
# Run 6 times rapidly
for i in {1..6}; do
  curl -X POST "http://localhost:8000/api/v1/auth/token" \
    -H "Content-Type: application/json" \
    -d '{"email":"test@test.com","password":"wrong"}'
done
```

**6th request returns 429**

---

## Common Patterns

### Pattern 1: Create Resource
```php
public function create(Request $request)
{
    $data = $this->validateRequest($request, [
        'name' => 'required|alpha_dash|between:3,50',
        'email' => 'required|email|unique:users,email',
        'status' => 'in:active,inactive',
    ]);
    
    try {
        $id = $this->model->create($data);
        return $this->json(['success' => true, 'id' => $id], 201);
    } catch (\Exception $e) {
        Logger::error('Failed to create resource', ['error' => $e->getMessage()]);
        return $this->json(['success' => false, 'message' => 'Creation failed'], 500);
    }
}
```

### Pattern 2: Update Resource
```php
public function update(Request $request, string $id)
{
    $id = $this->validateId($id);
    
    $data = $this->validateRequest($request, [
        'name' => 'alpha_dash|between:3,50',
        'status' => 'in:active,inactive',
    ]);
    
    try {
        $this->model->update($id, $data);
        return $this->json(['success' => true, 'message' => 'Updated']);
    } catch (\Exception $e) {
        Logger::error('Failed to update', ['id' => $id, 'error' => $e->getMessage()]);
        return $this->json(['success' => false, 'message' => 'Update failed'], 500);
    }
}
```

### Pattern 3: List with Filters
```php
public function index(Request $request)
{
    $page = $this->validateQueryParam($request, 'page', 'integer', 1, 1, 1000);
    $limit = $this->validateQueryParam($request, 'limit', 'integer', 20, 1, 100);
    $status = $this->validateQueryParam($request, 'status', 'string', 'all');
    
    if ($status !== 'all' && !in_array($status, ['active', 'inactive'])) {
        return $this->json(['success' => false, 'message' => 'Invalid status'], 400);
    }
    
    try {
        $result = $this->model->paginate($limit, $page, $status);
        return $this->json(['success' => true, 'data' => $result]);
    } catch (\Exception $e) {
        Logger::error('Failed to fetch list', ['error' => $e->getMessage()]);
        return $this->json(['success' => false, 'message' => 'Fetch failed'], 500);
    }
}
```

---

## Security Checklist

- [x] All API endpoints use ApiValidator trait
- [x] All POST/PUT requests validated
- [x] All query parameters validated
- [x] All ID parameters validated
- [x] SQL injection prevention applied
- [x] XSS sanitization applied
- [x] Input sanitization applied
- [x] Rate limiting on auth endpoint
- [x] Security events logged
- [x] Proper HTTP status codes
- [x] Error messages don't expose internals

---

**Status:** ✅ API VALIDATION COMPLETE  
**Security:** 🔒 PRODUCTION READY  
**Date:** 2025-01-24
