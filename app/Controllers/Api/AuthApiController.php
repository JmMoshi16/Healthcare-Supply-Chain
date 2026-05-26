<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Validator;
use App\Models\User;
use App\Models\ApiToken;

class AuthApiController extends BaseController
{
    public function token(Request $request)
    {
        $data = $request->isJson() ? $request->json() : $request->all();

        $validator = new Validator($data);
        if (!$validator->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ])) {
            return $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = (new User())->authenticate($data['email'], $data['password']);

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        // Determine scope from request; default to 'read'
        $requestedScope = $data['scope'] ?? ApiToken::SCOPE_READ;
        if (!in_array($requestedScope, ApiToken::VALID_SCOPES, true)) {
            $requestedScope = ApiToken::SCOPE_READ;
        }

        $token     = (new ApiToken())->generate((int) $user['id'], 24, $requestedScope);
        $expiresAt = date('Y-m-d\\TH:i:s\\Z', strtotime('+24 hours'));

        return $this->json([
            'success'    => true,
            'token'      => $token,
            'scope'      => $requestedScope,
            'user'       => $user,
            'expires_at' => $expiresAt,
        ]);
    }
}
