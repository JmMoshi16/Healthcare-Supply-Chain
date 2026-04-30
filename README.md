# 🏥 Healthcare Supply Chain Management System

## 📋 Project Overview

**Complete Healthcare Supply Chain Management System** built with **Vanilla PHP 8.2+** using custom MVC architecture. This system manages medicine inventory, batch tracking with expiry alerts, stock transactions, and provides a RESTful API for external integrations.

**Academic Project:** Secure Multi-Tenant Resource Orchestrator (SMRO) - Healthcare Implementation

---

## ✨ Key Features

### 🏥 Healthcare Domain
- **Medicine Management** - Complete CRUD for medicines with categories
- **Batch Tracking** - Track batches with manufacturing/expiry dates
- **Stock Transactions** - In/Out/Adjustment with full audit trail
- **Expiry Alerts** - Automatic alerts for medicines expiring in 30 days
- **Low Stock Alerts** - Notifications when stock falls below threshold
- **Supplier Management** - Track suppliers per batch

### 🔐 Security Features
- **CSRF Protection** - Token-based validation
- **XSS Filtering** - Input sanitization and output escaping
- **Password Hashing** - Bcrypt with cost factor 12
- **Role-Based Access Control** - SuperAdmin, Manager, Staff
- **API Authentication** - Bearer token with expiration
- **Rate Limiting** - Prevent brute force attacks
- **Secure Headers** - XSS, Clickjacking, MIME-sniffing protection

### 🎯 Technical Features
- **Custom MVC Framework** - No dependencies on CodeIgniter
- **Advanced Routing** - Resource routes, route groups, middleware
- **Query Builder** - PDO-based, no raw SQL
- **Migrations & Seeders** - Database version control
- **Validation** - Server-side input validation
- **Caching** - File-based caching system
- **Session Management** - Secure session handling
- **Image Upload** - With validation and manipulation
- **Pagination** - Built-in pagination support
- **RESTful API** - JSON responses with proper HTTP codes

### 🎨 UI/UX
- **Premium Design** - Clean, minimal, professional
- **Responsive** - Mobile-first approach
- **Smooth Animations** - 60fps interactions
- **Accessibility** - WCAG 2.1 Level AA compliant
- **Modern Components** - Cards, tables, forms, badges

---

## 🏗️ Project Structure

```
healthcare-supply-chain/
├── app/
│   ├── Core/                    # Framework core classes
│   │   ├── Router.php          # HTTP routing with middleware
│   │   ├── Request.php         # HTTP request handler
│   │   ├── Response.php        # HTTP response handler
│   │   ├── Database.php        # PDO database connection
│   │   ├── QueryBuilder.php    # SQL query builder
│   │   ├── Session.php         # Session management
│   │   ├── Validator.php       # Input validation
│   │   ├── View.php            # View rendering
│   │   └── Cache.php           # Caching system
│   │
│   ├── Controllers/             # Application controllers
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── MedicineController.php
│   │   ├── BatchController.php
│   │   ├── StockController.php
│   │   ├── UserController.php
│   │   └── Api/
│   │       ├── AuthApiController.php
│   │       └── MedicineApiController.php
│   │
│   ├── Models/                  # Data models
│   │   ├── BaseModel.php
│   │   ├── User.php
│   │   ├── Medicine.php
│   │   ├── Batch.php
│   │   ├── Stock.php
│   │   └── ApiToken.php
│   │
│   ├── Middleware/              # HTTP middleware
│   │   ├── AuthMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   └── ApiAuthMiddleware.php
│   │
│   ├── Helpers/                 # Helper functions
│   │   ├── functions.php
│   │   └── security.php
│   │
│   └── Views/                   # View templates
│       ├── layouts/
│       ├── auth/
│       ├── dashboard/
│       ├── medicines/
│       ├── batches/
│       ├── stocks/
│       └── users/
│
├── public/                      # Public web root
│   ├── index.php               # Application entry point
│   ├── .htaccess              # Apache rewrite rules
│   ├── assets/
│   │   ├── css/
│   │   │   └── healthcare.css  # Premium design system
│   │   ├── js/
│   │   │   └── healthcare.js   # Interactive enhancements
│   │   └── images/
│   └── uploads/
│       └── medicines/
│
├── database/
│   ├── migrations/              # Database migrations
│   ├── seeders/                # Database seeders
│   ├── migrate.php             # Migration runner
│   └── seed.php                # Seeder runner
│
├── config/                      # Configuration files
│   ├── app.php
│   ├── database.php
│   ├── routes.php
│   └── security.php
│
├── storage/                     # Storage directory
│   ├── cache/
│   ├── logs/
│   └── sessions/
│
├── tests/                       # PHPUnit tests
│   ├── Unit/
│   └── bootstrap.php
│
├── .env                        # Environment configuration
├── .env.example               # Environment template
├── .gitignore                 # Git ignore rules
├── composer.json              # Composer dependencies
├── phpunit.xml               # PHPUnit configuration
└── README.md                 # This file
```

---

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer
- Apache/Nginx with mod_rewrite enabled

### Step 1: Clone Repository

```bash
git clone https://github.com/your-team/healthcare-supply-chain.git
cd healthcare-supply-chain
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Environment Configuration

```bash
cp .env.example .env
```

Edit `.env` file:

```env
APP_NAME="Healthcare Supply Chain"
APP_ENV=development
APP_URL=http://localhost:8000
APP_KEY=generate-32-character-key-here

DB_HOST=localhost
DB_PORT=3306
DB_NAME=healthcare_supply
DB_USER=root
DB_PASS=

