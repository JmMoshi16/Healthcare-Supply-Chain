# Healthcare Supply Chain Management System
## Full-Stack Technical Analysis & Architecture Review

**Analyzed by:** Full-Stack Developer  
**Date:** May 27, 2026  
**Environment:** Production (InfinityFree Hosting)  
**Tech Stack:** PHP 8.2+ | MySQL | Vanilla JS | Custom MVC Framework

---

## 🎯 EXECUTIVE SUMMARY

**HealthChain** is a custom-built PHP MVC application for managing pharmaceutical inventory, batch tracking, expiry monitoring, and stock transactions. The system features role-based access control, RESTful API, automated alerts, and real-time notifications.

**Current Status:** ✅ Successfully deployed to InfinityFree  
**Production URL:** http://healthcaresupplychain.free.nf

---

## 📊 SYSTEM ARCHITECTURE

### Architecture Pattern
- **Pattern:** Custom MVC (Model-View-Controller)
- **No Framework:** Built from scratch without Laravel/Symfony
- **Routing:** Custom router with middleware pipeline
- **ORM:** Custom QueryBuilder with PDO
- **Authentication:** Session-based with bcrypt password hashing

### Directory Structure
```
Healthcare-Supply-Chain/
├── app/
│   ├── Controllers/      # Business logic & request handling
│   ├── Core/            # Framework components (Router, DB, Session, etc.)
│   ├── Helpers/         # Utility functions & environment loader
│   ├── Middleware/      # Auth, CSRF, Rate limiting, API guards
│   ├── Models/          # Data layer with QueryBuilder
│   └── Views/           # PHP templates (no Blade/Twig)
├── config/              # App, database, routes, security configs
├── database/
│   ├── migrations/      # Schema definitions
│   └── seeders/         # Default data (users, medicines, batches)
├── public/              # Web root (index.php, assets, uploads)
├── storage/             # Logs, cache, sessions
├── tests/               # PHPUnit tests
└── vendor/              # Composer dependencies (PHPMailer)
```

---

## 🔐 SECURITY IMPLEMENTATION

### Authentication & Authorization
- **Password Hashing:** bcrypt with cost factor 12
- **Session Management:** Custom session handler with regeneration on login
- **Session Storage:** File-based in `storage/sessions/` (InfinityFree compatible)
- **CSRF Protection:** Token-based validation on all POST/PUT/DELETE requests
- **Role-Based Access Control (RBAC):** 3 roles with granular permissions
  - `superadmin` - Full system access
  - `manager` - Inventory management (CRUD on medicines, batches, stocks)
  - `staff` - Read-only access

### Security Features
✅ SQL Injection Prevention (PDO prepared statements)  
✅ XSS Protection (htmlspecialchars escaping)  
✅ CSRF Tokens on all forms  
✅ Rate Limiting (5 attempts per 5 minutes)  
✅ Soft Deletes (data retention)  
✅ Activity Logging (audit trail)  
✅ Secure Headers (X-Frame-Options, X-Content-Type-Options, etc.)  
✅ Password Verification Codes (registration protection)  

### Known Security Gaps
⚠️ No HTTPS enforcement (InfinityFree free tier limitation)  
⚠️ No 2FA/MFA implementation  
⚠️ API tokens stored in plain text (should be hashed)  
⚠️ No input sanitization library (relies on manual escaping)  

---

## 💾 DATABASE SCHEMA

### Core Tables (9 total)

**1. users**
- Stores user accounts with role-based access
- Fields: id, fullname, email, password, role, profile_image, is_active, deleted_at
- Soft deletes enabled

**2. medicines**
- Master medicine catalog
- Fields: id, name, generic_name, category_id, description, unit, image, is_active
- Linked to categories (foreign key)

**3. batches**
- Batch tracking with expiry dates
- Fields: id, medicine_id, batch_number, manufacturing_date, expiry_date, supplier, purchase_price, selling_price, initial_quantity, current_quantity, status
- Status: active | expired | recalled

**4. stocks**
- Transaction log (in/out movements)
- Fields: id, batch_id, transaction_type, quantity, reference_number, notes, created_by
- Transaction types: purchase | sale | adjustment | return | transfer

**5. categories**
- Medicine categorization
- Fields: id, name, description

