<?php

declare(strict_types=1);

use Utility\Connection\Query;

require_once __DIR__ . '/../../vendor/autoload.php';

// Get all model files from the Models directory
$modelDirectory = __DIR__ . '/../../../app/Models';
$modelFiles = glob($modelDirectory . '/*.php');

try {
    foreach ($modelFiles as $modelFile) {
        // Get the class name from the file name
        $className = 'App\\Models\\' . basename($modelFile, '.php');
        
        // Skip if class doesn't exist
        if (!class_exists($className)) {
            continue;
        }

        // Get table information from the model
        $model = new $className();
        
        // Check if required constants exist
        if (!defined("$className::TABLE_NAME") || !defined("$className::TABLE_COLUMNS")) {
            echo "Skipping $className: Missing TABLE_NAME or TABLE_COLUMNS constants\n";
            continue;
        }

        try {
            $tableName = $className::TABLE_NAME;
            $tableColumns = $className::TABLE_COLUMNS;
            
            $sql = Query::table($tableName)
                ->create($tableColumns);

            if ($sql) {
                echo "Successfully created table: $tableName\n";
            } else {
                echo "Failed to create table: $tableName\n";
            }
        } catch (PDOException $e) {
            echo "Error creating table $tableName: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 