<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Models\User;
use App\Models\ApiToken;

class AuthApiController extends BaseController
{
    public function token(Request $request)
    {
        $data = $request->isJson() ? $request->json() : $request->all();

        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['success' => false, 'message' => 'Email and password required'], 422);
        }

        $user = (new User())->authenticate($data['email'], $data['password']);

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $token     = (new ApiToken())->generate((int) $user['id']);
        $expiresAt = date('Y-m-d\TH:i:s\Z', strtotime('+24 hours'));

        return $this->json([
            'success'    => true,
            'token'      => $token,
            'user'       => $user,
            'expires_at' => $expiresAt,
        ]);
    }
}
