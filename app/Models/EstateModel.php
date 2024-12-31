<?php

declare(strict_types=1);

namespace App\Models;

class EstateModel
{

    public const TABLE_NAME = 'estates';
    public const TABLE_COLUMNS = [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'property_id' => 'VARCHAR(255) UNIQUE NOT NULL',
        'price' => 'DECIMAL(10,2) NOT NULL',
        'address' => 'VARCHAR(255) NOT NULL',
        'size' => 'INT NOT NULL',
        'baths' => 'INT NOT NULL',
        'rooms' => 'INT NOT NULL',
        'park' => 'BOOLEAN NOT NULL',
        'type' => 'VARCHAR(50) NOT NULL',
        'mail' => 'VARCHAR(255) NOT NULL',
        'agent' => 'VARCHAR(255) NOT NULL',
        'option' => 'VARCHAR(50) NOT NULL',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ];




} 