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
