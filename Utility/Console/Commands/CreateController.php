<?php

declare(strict_types=1);

namespace Utility\Console\Commands;

class CreateController
{
    public static function create(string $name): void
    {
        $controllerDir = __DIR__ . '/../../../app/Controllers';
        $controllerName = ucfirst($name) . 'Controller';
        $path = "{$controllerDir}/{$controllerName}.php";

        if (file_exists($path)) {
            echo "Controller already exists!\n";
            return;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Controllers;

// use App\Seeds\\{$name}Seed;
use Exception;
use InvalidArgumentException;
use Utility\Core\Response;
use Utility\Core\Validator;

class {$controllerName}
{
   // private {$name}Seed \${$name}Seed;

    public function __construct()
    {
     //   \$this->{$name}Seed = new {$name}Seed();
    }

    public function get{$name}(?string \$id = null): void
    {
        try {
            if (\$id === null) {
              //  \$items = \$this->{$name}Seed->getAll{$name}s();
              //  Response::successResponse(\$items, 200);
            } else {
                Validator::validate(['id' => \$id], [
                    'id' => 'required|string',
                ]);


            }
        } catch (InvalidArgumentException \$e) {
            Response::errorResponse(\$e->getMessage(), 400);
        } catch (Exception \$e) {
            Response::errorResponse('An error occurred while fetching {$name}', 500);
        }
    }

    public function create{$name}(): void
    {
        try {
            Validator::validate(\$_POST, [
                // Add your validation rules here
            ]);

            // \$result = \$this->{$name}Seed->create{$name}(\$_POST);

            // if (\$result) {
            //     Response::successResponse(['message' => '{$name} created successfully'], 200);
            // } else {
            //     Response::errorResponse('Failed to create {$name}', 500);
            // }
        } catch (InvalidArgumentException \$e) {
            Response::errorResponse(\$e->getMessage(), 400);
        }
    }
}
PHP;

        if (!is_dir($controllerDir)) {
            mkdir($controllerDir, 0777, true);
        }

        file_put_contents($path, $stub);
        echo "Controller {$controllerName} created successfully!\n";
    }
} 