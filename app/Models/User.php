<?php

namespace App\Models;

class User extends BaseModel
{
    protected string $table = 'users';
    protected bool $useSoftDeletes = true;
    protected bool $logActivities = true;

    protected array $fillable = [
        'fullname',
        'email',
        'password',
        'role',
        'profile_image',
        'is_active',
    ];

    protected array $hidden = ['password'];

    public function findByEmail(string $email): ?array
    {
        return $this->query()
            ->where('email', strtolower(trim($email)))
            ->first();
    }

    /**
     * Authenticate by email + password.
     * Returns user array (without password) or null on failure.
     */
    public function authenticate(string $email, string $password): ?array
    {
        try {
            $email = strtolower(trim($email));

            // Fetch raw row (including password hash) directly via PDO
            $stmt = \App\Core\Database::query(
                "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1",
                [$email]
            );
            $raw = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$raw) {
                return null;
            }

            if (!verify_password($password, $raw['password'])) {
                return null;
            }

            if (!(int)$raw['is_active']) {
                return null;
            }

            return $this->hideFields($raw);
        } catch (\Throwable $e) {
            // Database error during authentication
            return null;
        }
    }

    public function create(array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = hash_password($data['password']);
        }

        if (isset($data['email'])) {
            $data['email'] = strtolower(trim($data['email']));
        }

        return parent::create($data);
    }

    public function update(int $id, array $data): bool
    {
        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = hash_password($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['email'])) {
            $data['email'] = strtolower(trim($data['email']));
        }

        return parent::update($id, $data);
    }

    public function getUserCount(): int
    {
        $result = \App\Core\Database::query("SELECT COUNT(*) as count FROM users")
            ->fetch(\PDO::FETCH_ASSOC);
        
        return (int)($result['count'] ?? 0);
    }
}
