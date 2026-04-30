# Healthcare Supply Chain - API Guide

## 🔗 Base URL
```
http://localhost:8000/api/v1
```

---

## 🔐 Authentication

### Step 1: Get API Token

**Endpoint:** `POST /api/v1/auth/token`

**Request:**
```bash
curl -X POST http://localhost:8000/api/v1/auth/token \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@healthcare.com",
    "password": "Admin@123"
  }'
```

**Response:**
```json
{
  "success": true,
  "token": "dGVzdF90b2tlbl8xMjM0NTY3ODkw...",
  "user": {
    "id": 1,
    "fullname": "System Administrator",
    "email": "admin@healthcare.com",
    "role": "superadmin"
  },
  "expires_at": "2025-01-25T10:30:00Z"
}
```

**Save the token** - you'll need it for all other API calls!

---

## 📦 Check Medicine Stock (Main Feature)

### Get Stock for Specific Medicine

**Endpoint:** `GET /api/v1/medicines/{id}/stock`

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Example Request:**
```bash
curl -X GET http://localhost:8000/api/v1/medicines/1/stock \
  -H "Authorization: Bearer dGVzdF90b2tlbl8xMjM0NTY3ODkw..."
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
        "manufacturing_date": "2024-01-15",
        "expiry_date": "2026-01-15",
        "supplier": "PharmaCorp Ltd",
        "purchase_price": "2.50",
        "selling_price": "5.00",
        "initial_quantity": 1000,
        "current_quantity": 850,
        "status": "active"
      },
      {
        "id": 2,
        "batch_number": "PCM-2024-002",
        "manufacturing_date": "2024-06-20",
        "expiry_date": "2026-06-20",
        "supplier": "PharmaCorp Ltd",
        "purchase_price": "2.50",
        "selling_price": "5.00",
        "initial_quantity": 500,
        "current_quantity": 500,
        "status": "active"
      }
    ]
  }
}
```

---

## 📋 All Medicine Endpoints

### 1. List All Medicines

**Endpoint:** `GET /api/v1/medicines?page=1`

```bash
curl -X GET "http://localhost:8000/api/v1/medicines?page=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
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
    "total": 20,
    "total_pages": 1
  }
}
```

### 2. Get Single Medicine Details

**Endpoint:** `GET /api/v1/medicines/{id}`

```bash
curl -X GET http://localhost:8000/api/v1/medicines/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🚨 Alert Endpoints

### 1. Get Expiring Medicines

**Endpoint:** `GET /api/v1/alerts/expiring?days=30`

```bash
curl -X GET "http://localhost:8000/api/v1/alerts/expiring?days=30" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

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

### 2. Get Low Stock Medicines

**Endpoint:** `GET /api/v1/alerts/low-stock?threshold=10`

```bash
curl -X GET "http://localhost:8000/api/v1/alerts/low-stock?threshold=10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

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

## 🔍 Search API

**Endpoint:** `GET /api/search?q={query}`

**Note:** This requires web authentication (logged in user), not Bearer token.

```bash
curl -X GET "http://localhost:8000/api/search?q=paracetamol" \
  --cookie "session_cookie_here"
```

---

## 📝 Complete Example Workflow

### Step-by-Step: Check Stock for All Medicines

```bash
#!/bin/bash

# 1. Get API Token
TOKEN=$(curl -s -X POST http://localhost:8000/api/v1/auth/token \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@healthcare.com","password":"Admin@123"}' \
  | jq -r '.token')

echo "Token: $TOKEN"

# 2. Get all medicines
curl -s -X GET "http://localhost:8000/api/v1/medicines" \
  -H "Authorization: Bearer $TOKEN" \
  | jq '.data[] | {id, name}'

# 3. Check stock for medicine ID 1
curl -s -X GET "http://localhost:8000/api/v1/medicines/1/stock" \
  -H "Authorization: Bearer $TOKEN" \
  | jq '.data'

# 4. Check expiring medicines
curl -s -X GET "http://localhost:8000/api/v1/alerts/expiring?days=30" \
  -H "Authorization: Bearer $TOKEN" \
  | jq '.data'

# 5. Check low stock
curl -s -X GET "http://localhost:8000/api/v1/alerts/low-stock?threshold=10" \
  -H "Authorization: Bearer $TOKEN" \
  | jq '.data'
```

---

## 🐍 Python Example

```python
import requests

# Base URL
BASE_URL = "http://localhost:8000/api/v1"

# 1. Get Token
response = requests.post(f"{BASE_URL}/auth/token", json={
    "email": "admin@healthcare.com",
    "password": "Admin@123"
})
token = response.json()["token"]

# 2. Set headers
headers = {"Authorization": f"Bearer {token}"}

# 3. Get all medicines
medicines = requests.get(f"{BASE_URL}/medicines", headers=headers).json()
print(f"Total medicines: {medicines['pagination']['total']}")

# 4. Check stock for each medicine
for med in medicines['data']:
    stock = requests.get(
        f"{BASE_URL}/medicines/{med['id']}/stock", 
        headers=headers
    ).json()
    
    print(f"{med['name']}: {stock['data']['total_stock']} units")

# 5. Get expiring medicines
expiring = requests.get(
    f"{BASE_URL}/alerts/expiring?days=30", 
    headers=headers
).json()
print(f"Expiring soon: {expiring['count']} batches")

# 6. Get low stock
low_stock = requests.get(
    f"{BASE_URL}/alerts/low-stock?threshold=10", 
    headers=headers
).json()
print(f"Low stock: {low_stock['count']} medicines")
```

---

## 🔧 Testing with Postman

### 1. Create New Request
- Method: `POST`
- URL: `http://localhost:8000/api/v1/auth/token`
- Body (JSON):
```json
{
  "email": "admin@healthcare.com",
  "password": "Admin@123"
}
```
- Click Send
- Copy the `token` from response

### 2. Check Medicine Stock
- Method: `GET`
- URL: `http://localhost:8000/api/v1/medicines/1/stock`
- Headers:
  - Key: `Authorization`
  - Value: `Bearer YOUR_TOKEN_HERE`
- Click Send

---

## ⚠️ Error Responses

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

## 📊 API Endpoints Summary

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/api/v1/auth/token` | POST | None | Get API token |
| `/api/v1/medicines` | GET | Bearer | List all medicines |
| `/api/v1/medicines/{id}` | GET | Bearer | Get medicine details |
| `/api/v1/medicines/{id}/stock` | GET | Bearer | **Check medicine stock** |
| `/api/v1/alerts/expiring` | GET | Bearer | Get expiring medicines |
| `/api/v1/alerts/low-stock` | GET | Bearer | Get low stock medicines |
| `/api/search` | GET | Session | Search medicines/batches |

---

## 🚀 Quick Start

**1. Get your token:**
```bash
curl -X POST http://localhost:8000/api/v1/auth/token \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@healthcare.com","password":"Admin@123"}'
```

**2. Check stock for medicine ID 1:**
```bash
curl -X GET http://localhost:8000/api/v1/medicines/1/stock \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Done!** You now have the current stock levels for that medicine.

---

## 📞 Support

- API runs on: `http://localhost:8000`
- Default credentials: `admin@healthcare.com` / `Admin@123`
- Token expires: 24 hours
- Rate limit: 100 requests/minute

---

**The main API to check medicine stocks is:**
```
GET /api/v1/medicines/{id}/stock
```
