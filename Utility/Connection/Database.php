<?php

declare(strict_types=1);
namespace Utility\Connection;

use Exception;
use PDO;
use PDOException;
use Utility\Core\EnvLoader;



class Database {
    private static ?self $instance = null;
    private PDO $connection;

    private function __construct() {
       
        EnvLoader::LoadEnv();
       
        $host = $_ENV['DB_HOST'] ?? throw new Exception('DB_HOST environment variable is not set');
        $dbname = $_ENV['DB_NAME'] ?? throw new Exception('DB_NAME environment variable is not set');
        $user = $_ENV['DB_USER'] ?? throw new Exception('DB_USER environment variable is not set');
        $pass = $_ENV['DB_PASS'] ?? throw new Exception('DB_PASS environment variable is not set');

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log the error and throw a generic exception to avoid exposing sensitive information
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed. Please check your environment variables and try again.');
        }
    }

 

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}
