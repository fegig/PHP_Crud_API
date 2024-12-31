<?php

declare(strict_types=1);

namespace Utility\Security;

class Middleware
{
    
    /**
     * Authenticate the request
     * @return callable The middleware function
     */
    public static function authData(): callable
    {
        return function ($route) {
            // Perform authentication checks
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception('Authentication failed');
            }
            
            // If validation passes, execute the route
            return $route;
        };
    }


    // Example of additional middleware methods
    public static function rateLimiting(): callable
    {
        return function ($route) {
            // Your rate limiting logic here
            return $route;
        };
    }

    public static function adminOnly(): callable
    {
        return function ($route) {
            if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
                throw new \Exception('Admin access required');
            }
            return $route;
        };
    }
}
