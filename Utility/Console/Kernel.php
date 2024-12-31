<?php

declare(strict_types=1);

namespace Utility\Console;

use Utility\Console\Commands\CreateController;
use Utility\Console\Commands\CreateModel;
use Utility\Console\Commands\CreateQueue;
use Utility\Console\Commands\CreateSeeding;

class Kernel
{
    private array $commands = [
        'controller' => CreateController::class,
        'model' => CreateModel::class,
        'queue' => CreateQueue::class,
        'seed' => CreateSeeding::class
    ];

    public function handle(array $argv): void
    {
        if (count($argv) < 3) {
            $this->showUsage();
            exit(1);
        }

        $type = strtolower($argv[1]);
        $name = ucfirst($argv[2]);

        if (!isset($this->commands[$type])) {
            echo "Invalid command type. Available commands:\n";
            foreach (array_keys($this->commands) as $command) {
                echo "- {$command}\n";
            }
            exit(1);
        }

        $commandClass = $this->commands[$type];
        $commandClass::create($name);
    }

    private function showUsage(): void
    {
        echo "Usage: php console [command] [name]\n\n";
        echo "Available commands:\n";
        foreach (array_keys($this->commands) as $command) {
            echo "- {$command}\n";
        }
    }
} 