**6. api_tokens**
- API authentication tokens
- Fields: id, user_id, token, name, scopes, expires_at, last_used_at

**7. notifications**
- In-app notification system
- Fields: id, user_id, type, title, message, is_read, created_at

**8. activity_logs**
- Audit trail for all CRUD operations
- Fields: id, user_id, action, model, model_id, old_values, new_values, ip_address, user_agent

**9. calendar_notes** (implied from routes)
- User notes for calendar events

### Database Relationships
```
users (1) ──< (N) api_tokens
users (1) ──< (N) notifications
users (1) ──< (N) activity_logs
users (1) ──< (N) stocks (created_by)

categories (1) ──< (N) medicines
medicines (1) ──< (N) batches
batches (1) ──< (N) stocks
```

---

## 🛣️ ROUTING & API ENDPOINTS

### Web Routes (Session-based)

**Public Routes**
- `GET /` → Login page
- `GET /login` → Login form
- `POST /login` → Authenticate user
- `GET /register` → Registration form
- `POST /register` → Create new user (with verification code)
- `GET /logout` → Destroy session

**Protected Routes** (Auth + CSRF middleware)
- `GET /dashboard` → Main dashboard with stats
- RESTful resources:
  - `/medicines` (index, create, store, show, edit, update, destroy)
  - `/batches` (full CRUD)
  - `/stocks` (full CRUD)
  - `/categories` (full CRUD)
  - `/users` (superadmin only)

### API Routes (Token-based)

**Public API**
- `POST /api/v1/auth/token` → Generate API token

**Protected API** (Bearer token required)
- `GET /api/v1/medicines` → List all medicines
- `GET /api/v1/medicines/{id}` → Get medicine details
- `GET /api/v1/medicines/{id}/stock` → Get stock levels
- `GET /api/v1/alerts/expiring` → Expiring batches
- `GET /api/v1/alerts/low-stock` → Low stock items

**Internal API** (Session-based, for AJAX)
- `GET /api/search` → Global search
- `POST /api/medicines/{id}/toggle-status` → Activate/deactivate
- `GET /api/notifications` → Fetch notifications
- `POST /api/notifications/mark-read` → Mark as read
- `GET /api/calendar/notes` → Calendar notes CRUD
- `GET /api/calendar/expiring` → Expiring medicines by month

---

## 🧩 CORE COMPONENTS

### 1. Router (`app/Core/Router.php`)
- Custom routing with regex pattern matching
- Middleware pipeline support
- Route grouping with prefixes
- RESTful resource routing helper
- HTTP method spoofing (PUT/DELETE via `_method`)

### 2. Database (`app/Core/Database.php`)
- PDO singleton connection
- Prepared statements for all queries
- Connection pooling disabled (InfinityFree limitation)
- Graceful error handling with logging
- Timeout: 10 seconds

### 3. QueryBuilder (`app/Core/QueryBuilder.php`)
- Fluent interface for SQL queries
- Methods: `select()`, `where()`, `join()`, `orderBy()`, `limit()`, `get()`, `first()`, `count()`, `insert()`, `update()`, `delete()`
- Supports: WHERE, OR WHERE, IS NULL, LEFT JOIN, INNER JOIN
- No support for: GROUP BY, HAVING, subqueries, transactions

### 4. Session (`app/Core/Session.php`)
- Custom session save path (`storage/sessions/`)
- Cookie params configured for InfinityFree (HTTP-only, SameSite=Lax)
- Flash messages support
- Session regeneration on login (security fix applied)

### 5. Request (`app/Core/Request.php`)
- HTTP request abstraction
- Methods: `method()`, `uri()`, `get()`, `post()`, `input()`, `all()`, `file()`, `header()`, `bearerToken()`, `json()`
- Strips `/public/` prefix for InfinityFree compatibility

### 6. Response (`app/Core/Response.php`)
- JSON and HTML responses
- HTTP status codes
- Redirect support

### 7. Validator (`app/Core/Validator.php`)
- Rules: required, email, min, max, numeric, in, unique
- Custom error messages
- No support for: regex, date validation, file validation

### 8. Mailer (`app/Core/Mailer.php`)
- PHPMailer integration
- Email templates in `app/Views/emails/`
- SMTP configuration via `.env`