CSRF_TOKEN_NAME=csrf_token
SESSION_LIFETIME=7200
```

### Step 4: Create Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE healthcare_supply CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Step 5: Run Migrations

```bash
php database/migrate.php
```

### Step 6: Seed Database

```bash
php database/seed.php
```

### Step 7: Set Permissions

```bash
chmod -R 755 storage/
chmod -R 755 public/uploads/
```

### Step 8: Start Development Server

```bash
php -S localhost:8000 -t public
```

### Step 9: Access Application

- **Web Interface:** http://localhost:8000
- **API Endpoint:** http://localhost:8000/api/v1

---

## 👥 Default Users

| Role | Email | Password |
|------|-------|----------|
| SuperAdmin | admin@healthcare.com | Admin@123 |
| Manager | manager@healthcare.com | Manager@123 |
| Staff | staff@healthcare.com | Staff@123 |

---

## 📊 Database Schema

### Tables

1. **users** - User accounts with roles
2. **medicines** - Medicine master data
3. **batches** - Medicine batches with expiry tracking
4. **stocks** - Stock transaction history
5. **api_tokens** - API authentication tokens

### Relationships

```
users (1) ──→ (N) stocks
medicines (1) ──→ (N) batches
batches (1) ──→ (N) stocks
users (1) ──→ (N) api_tokens
```

---

## 🔌 API Documentation

### Authentication

**Get API Token**
```http
POST /api/v1/auth/token
Content-Type: application/json

{
  "email": "admin@healthcare.com",
  "password": "Admin@123"
}

Response:
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {...},
  "expires_at": "2025-01-24T10:30:00Z"
}
```

### Medicine Endpoints

**Get All Medicines**
```http
GET /api/v1/medicines?page=1
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": [...],
  "pagination": {
    "page": 1,
    "per_page": 20,
    "total": 50,
    "total_pages": 3
  }
}
```

**Get Medicine Details**
```http
GET /api/v1/medicines/{id}
Authorization: Bearer {token}
```

**Check Stock Levels**
```http
GET /api/v1/medicines/{id}/stock
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "medicine_id": 1,
    "name": "Paracetamol",
    "total_stock": 500,
    "batches": [...]
  }
}
```

---

## 🧪 Testing

### Run All Tests

```bash
./vendor/bin/phpunit
```

### Run Specific Test

```bash
./vendor/bin/phpunit tests/Unit/MedicineTest.php
```

### Test Coverage

```bash
./vendor/bin/phpunit --coverage-html coverage/
```

### Test Cases Included

1. **MedicineTest** - CRUD operations, validation
2. **BatchTest** - Batch creation, expiry checks
3. **StockTest** - Stock calculations, transactions
4. **AuthTest** - Login, registration, roles
5. **ApiTest** - API endpoints, authentication
6. **ValidationTest** - Input validation rules
7. **SecurityTest** - CSRF, XSS protection
8. **CacheTest** - Cache operations
9. **PaginationTest** - Pagination logic
10. **ImageTest** - Image upload, validation

---

## 🎯 Academic Requirements Compliance

| Requirement | Implementation | Status |
|-------------|----------------|--------|
| **MVC & Routing** | Custom Router with Resource Routes & Middleware | ✅ |
| **Database** | PDO Query Builder, Migrations, Seeders | ✅ |
| **Security** | CSRF, XSS Filtering, Password Hashing | ✅ |
| **Auth** | Role-Based: SuperAdmin, Manager, Staff | ✅ |
| **API** | RESTful with Bearer Token Authentication | ✅ |
| **Optimization** | Pagination, Image Upload, Caching | ✅ |
| **Testing** | 10+ PHPUnit Test Cases | ✅ |
| **Standards** | PSR-12 Coding Standards | ✅ |
| **Deployment** | Cloud-ready with .htaccess | ✅ |

---

## 🚀 Deployment

### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName healthcare.yourdomain.com
    DocumentRoot /var/www/healthcare-supply-chain/public
    
    <Directory /var/www/healthcare-supply-chain/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/healthcare-error.log
    CustomLog ${APACHE_LOG_DIR}/healthcare-access.log combined
</VirtualHost>
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name healthcare.yourdomain.com;
    root /var/www/healthcare-supply-chain/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 👥 Team Contribution

### Recommended Distribution

**Member 1:** Core Framework (Router, Database, QueryBuilder)  
**Member 2:** Authentication & User Management  
**Member 3:** Medicine & Batch Management  
**Member 4:** Stock Transactions & Alerts  
**Member 5:** API Development & Testing  

### Git Workflow

```bash
# Create feature branch
git checkout -b feature/medicine-management

# Make changes and commit
git add .
git commit -m "feat: add medicine CRUD operations"

# Push to remote
git push origin feature/medicine-management

# Create pull request for review
```

---

## 📝 License

This project is developed for academic purposes.

---

## 📞 Support

For issues or questions:
1. Check documentation
2. Review code comments
3. Run tests
4. Check logs in `storage/logs/`

---

## 🎓 Learning Resources

- **PHP Manual:** https://www.php.net/manual/en/
- **PSR-12 Standards:** https://www.php-fig.org/psr/psr-12/
- **PHPUnit:** https://phpunit.de/documentation.html
- **OWASP Top 10:** https://owasp.org/www-project-top-ten/

---

**Built for Academic Excellence**  
*Healthcare Supply Chain Management System*  
**Version:** 1.0.0  
**Status:** Production Ready ✅  
**Framework:** Vanilla PHP 8.2+ with Custom MVC
