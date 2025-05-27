<?php

namespace App\Core;

use App\Config\Config;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private string $apiPrefix;
    private string $apiVersion;

    public function __construct()
    {
        $config = Config::getInstance();
        $apiConfig = $config->get('api');
        
        $this->apiPrefix = $apiConfig['prefix'];
        $this->apiVersion = $apiConfig['version'];
    }

    public function addRoute(string $method, string $path, array $handler, ?string $middleware = null): void
    {
        // Remove any existing /api prefix and version from the path
        $path = preg_replace('/^\/api\/v\d+\//', '', $path);
        
        // Remove leading slash from path
        $path = ltrim($path, '/');
        
        // Add the global prefix and version
        $fullPath = "/{$this->apiPrefix}/{$this->apiVersion}/{$path}";
        
        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function addMiddleware(callable $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Matches a route pattern against a request path and extracts route parameters.
     * 
     * This method converts route patterns with parameters (e.g., '/users/{id}') into
     * regex patterns and attempts to match them against the actual request path.
     * 
     * @param string $routePath    The route pattern (e.g., '/users/{id}/posts/{postId}')
     * @param string $requestPath  The actual request path (e.g., '/users/123/posts/456')
     * 
     * @return array|null Returns an array of matched parameters if the route matches
     *                    (e.g., ['id' => '123', 'postId' => '456']),
     *                    or null if no match is found
     */
    private function matchRoute(string $routePath, string $requestPath): ?array
    {
        // Convert route parameters to regex pattern
        $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        if (preg_match($pattern, $requestPath, $matches)) {
            // Filter out numeric keys
            return array_filter($matches, function($key) {
                return !is_numeric($key);
            }, ARRAY_FILTER_USE_KEY);
        }

        return null;
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Run middlewares
        foreach ($this->middlewares as $middleware) {
            $middleware();
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $params = $this->matchRoute($route['path'], $path);
                if ($params !== null) {
                    // Handle middleware if present
                    if ($route['middleware']) {
                        $middlewareClass = $route['middleware'];
                        $middleware = new $middlewareClass();
                        if (!$middleware->handle()) {
                            return;
                        }
                    }

                    // Handle the route
                    [$controllerClass, $action] = $route['handler'];
                    $controller = new $controllerClass();
                    
                    // Call the action with parameters if they exist
                    if (!empty($params)) {
                        $controller->$action($params);
                    } else {
                        $controller->$action();
                    }
                    return;
                }
            }
        }

        Response::sendError(
            sprintf('Endpoint %s %s not found', $method, $path),
            404,
            [],
            'ROUTE_NOT_FOUND'
        );
    }
}