### 9. AlertService (`app/Core/AlertService.php`)
- Automated email alerts for:
  - Expiring batches (30 days threshold)
  - Low stock items (configurable threshold)
  - Daily/weekly inventory reports
- Triggered via `cron_alerts.php`

### 10. Logger (`app/Core/Logger.php`)
- File-based logging to `storage/logs/YYYY-MM-DD.log`
- Log levels: debug, info, warning, error, critical
- Context data support

---

## 🎨 FRONTEND ARCHITECTURE

### Technology Stack
- **No Framework:** Vanilla JavaScript (no React/Vue/Angular)
- **CSS:** Custom stylesheets (no Tailwind/Bootstrap)
- **AJAX:** Fetch API for async requests
- **UI Components:** Custom-built (no component library)

### Key Features
- Real-time search (debounced)
- Notification dropdown with unread count
- Calendar view for expiring medicines
- Modal dialogs for confirmations
- Toast notifications
- Responsive design

### Asset Structure
```
public/assets/
├── css/
│   ├── healthcare.css           # Base styles
│   ├── dashboard.css            # Dashboard-specific
│   ├── medicines-advanced.css   # Medicine management
│   ├── batches-advanced.css     # Batch management
│   ├── calendar.css             # Calendar view
│   ├── notifications.css        # Notification UI
│   └── sidebar-advanced.css     # Navigation
└── js/
    ├── healthcare.js            # Core JS utilities
    ├── medicines-advanced.js    # Medicine CRUD
    ├── batches-advanced.js      # Batch CRUD
    └── email-management.js      # Email templates
```

---

## 📦 DEPENDENCIES

### Production Dependencies
```json
{
  "php": ">=8.2",
  "phpmailer/phpmailer": "^6.8"
}
```

### Development Dependencies
```json
{
  "phpunit/phpunit": "^10.0"
}
```

**Total Vendor Size:** ~2MB (PHPMailer only)

---

## 🚀 DEPLOYMENT (InfinityFree)

### Hosting Configuration
- **Provider:** InfinityFree (free tier)
- **Domain:** healthcaresupplychain.free.nf
- **MySQL Host:** sql201.infinityfree.com
- **Database:** if0_42028943_healthcare_supply
- **FTP:** ftpupload.net:21

### Deployment Fixes Applied
1. ✅ Session save path set to `storage/sessions/` (not `/tmp`)
2. ✅ Cookie params configured for HTTP (no HTTPS on free tier)
3. ✅ `.htaccess` routing fixed (root + public)
4. ✅ Database host changed from `localhost` to `sql201.infinityfree.com`
5. ✅ Request URI stripping `/public/` prefix
6. ✅ Session regeneration after login
7. ✅ CHMOD 755 on `storage/` and `public/uploads/`

