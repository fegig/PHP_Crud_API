<?php

declare(strict_types=1);

namespace Utility\Core;

use Exception;
use InvalidArgumentException;
use RuntimeException;

class Router
{
    private array $routes = [];

    public function addRoute(string $method, string $path, string $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, string $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, string $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = trim($path, '/');

        // Handle different content types
        if ($method === 'POST' || $method === 'PUT') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            
            // Handle JSON data
            if (strpos($contentType, 'application/json') !== false) {
                $this->handleJsonData();
            }
           
        }

        // Check for exact match first
        if (isset($this->routes[$method][$path])) {
            $this->executeHandler($this->routes[$method][$path], []);
            return;
        }

        // Check for routes with parameters
        foreach ($this->routes[$method] as $routePath => $handler) {
            $pattern = $this->convertRouteToRegex($routePath);
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove the full match
                $this->executeHandler($handler, $matches);
                return;
            }
        }

        // No route found
        http_response_code(404);
        echo "No route found";
    }

    private function handleJsonData(): void
    {
        try {
            $jsonData = file_get_contents('php://input');
            if (empty($jsonData)) {
                return; // Skip if no data
            }

            $data = json_decode($jsonData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('Invalid JSON data');
            }

            $_POST = array_merge($_POST, $data); // Merge JSON data with existing $_POST
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred while processing the request']);
            exit;
        }
    }

    private function convertRouteToRegex(string $route): string
    {
        return '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route) . '$#';
    }

    private function executeHandler(string $handler, array $params = []): void
    {
        // Check if the handler is already a fully qualified class name
        if (strpos($handler, '@') !== false) {
            [$className, $action] = explode('@', $handler);
            
            // If the class name doesn't start with a namespace, prepend the default namespace
            if (strpos($className, '\\') === false) {
                $className = 'App\\Controllers\\' . $className;
            }
            
            if (!class_exists($className)) {
                throw new RuntimeException("Controller class {$className} not found");
            }
            
            $controllerInstance = new $className();
            
            // If it's a POST or PUT request, merge $_FILES with $_POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
                $params[] = array_merge($_POST, ['image' => $_FILES['image'] ?? null]);
            }
            
            $controllerInstance->$action(...$params);
        }
    }
}
