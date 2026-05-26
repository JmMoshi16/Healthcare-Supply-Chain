<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $groupMiddlewares = [];
    private string $groupPrefix = '';

    public function get(string $uri, $action, array $middleware = []): void
    {
        $this->addRoute('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, $action, array $middleware = []): void
    {
        $this->addRoute('POST', $uri, $action, $middleware);
    }

    public function put(string $uri, $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $uri, $action, $middleware);
    }

    public function delete(string $uri, $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $uri, $action, $middleware);
    }

    public function resource(string $name, string $controller): void
    {
        $this->get("/{$name}", [$controller, 'index']);
        $this->get("/{$name}/create", [$controller, 'create']);
        $this->post("/{$name}", [$controller, 'store']);
        $this->get("/{$name}/{id}", [$controller, 'show']);
        $this->get("/{$name}/{id}/edit", [$controller, 'edit']);
        $this->put("/{$name}/{id}", [$controller, 'update']);
        $this->delete("/{$name}/{id}", [$controller, 'destroy']);
    }

    public function group(array $attributes, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddlewares = $this->groupMiddlewares;

        if (isset($attributes['prefix'])) {
            $this->groupPrefix = $previousPrefix . '/' . trim($attributes['prefix'], '/');
        }

        if (isset($attributes['middleware'])) {
            $this->groupMiddlewares = array_merge(
                $this->groupMiddlewares,
                (array) $attributes['middleware']
            );
        }

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddlewares = $previousMiddlewares;
    }

    private function addRoute(string $method, string $uri, $action, array $middleware = []): void
    {
        $uri = $this->groupPrefix . '/' . trim($uri, '/');
        $uri = '/' . trim($uri, '/');

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'middleware' => array_merge($this->groupMiddlewares, $middleware)
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertToRegex($route['uri']);
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                
                foreach ($route['middleware'] as $middleware) {
                    $middlewareInstance = is_object($middleware) ? $middleware : new $middleware();
                    $middlewareResponse = $middlewareInstance->handle($request);
                    
                    if ($middlewareResponse !== null) {
                        return $middlewareResponse;
                    }
                }

                return $this->executeAction($route['action'], $matches, $request);
            }
        }

        return new Response(['error' => 'Route not found'], 404);
    }

    private function convertToRegex(string $uri): string
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    private function executeAction($action, array $params, Request $request)
    {
        if (is_callable($action)) {
            return call_user_func_array($action, array_merge([$request], $params));
        }

        if (is_array($action)) {
            [$controller, $method] = $action;
            
            if (is_string($controller)) {
                $controller = new $controller();
            }

            if (!method_exists($controller, $method)) {
                return new Response(['error' => 'Method not found'], 404);
            }

            return call_user_func_array([$controller, $method], array_merge([$request], $params));
        }

        return new Response(['error' => 'Invalid action'], 500);
    }
}
