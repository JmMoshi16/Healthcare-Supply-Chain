# Healthcare Supply Chain API Documentation

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication

All API endpoints (except `/auth/token`) require Bearer token authentication.

### Get API Token
```http
POST /api/v1/auth/token
Content-Type: application/json

{
  "email": "admin@healthcare.com",
  "password": "Admin@123"
}
```

**Response:**
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "fullname": "System Administrator",
    "email": "admin@healthcare.com",
    "role": "superadmin"
  },
  "expires_at": "2025-01-25T10:30:00Z"
}
```

---

## Medicine Endpoints

### List All Medicines
```http
GET /api/v1/medicines?page=1
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Paracetamol",
      "generic_name": "Acetaminophen",
      "category": "Analgesic",
      "description": "Pain reliever and fever reducer",
      "unit": "tablet",
      "is_active": 1
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 20,
    "total": 50,
    "total_pages": 3
  }
}
```

### Get Medicine Details
```http
GET /api/v1/medicines/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Paracetamol",
    "generic_name": "Acetaminophen",
    "category": "Analgesic",
    "unit": "tablet"
  }
}
```

### Check Medicine Stock
```http
GET /api/v1/medicines/{id}/stock
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "medicine_id": 1,
    "name": "Paracetamol",
    "total_stock": 1350,
    "batches": [
      {
        "id": 1,
        "batch_number": "PCM-2024-001",
        "expiry_date": "2026-01-15",
        "current_quantity": 850,
        "status": "active"
      },
      {
        "id": 2,
        "batch_number": "PCM-2024-002",
        "expiry_date": "2026-06-20",
        "current_quantity": 500,
        "status": "active"
      }
    ]
  }
}
```

---

## Alert Endpoints

### Get Expiring Medicines
```http
GET /api/v1/alerts/expiring?days=30
Authorization: Bearer {token}
```

**Parameters:**
- `days` (optional): Number of days to check (default: 30)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 4,
      "batch_number": "CET-2024-001",
      "medicine_name": "Cetirizine",
      "generic_name": "Cetirizine HCl",
      "expiry_date": "2025-12-05",
      "current_quantity": 400,
      "status": "active"
    }
  ],
  "count": 1,
  "message": "1 batches expiring in 30 days"
}
```

### Get Low Stock Medicines
```http
GET /api/v1/alerts/low-stock?threshold=10
Authorization: Bearer {token}
```

**Parameters:**
- `threshold` (optional): Minimum stock level (default: 10)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 3,
      "name": "Ibuprofen",
      "generic_name": "Ibuprofen",
      "category": "Anti-inflammatory",
      "total_stock": 5
    }
  ],
  "count": 1,
  "message": "1 medicines below threshold of 10"
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Medicine not found"
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Email and password required"
}
```

---

## Rate Limiting

- **Rate Limit:** 100 requests per minute per IP
- **Headers:** 
  - `X-RateLimit-Limit: 100`
  - `X-RateLimit-Remaining: 95`

---

## Example Usage (cURL)

### Get Token
```bash
curl -X POST http://localhost:8000/api/v1/auth/token \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@healthcare.com","password":"Admin@123"}'
```

### Get Medicines
```bash
curl -X GET http://localhost:8000/api/v1/medicines \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Check Expiring Medicines
```bash
curl -X GET "http://localhost:8000/api/v1/alerts/expiring?days=30" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error
