<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Utility\Console\Kernel;

$kernel = new Kernel();
$kernel->handle($argv); 