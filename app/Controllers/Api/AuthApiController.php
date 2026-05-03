<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\ApiValidator;
use App\Core\Logger;
use App\Models\User;
use App\Models\ApiToken;

class AuthApiController extends BaseController
{
    use ApiValidator;
    
    public function token(Request $request)
    {
        // Validate input
        $data = $this->validateRequest($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required|min:6|max:255',
        ]);
        
        // Additional XSS prevention
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        
        // Authenticate user
        $user = (new User())->authenticate($email, $data['password']);

        if (!$user) {
            // Log failed attempt
            Logger::warning('API authentication failed', [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);
            
            return $this->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }
        
        // Generate token
        $token     = (new ApiToken())->generate((int) $user['id']);
        $expiresAt = date('Y-m-d\TH:i:s\Z', strtotime('+24 hours'));
        
        // Log successful authentication
        Logger::info('API token generated', [
            'user_id' => $user['id'],
            'email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);

        return $this->json([
            'success'    => true,
            'token'      => $token,
            'user'       => $user,
            'expires_at' => $expiresAt,
        ]);
    }
}
