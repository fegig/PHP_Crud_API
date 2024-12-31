<?php

declare(strict_types=1);

/**
 * Application Entry Point
 */

// Load Composer autoloader
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Composer dependencies not installed. Please run "composer install".');
}
require __DIR__ . '/vendor/autoload.php';

use Utility\Core\EnvLoader;
EnvLoader::LoadEnv();

try {
    require_once __DIR__ . '/utility/Helpers/request_type.php';
    
    if (!empty($request_uri)) {
        include __DIR__ . '/routes/api.php';
    } else {
        include __DIR__ . '/routes/web.php';
    }

} catch (\Throwable $e) {

    error_log($e->getMessage());
}
