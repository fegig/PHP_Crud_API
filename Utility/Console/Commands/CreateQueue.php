<?php

declare(strict_types=1);

namespace Utility\Console\Commands;

class CreateQueue
{
    public static function create(string $name): void
    {
        $queueDir = __DIR__ . '/../../../app/Queues';
        $queueName = ucfirst($name) . 'Queue';
        $path = "{$queueDir}/{$queueName}.php";

        if (file_exists($path)) {
            echo "Queue already exists!\n";
            return;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Queues;

use Exception;

class {$queueName}
{
    private array \$queue = [];
    private string \$queueFile;

    public function __construct()
    {
        \$this->queueFile = storage_path('queues/{$name}_queue.json');
        \$this->loadQueue();
    }

    public function push(array \$data): bool
    {
        try {
            \$this->queue[] = [
                'id' => uniqid(),
                'data' => \$data,
                'created_at' => date('Y-m-d H:i:s')
            ];
            return \$this->saveQueue();
        } catch (Exception \$e) {
            error_log("Error pushing to {$name} queue: " . \$e->getMessage());
            return false;
        }
    }

    public function pop()
    {
        if (empty(\$this->queue)) {
            return null;
        }

        \$item = array_shift(\$this->queue);
        \$this->saveQueue();
        return \$item;
    }

    private function loadQueue(): void
    {
        if (file_exists(\$this->queueFile)) {
            \$this->queue = json_decode(file_get_contents(\$this->queueFile), true) ?? [];
        }
    }

    private function saveQueue(): bool
    {
        try {
            \$dir = dirname(\$this->queueFile);
            if (!is_dir(\$dir)) {
                mkdir(\$dir, 0777, true);
            }
            return file_put_contents(\$this->queueFile, json_encode(\$this->queue)) !== false;
        } catch (Exception \$e) {
            error_log("Error saving queue: " . \$e->getMessage());
            return false;
        }
    }
}
PHP;

        if (!is_dir($queueDir)) {
            mkdir($queueDir, 0777, true);
        }

        file_put_contents($path, $stub);
        echo "Queue {$queueName} created successfully!\n";
    }
} 