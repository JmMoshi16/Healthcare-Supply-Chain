<?php

namespace App\Models;

class ApiToken extends BaseModel
{
    protected string $table = 'api_tokens';

    protected array $fillable = ['user_id', 'token', 'expires_at', 'scope'];

    // Available scopes
    public const SCOPE_READ  = 'read';
    public const SCOPE_WRITE = 'write';
    public const SCOPE_ADMIN = 'admin';

    public const VALID_SCOPES = [self::SCOPE_READ, self::SCOPE_WRITE, self::SCOPE_ADMIN];

    // Which scopes satisfy a required scope
    private const SCOPE_HIERARCHY = [
        self::SCOPE_READ  => [self::SCOPE_READ, self::SCOPE_WRITE, self::SCOPE_ADMIN],
        self::SCOPE_WRITE => [self::SCOPE_WRITE, self::SCOPE_ADMIN],
        self::SCOPE_ADMIN => [self::SCOPE_ADMIN],
    ];

    public function generate(int $userId, int $expiryHours = 24, string $scope = self::SCOPE_READ): string
    {
        if (!in_array($scope, self::VALID_SCOPES, true)) {
            $scope = self::SCOPE_READ;
        }

        $token     = generate_api_token();
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryHours} hours"));

        $this->create([
            'user_id'    => $userId,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'scope'      => $scope,
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

        $user          = (new User())->find((int) $result['user_id']);
        $user['scope'] = $result['scope'] ?? self::SCOPE_READ;

        return $user;
    }

    /**
     * Check if the token's scope satisfies the required scope.
     */
    public static function scopeAllows(string $tokenScope, string $requiredScope): bool
    {
        return in_array($tokenScope, self::SCOPE_HIERARCHY[$requiredScope] ?? [], true);
    }
}
