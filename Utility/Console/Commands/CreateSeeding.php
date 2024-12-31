<?php

declare(strict_types=1);

namespace Utility\Console\Commands;

class CreateSeeding
{
    public static function create(string $modelName): void
    {
        $seedingScript = __DIR__ . '/../seeding/' . $modelName . 'Seeder.php';
        
        if (!file_exists($seedingScript)) {
            echo "Error: Seeding script not found for model '{$modelName}' at {$seedingScript}\n";
            exit(1);
        }

        try {
            require_once $seedingScript;
            $seederClass = $modelName . 'Seeder';
            
            if (class_exists($seederClass)) {
                $seeder = new $seederClass();
                $seeder->run();
                echo "Successfully seeded {$modelName} data\n";
            } else {
                echo "Error: Seeder class '{$seederClass}' not found in {$seedingScript}\n";
                exit(1);
            }
        } catch (\Throwable $e) {
            echo "Error during seeding: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
} 