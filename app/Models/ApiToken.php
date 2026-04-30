<?php

namespace App\Models;

class ApiToken extends BaseModel
{
    protected string $table = 'api_tokens';

    protected array $fillable = ['user_id', 'token', 'expires_at'];

    public function generate(int $userId, int $expiryHours = 24): string
    {
        $token     = generate_api_token();
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryHours} hours"));

        $this->create([
            'user_id'    => $userId,
            'token'      => $token,
            'expires_at' => $expiresAt,
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

        return (new User())->find((int) $result['user_id']);
    }
}
