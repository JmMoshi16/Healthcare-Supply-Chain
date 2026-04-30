# Healthcare Supply Chain - Complete Setup Script

This file contains all the code you need to complete the project.
Copy each section to its respective file.

## MODELS

### app/Models/User.php
```php
<?php
namespace App\Models;

class User extends BaseModel
{
    protected string $table = 'users';
    
    protected array $fillable = [
        'fullname', 'email', 'password', 'role', 'profile_image', 'is_active'
    ];
    
    protected array $hidden = ['password'];
    
    public function findByEmail(string $email): ?array
    {
        return $this->query()->where('email', $email)->first();
    }
    
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        
        if (!$user || !verify_password($password, $user['password'])) {
            return null;
        }
        
        if (!$user['is_active']) {
            return null;
        }
        
        return $this->hideFields($user);
    }
    
    public function create(array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = hash_password($data['password']);
        }
        
        return parent::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = hash_password($data['password']);
        } else {
            unset($data['password']);
        }
        
        return parent::update($id, $data);
    }
}
```

### app/Models/Medicine.php
```php
<?php
namespace App\Models;

class Medicine extends BaseModel
{
    protected string $table = 'medicines';
    
    protected array $fillable = [
        'name', 'generic_name', 'category', 'description', 'unit', 'image', 'is_active'
    ];
    
    public function withBatches(int $id): ?array
    {
        $medicine = $this->find($id);
        
        if (!$medicine) {
            return null;
        }
        
        $batches = (new Batch())->query()
            ->where('medicine_id', $id)
            ->orderBy('expiry_date', 'ASC')
            ->get();
        
        $medicine['batches'] = $batches;
        $medicine['total_stock'] = array_sum(array_column($batches, 'current_quantity'));
        
        return $medicine;
    }
    
    public function getLowStock(int $threshold = 10): array
    {
        $sql = "
            SELECT m.*, SUM(b.current_quantity) as total_stock
            FROM medicines m
            LEFT JOIN batches b ON b.medicine_id = m.id AND b.status = 'active'
            WHERE m.is_active = 1
            GROUP BY m.id
            HAVING total_stock < ?
            ORDER BY total_stock ASC
        ";
        
        return \App\Core\Database::query($sql, [$threshold])->fetchAll();
    }
}
```

### app/Models/Batch.php
```php
<?php
namespace App\Models;

class Batch extends BaseModel
{
    protected string $table = 'batches';
    
    protected array $fillable = [
        'medicine_id', 'batch_number', 'manufacturing_date', 'expiry_date',
        'supplier', 'purchase_price', 'selling_price', 'initial_quantity',
        'current_quantity', 'status'
    ];
    
    public function create(array $data): int
    {
        if (!isset($data['current_quantity'])) {
            $data['current_quantity'] = $data['initial_quantity'];
        }
        
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }
        
        return parent::create($data);
    }
    
    public function getExpiringSoon(int $days = 30): array
    {
        $sql = "
            SELECT b.*, m.name as medicine_name, m.generic_name
            FROM batches b
            JOIN medicines m ON m.id = b.medicine_id
            WHERE b.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
            AND b.status = 'active'
            AND b.current_quantity > 0
            ORDER BY b.expiry_date ASC
        ";
        
        return \App\Core\Database::query($sql, [$days])->fetchAll();
    }
    
    public function updateStock(int $id, int $quantity, string $type = 'out'): bool
    {
        $batch = $this->find($id);
        
        if (!$batch) {
            return false;
        }
        
        $newQuantity = $type === 'out' 
            ? $batch['current_quantity'] - $quantity
            : $batch['current_quantity'] + $quantity;
        
        if ($newQuantity < 0) {
            throw new \Exception('Insufficient stock');
        }
        
        return $this->update($id, ['current_quantity' => $newQuantity]);
    }
}
```

### app/Models/Stock.php
```php
<?php
namespace App\Models;

class Stock extends BaseModel
{
    protected string $table = 'stocks';
    
    protected array $fillable = [
        'batch_id', 'transaction_type', 'quantity', 'reason', 'performed_by'
    ];
    
    public function create(array $data): int
    {
        $batchModel = new Batch();
        
        $batchModel->updateStock(
            $data['batch_id'],
            $data['quantity'],
            $data['transaction_type']
        );
        
        return parent::create($data);
    }
    
    public function getRecentTransactions(int $limit = 10): array
    {
        $sql = "
            SELECT s.*, 
                   b.batch_number,
                   m.name as medicine_name,
                   u.fullname as performed_by_name
            FROM stocks s
            JOIN batches b ON b.id = s.batch_id
            JOIN medicines m ON m.id = b.medicine_id
            JOIN users u ON u.id = s.performed_by
            ORDER BY s.created_at DESC
            LIMIT ?
        ";
        
        return \App\Core\Database::query($sql, [$limit])->fetchAll();
    }
}
```

### app/Models/ApiToken.php
```php
<?php
namespace App\Models;

class ApiToken extends BaseModel
{
    protected string $table = 'api_tokens';
    
    protected array $fillable = ['user_id', 'token', 'expires_at'];
    
    public function generate(int $userId, int $expiryHours = 24): string
    {
        $token = generate_api_token();
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryHours} hours"));
        
        $this->create([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
        
        return $token;
    }
    
    public function verify(string $token): ?array
    {
        $result = $this->query()
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
        
        if (!$result) {
            return null;
        }
        
        $user = (new User())->find($result['user_id']);
        return $user;
    }
}
```

## MIDDLEWARE

### app/Middleware/AuthMiddleware.php
```php
<?php
namespace App\Middleware;
use App\Core\Request;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(Request $request): ?Response
    {
        if (!is_logged_in()) {
            flash('error', 'Please login to continue');
            return (new Response())->redirect('/login');
        }
        
        return null;
    }
}
```

### app/Middleware/RoleMiddleware.php
```php
<?php
namespace App\Middleware;
use App\Core\Request;
use App\Core\Response;

class RoleMiddleware
{
    private string $requiredRole;
    
    public function __construct(string $role = 'superadmin')
    {
        $this->requiredRole = $role;
    }
    
    public function handle(Request $request): ?Response
    {
        if (!is_logged_in()) {
            return (new Response())->redirect('/login');
        }
        
        if (!has_role($this->requiredRole)) {
            flash('error', 'You do not have permission to access this resource');
            return (new Response())->redirect('/dashboard');
        }
        
        return null;
    }
}
```

### app/Middleware/CsrfMiddleware.php
```php
<?php
namespace App\Middleware;
use App\Core\Request;
use App\Core\Response;

class CsrfMiddleware
{
    public function handle(Request $request): ?Response
    {
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            $token = $request->post('csrf_token');
            
            if (!$token || !verify_csrf($token)) {
                flash('error', 'CSRF token validation failed');
                return (new Response())->redirect('/');
            }
        }
        
        return null;
    }
}
```

### app/Middleware/ApiAuthMiddleware.php
```php
<?php
namespace App\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Models\ApiToken;

class ApiAuthMiddleware
{
    public function handle(Request $request): ?Response
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return (new Response())->json([
                'success' => false,
                'message' => 'Authorization token required'
            ], 401);
        }
        
        $apiToken = new ApiToken();
        $user = $apiToken->verify($token);
        
        if (!$user) {
            return (new Response())->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }
        
        $request->user = $user;
        
        return null;
    }
}
```

## CONTINUE IN NEXT FILE...
This is Part 1 of the setup. See SETUP_PART2.md for Controllers, Views, and Configuration.
