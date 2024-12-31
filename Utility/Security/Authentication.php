<?php
declare(strict_types=1);

namespace Utility\Security;

use Exception;
use Utility\Core\EnvLoader;

class Authentication
{
    public static function validateRequest(): void
    {
        EnvLoader::LoadEnv();
       
        self::handleCORS();
        
        $requiredHeaders = ['X-API-KEY', 'User-Agent'];
        foreach ($requiredHeaders as $header) {
            if (!isset($_SERVER['HTTP_' . str_replace('-', '_', strtoupper($header))])) {
                http_response_code(400);
                exit(json_encode(['error' => "Missing required header: $header"]));
            }
        }

        // Validate API key
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        if (!$apiKey || !self::isValidApiKey($apiKey)) {
            http_response_code(401);
            exit(json_encode(['error' => 'Invalid or missing API key']));
        }

        // Add more security checks as needed (e.g., rate limiting, IP whitelisting)
    }

    private static function handleCORS(): void
    {
        // Allow requests from any origin
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, X-API-KEY, User-Agent");

        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    private static function isValidApiKey(?string $apiKey): bool
    {
        if ($apiKey === null) {
            return false;
        }
        $envApiKey = $_ENV['API_KEY'] ?? throw new Exception('API_KEY environment variable is not set');
        
        if (!$envApiKey) {
            error_log('API_KEY not set in .env file');
            return false;
        }

        return hash_equals($envApiKey, $apiKey);
    }

    public static function group(callable $middleware, array $routes): void
    {
        foreach ($routes as $route) {
            $middleware($route);
        }
    }

}
