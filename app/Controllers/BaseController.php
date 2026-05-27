<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Response;

abstract class BaseController
{
    protected function view(string $view, array $data = []): Response
    {
        $content = View::render($view, $data);
        return new Response($content);
    }

    /**
     * Redirect using an absolute URL.
     * InfinityFree's mod_rewrite can loop on bare /path redirects,
     * so we always prepend APP_URL to produce a full http://... URL.
     */
    protected function redirect(string $url): Response
    {
        $baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        // If already absolute (http/https), use as-is
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            $absoluteUrl = $url;
        } else {
            $absoluteUrl = $baseUrl . '/' . ltrim($url, '/');
        }
        return (new Response())->redirect($absoluteUrl);
    }

    protected function json(array $data, int $status = 200): Response
    {
        return (new Response())->json($data, $status);
    }
}