### Known InfinityFree Limitations
- ❌ No SSH access (no command-line migrations/seeders)
- ❌ No cron jobs (must use InfinityFree's cron feature)
- ❌ No HTTPS on free tier
- ❌ Shared `/tmp` directory (sessions get wiped)
- ❌ Limited PHP settings (can't use `php_flag` in `.htaccess`)
- ❌ 10-second MySQL timeout
- ❌ No Composer on server (must upload `vendor/` folder)

---

## 🧪 TESTING

### Test Coverage
- **Unit Tests:** 10 test files in `tests/Unit/`
  - ApiTest, AuthTest, BatchTest, CacheTest, CategoryTest
  - MedicineTest, PaginationTest, SecurityTest, StockTest, ValidationTest
- **Integration Tests:** 7 test files in `tests/`
  - API validation, rate limiting, batch expiry, security fixes

### Test Framework
- **PHPUnit 10.0**
- **Bootstrap:** `tests/bootstrap.php` (loads environment)
- **Configuration:** `phpunit.xml`

### Test Execution
```bash
vendor/bin/phpunit
```

---

## 📈 DASHBOARD METRICS

### Statistics Displayed
1. **Medicine Stats**
   - Total medicines
   - Active medicines
   - Inactive medicines

2. **Batch Stats**
   - Total batches
   - Active batches
   - Expired batches
   - Expiring soon (30 days)

3. **Stock Stats**
   - Low stock items (< 10 units)
   - Total stock value (calculated)
   - Recent transactions (last 10)
   - Today's transactions

4. **Calendar View**
   - Expiring medicines (next 3 months)
   - Grouped by expiry date

5. **Charts** (implied from `weekly_stats` and `monthly_stats`)
   - Weekly transaction trends
   - Monthly inventory movements

---

## 🔧 CONFIGURATION FILES

### `.env` (Production)
```env
APP_NAME=HealthChain
APP_ENV=production
APP_DEBUG=false
APP_URL=http://healthcaresupplychain.free.nf

DB_HOST=sql201.infinityfree.com
DB_PORT=3306
DB_NAME=if0_42028943_healthcare_supply
DB_USER=if0_42028943
DB_PASS=ZT67BTWXaLC

MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@healthcare-supply.local
MAIL_FROM_NAME="Healthcare Supply Chain"
```

### `config/registration_codes.php`
- Protects registration with verification codes
- Separate codes for: admin, manager IDs, staff IDs

### `config/security.php`
- CSRF token name
- Session lifetime: 7200 seconds (2 hours)
- Bcrypt cost: 12
- Rate limit: 5 attempts per 300 seconds

---

## 🐛 KNOWN ISSUES & TECHNICAL DEBT

### Critical Issues
1. ❌ **API tokens stored in plain text** - Should be hashed like passwords
2. ❌ **No database transactions** - Risk of data inconsistency
3. ❌ **No input sanitization library** - Relies on manual escaping
4. ❌ **No HTTPS** - Credentials sent in plain text (InfinityFree limitation)

### Medium Priority
5. ⚠️ **No pagination on dashboard** - All records loaded at once
6. ⚠️ **No caching layer** - Every request hits the database
7. ⚠️ **No queue system** - Email alerts sent synchronously
8. ⚠️ **No file upload validation** - Only checks MIME type
9. ⚠️ **No API rate limiting** - Only web login rate limiting
10. ⚠️ **No database backup automation** - Manual export only

### Low Priority
11. 📝 **No API documentation** - No Swagger/OpenAPI spec
12. 📝 **No frontend build process** - No minification/bundling
13. 📝 **No error tracking** - No Sentry/Bugsnag integration
14. 📝 **No performance monitoring** - No APM tool
15. 📝 **No CI/CD pipeline** - Manual deployment

---

## 💡 RECOMMENDATIONS

### Immediate Actions (High Priority)
1. **Hash API tokens** - Use bcrypt like passwords
2. **Add database transactions** - Wrap multi-step operations
3. **Implement HTTPS** - Upgrade hosting or use Cloudflare
4. **Add API rate limiting** - Prevent abuse
5. **Implement pagination** - Limit records per page

### Short-term Improvements (1-2 weeks)
6. **Add caching layer** - Redis or file-based cache
7. **Implement queue system** - Background jobs for emails
8. **Add file upload validation** - Check file size, extension, content
9. **Create API documentation** - Swagger/Postman collection
10. **Add database backup cron** - Daily automated backups

### Long-term Enhancements (1-3 months)
11. **Migrate to Laravel** - Leverage mature framework features
12. **Add frontend framework** - Vue.js or React for better UX
13. **Implement 2FA** - TOTP-based authentication
14. **Add reporting module** - PDF/Excel export
15. **Implement barcode scanning** - Mobile app integration
16. **Add multi-language support** - i18n/l10n
17. **Implement real-time notifications** - WebSockets/Pusher
18. **Add data analytics** - Charts, trends, forecasting

---

## 🎯 CODE QUALITY ASSESSMENT

### Strengths ✅
- Clean MVC separation
- Consistent naming conventions
- PSR-4 autoloading
- Comprehensive middleware system
- Soft deletes implementation
- Activity logging (audit trail)
- RESTful API design
- Role-based permissions

### Weaknesses ❌
- No dependency injection container
- Tight coupling in some controllers
- Limited error handling in views
- No service layer (business logic in controllers)
- No repository pattern
- No interface abstractions
- Limited test coverage
- No code documentation (PHPDoc)

### Code Metrics (Estimated)
- **Total Lines of Code:** ~8,000 LOC
- **Controllers:** 14 files
- **Models:** 9 files
- **Middleware:** 8 files
- **Views:** ~30 files
- **Migrations:** 15 files
- **Tests:** 17 files

---

## 📚 DEVELOPER ONBOARDING

### Local Setup
```bash
# 1. Clone repository
git clone https://github.com/JmMoshi16/Healthcare-Supply-Chain.git
cd Healthcare-Supply-Chain

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
# Edit .env with local database credentials

# 4. Run migrations
php database/migrate.php

# 5. Seed database
php database/seed.php

# 6. Start local server
php -S localhost:8000 -t public
```

### Default Credentials
- **SuperAdmin:** admin@healthcare.com / Admin@123
- **Manager:** manager@healthcare.com / Manager@123
- **Staff:** staff@healthcare.com / Staff@123

---

## 🔍 PERFORMANCE ANALYSIS

### Database Queries
- **Dashboard:** ~15 queries (no N+1 problem detected)
- **Medicine List:** 1 query with LEFT JOIN
- **Batch List:** 1 query with JOIN
- **No Query Caching:** Every request hits DB

### Page Load Times (Estimated on InfinityFree)
- Login page: ~500ms
- Dashboard: ~1.5s (multiple queries)
- Medicine list: ~800ms
- Batch details: ~600ms

### Optimization Opportunities
1. Implement query result caching
2. Add database indexes on foreign keys
3. Lazy load related data
4. Implement pagination
5. Minify CSS/JS assets
6. Enable Gzip compression
7. Add CDN for static assets

---

## 🛡️ COMPLIANCE & STANDARDS

### Security Standards
- ✅ OWASP Top 10 (partial compliance)
- ✅ Password hashing (bcrypt)
- ✅ CSRF protection
- ✅ SQL injection prevention
- ❌ No HTTPS enforcement
- ❌ No security headers (CSP, HSTS)

### Code Standards
- ✅ PSR-4 autoloading
- ⚠️ PSR-12 coding style (not enforced)
- ❌ No PHPStan/Psalm static analysis
- ❌ No PHP CS Fixer

### Data Privacy
- ⚠️ No GDPR compliance features
- ⚠️ No data retention policy
- ⚠️ No user data export/deletion
- ✅ Soft deletes (data retention)

---

## 📞 SUPPORT & MAINTENANCE

### Monitoring
- **Error Logging:** File-based (`storage/logs/`)
- **Activity Logging:** Database (`activity_logs` table)
- **No Uptime Monitoring:** No external service
- **No Performance Monitoring:** No APM

### Backup Strategy
- **Database:** Manual export via phpMyAdmin
- **Files:** Manual FTP download
- **No Automated Backups:** Must be set up manually

### Update Process
1. Test changes locally
2. Run tests (`vendor/bin/phpunit`)
3. Upload via FTP (FileZilla)
4. Verify on production
5. Monitor logs for errors

---

## 🎓 LEARNING RESOURCES

### For New Developers
- **PHP 8.2 Documentation:** https://www.php.net/manual/en/
- **PDO Tutorial:** https://phpdelusions.net/pdo
- **MVC Pattern:** https://www.tutorialspoint.com/mvc_framework/
- **RESTful API Design:** https://restfulapi.net/

### For System Admins
- **InfinityFree Docs:** https://forum.infinityfree.com/
- **FileZilla Guide:** https://filezilla-project.org/
- **MySQL Optimization:** https://dev.mysql.com/doc/

---

## 📝 CHANGELOG

### v1.0.0 (May 27, 2026)
- ✅ Initial deployment to InfinityFree
- ✅ Fixed session handling for shared hosting
- ✅ Fixed `.htaccess` routing
- ✅ Fixed database connection (correct password)
- ✅ Added session regeneration on login
- ✅ Cleaned up test files and documentation

---

## 🏁 CONCLUSION

**HealthChain** is a well-structured, custom-built PHP MVC application with solid fundamentals. The codebase demonstrates good separation of concerns, security awareness, and practical feature implementation. However, it lacks some modern development practices (DI, service layer, comprehensive testing) and has technical debt that should be addressed for long-term maintainability.

**Overall Grade:** B+ (Good, with room for improvement)

**Recommended Next Steps:**
1. Implement API token hashing
2. Add database transactions
3. Upgrade to HTTPS
4. Implement caching
5. Add comprehensive tests
6. Document API endpoints
7. Consider framework migration (Laravel) for future scalability

---

**End of Analysis**
