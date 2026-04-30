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
