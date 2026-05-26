<?php

namespace App\Models;

use App\Core\Logger;

class ApiToken extends BaseModel
{
    protected string $table = 'api_tokens';

    protected array $fillable = [
        'user_id', 'token', 'expires_at', 'scope', 'scopes', 'name',
        'last_used_at', 'last_used_ip', 'is_revoked', 'revoked_at', 'revoked_by'
    ];

    // Simple scope constants (backwards-compatible)
    public const SCOPE_READ  = 'read';
    public const SCOPE_WRITE = 'write';
    public const SCOPE_ADMIN = 'admin';

    public const VALID_SCOPES = [self::SCOPE_READ, self::SCOPE_WRITE, self::SCOPE_ADMIN];

    // Granular scope constants
    const SCOPE_READ_MEDICINES  = 'medicines:read';
    const SCOPE_WRITE_MEDICINES = 'medicines:write';
    const SCOPE_READ_BATCHES    = 'batches:read';
    const SCOPE_WRITE_BATCHES   = 'batches:write';
    const SCOPE_READ_STOCKS     = 'stocks:read';
    const SCOPE_WRITE_STOCKS    = 'stocks:write';
    const SCOPE_READ_USERS      = 'users:read';
    const SCOPE_WRITE_USERS     = 'users:write';
    const SCOPE_ALL             = '*';

    // Role-based default scopes
    const ROLE_SCOPES = [
        'superadmin' => [self::SCOPE_ALL],
        'manager' => [
            self::SCOPE_READ_MEDICINES,
            self::SCOPE_WRITE_MEDICINES,
            self::SCOPE_READ_BATCHES,
            self::SCOPE_WRITE_BATCHES,
            self::SCOPE_READ_STOCKS,
            self::SCOPE_WRITE_STOCKS,
            self::SCOPE_READ_USERS,
        ],
        'staff' => [
            self::SCOPE_READ_MEDICINES,
            self::SCOPE_READ_BATCHES,
            self::SCOPE_READ_STOCKS,
        ],
    ];

    // Which scopes satisfy a required scope
    private const SCOPE_HIERARCHY = [
        self::SCOPE_READ  => [self::SCOPE_READ, self::SCOPE_WRITE, self::SCOPE_ADMIN],
        self::SCOPE_WRITE => [self::SCOPE_WRITE, self::SCOPE_ADMIN],
        self::SCOPE_ADMIN => [self::SCOPE_ADMIN],
    ];

    public function generate(int $userId, int $expiryHours = 24, string $scope = self::SCOPE_READ, ?array $scopes = null, ?string $name = null): string
    {
        if (!in_array($scope, self::VALID_SCOPES, true)) {
            $scope = self::SCOPE_READ;
        }

        $token     = generate_api_token();
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryHours} hours"));

        // Get user to determine default granular scopes
        $user = (new User())->find($userId);

        if ($scopes === null && $user) {
            $scopes = self::ROLE_SCOPES[$user['role']] ?? [self::SCOPE_READ_MEDICINES];
        }

        $this->create([
            'user_id'    => $userId,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'scope'      => $scope,
            'scopes'     => json_encode($scopes),
            'name'       => $name ?? 'API Token',
            'is_revoked' => 0,
        ]);

        Logger::info('API token generated', [
            'user_id'    => $userId,
            'scope'      => $scope,
            'scopes'     => $scopes,
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }

    public function verify(string $token): ?array
    {
        $result = $this->query()
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->where('is_revoked', 0)
            ->first();

        if (!$result) {
            return null;
        }

        // Update last used timestamp and IP
        $this->update($result['id'], [
            'last_used_at' => date('Y-m-d H:i:s'),
            'last_used_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $user = (new User())->find((int) $result['user_id']);

        if (!$user) {
            return null;
        }

        // Attach both scope systems
        $user['scope']        = $result['scope'] ?? self::SCOPE_READ;
        $user['token_scopes'] = json_decode($result['scopes'] ?? '[]', true);
        $user['token_id']     = $result['id'];

        return $user;
    }

    /**
     * Check if the token's simple scope satisfies the required scope.
     */
    public static function scopeAllows(string $tokenScope, string $requiredScope): bool
    {
        return in_array($tokenScope, self::SCOPE_HIERARCHY[$requiredScope] ?? [], true);
    }

    /**
     * Check if token has specific granular scope
     */
    public function hasScope(string $token, string $scope): bool
    {
        $result = $this->query()
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->where('is_revoked', 0)
            ->first();

        if (!$result) {
            return false;
        }

        $scopes = json_decode($result['scopes'] ?? '[]', true);

        if (in_array(self::SCOPE_ALL, $scopes)) {
            return true;
        }

        if (in_array(self::SCOPE_ADMIN, $scopes) && strpos($scope, 'admin:') === 0) {
            return true;
        }

        return in_array($scope, $scopes);
    }

    /**
     * Revoke a token
     */
    public function revoke(int $tokenId, int $revokedBy): bool
    {
        $updated = $this->update($tokenId, [
            'is_revoked' => 1,
            'revoked_at' => date('Y-m-d H:i:s'),
            'revoked_by' => $revokedBy,
        ]);

        if ($updated) {
            Logger::warning('API token revoked', [
                'token_id'   => $tokenId,
                'revoked_by' => $revokedBy,
            ]);
        }

        return $updated;
    }

    /**
     * Revoke all tokens for a user
     */
    public function revokeAllForUser(int $userId, int $revokedBy): int
    {
        $sql = "
            UPDATE api_tokens
            SET is_revoked = 1,
                revoked_at = NOW(),
                revoked_by = ?
            WHERE user_id = ?
              AND is_revoked = 0
        ";

        $stmt  = \App\Core\Database::query($sql, [$revokedBy, $userId]);
        $count = $stmt->rowCount();

        if ($count > 0) {
            Logger::warning('All API tokens revoked for user', [
                'user_id'    => $userId,
                'count'      => $count,
                'revoked_by' => $revokedBy,
            ]);
        }

        return $count;
    }

    /**
     * Get all tokens for a user
     */
    public function getTokensForUser(int $userId): array
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Get active tokens for a user
     */
    public function getActiveTokensForUser(int $userId): array
    {
        $sql = "
            SELECT * FROM api_tokens
            WHERE user_id = ?
              AND is_revoked = 0
              AND expires_at > NOW()
            ORDER BY created_at DESC
        ";

        return \App\Core\Database::query($sql, [$userId])->fetchAll(\PDO::FETCH_ASSOC);
    }
}
