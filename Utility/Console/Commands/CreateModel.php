<?php

declare(strict_types=1);

namespace Utility\Console\Commands;

class CreateModel
{
    public static function create(string $name): void
    {
        $modelDir = __DIR__ . '/../../../app/Models';
        $modelName = ucfirst($name) . 'Model';
        $path = "{$modelDir}/{$modelName}.php";

        if (file_exists($path)) {
            echo "Model already exists!\n";
            return;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Models;

class {$modelName}
{
    public const TABLE_NAME = '{$modelName}';
    public const TABLE_COLUMNS = [];
}
PHP;

        if (!is_dir($modelDir)) {
            mkdir($modelDir, 0777, true);
        }

        file_put_contents($path, $stub);
        echo "Model {$modelName} created successfully!\n";
    }
} 