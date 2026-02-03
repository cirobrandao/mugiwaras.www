<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->add('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->add('POST', $path, $handler, $middlewares);
    }

    public function add(string $method, string $path, mixed $handler, array $middlewares = []): void
    {
        $this->routes[] = compact('method', 'path', 'handler', 'middlewares');
    }

    public function dispatch(Request $request): void
    {
        $uri = parse_url($request->uri, PHP_URL_PATH) ?: '/';
        $basePath = rtrim((string)Config::get('app.base_path', ''), '/');
        if ($basePath !== '' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath)) ?: '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method) {
                continue;
            }
            $params = [];
            if ($this->match($route['path'], $uri, $params)) {
                foreach ($route['middlewares'] as $mw) {
                    $mw($request);
                }
                $handler = $route['handler'];
                if (!is_callable($handler)) {
                    Logger::error('route_not_callable', ['path' => $route['path'], 'method' => $route['method']]);
                    http_response_code(500);
                    echo 'Route handler not callable.';
                    return;
                }
                $args = $params;
                $paramCount = null;
                if (is_array($handler) && count($handler) === 2) {
                    $ref = new \ReflectionMethod($handler[0], $handler[1]);
                    $paramCount = $ref->getNumberOfParameters();
                } elseif (is_object($handler) && ($handler instanceof \Closure)) {
                    $ref = new \ReflectionFunction($handler);
                    $paramCount = $ref->getNumberOfParameters();
                }

                if ($paramCount !== null && $paramCount > 0) {
                    $args = array_merge([$request], $params);
                }

                call_user_func_array($handler, $args);
                return;
            }
        }

        http_response_code(404);
        echo View::render('errors/404');
    }

    private function match(string $routePath, string $uri, array &$params): bool
    {
        $pattern = preg_replace('#\{([^/]+)\}#', '([^/]+)', $routePath);
        $pattern = '#^' . rtrim($pattern, '/') . '/?$#';
        if (!preg_match($pattern, $uri, $matches)) {
            return false;
        }
        array_shift($matches);
        $params = $matches;
        return true;
    }
}
