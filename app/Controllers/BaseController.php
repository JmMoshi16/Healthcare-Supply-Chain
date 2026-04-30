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

    protected function redirect(string $url): Response
    {
        return (new Response())->redirect($url);
    }

    protected function json(array $data, int $status = 200): Response
    {
        return (new Response())->json($data, $status);
    }
}
