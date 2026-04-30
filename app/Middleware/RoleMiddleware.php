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
