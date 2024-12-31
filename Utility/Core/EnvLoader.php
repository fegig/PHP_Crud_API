<?php
namespace Utility\Core;

class EnvLoader {
   public static function LoadEnv() {
    $envFile = __DIR__ . '/../../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                    putenv(sprintf('%s=%s', $name, $value));
                }
            }
        }
    }
}
}
