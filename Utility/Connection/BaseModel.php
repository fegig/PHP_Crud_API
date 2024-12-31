<?php
namespace Utility\Connection;

use Utility\Connection\Database;


class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
}
