<?php

declare(strict_types=1);

namespace Utility\Connection;

use Utility\Connection\Database;
use Utility\Connection\BaseModel;
use InvalidArgumentException;
use PDO;
use PDOException;

class Query extends BaseModel
{
    private string $table;
    private string $operation;
    private array $data = [];
    private array $where = [];
    private PDO $pdo;
    private array $columns = ['*'];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $joins = [];
    private array $orderBy = [];
    private array $groupBy = [];
    private array $having = [];
    private bool $debug = false;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public static function table(string $table): self
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    public function create(array $columns): self
    {
        $this->operation = 'CREATE';
        $this->data = $columns;
        return $this;
    }

    public function alter(array $modifications): self
    {
        $this->operation = 'ALTER';
        $this->data = $modifications;
        return $this;
    }

    public function insert(array $data): self
    {
        $this->operation = 'INSERT';
        $this->data = $data;
        return $this;
    }

    public function update(array $data): self
    {
        $this->operation = 'UPDATE';
        $this->data = $data;
        return $this;
    }

    public function delete(): self
    {
        $this->operation = 'DELETE';
        return $this;
    }

    public function where(string $column, $value): self
    {
        $this->where[$column] = $value;
        return $this;
    }

    public function select(array $columns = ['*']): self
    {
        $this->operation = 'SELECT';
        $this->columns = $columns;
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function innerJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = ['type' => 'INNER JOIN', 'table' => $table, 'first' => $first, 'operator' => $operator, 'second' => $second];
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = ['type' => 'LEFT JOIN', 'table' => $table, 'first' => $first, 'operator' => $operator, 'second' => $second];
        return $this;
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = ['type' => 'RIGHT JOIN', 'table' => $table, 'first' => $first, 'operator' => $operator, 'second' => $second];
        return $this;
    }

    public function fullOuterJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = ['type' => 'FULL OUTER JOIN', 'table' => $table, 'first' => $first, 'operator' => $operator, 'second' => $second];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = [$column, strtoupper($direction)];
        return $this;
    }

    public function groupBy(string ...$columns): self
    {
        $this->groupBy = array_merge($this->groupBy, $columns);
        return $this;
    }

    public function having(string $column, string $operator, $value): self
    {
        $this->having[] = [$column, $operator, $value];
        return $this;
    }

    public function debug(bool $enable = true): self
    {
        $this->debug = $enable;
        return $this;
    }

    public function execute()
    {

        $query = $this->buildQuery();
        $params = $this->getParams();

        if ($this->debug) {
            echo "Debug - SQL Query: " . $this->interpolateQuery($query, $params) . "\n";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        switch ($this->operation) {
            case 'SELECT':
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            case 'INSERT':
                return $this->pdo->lastInsertId();
            default:
                return $stmt->rowCount();
        }
    }

    private function buildQuery(): string
    {
        switch ($this->operation) {
            case 'SELECT':
                return $this->buildSelectQuery();
            case 'INSERT':
                return $this->buildInsertQuery();
            case 'UPDATE':
                return $this->buildUpdateQuery();
            case 'DELETE':
                return $this->buildDeleteQuery();
            case 'CREATE':
                return $this->buildCreateQuery();
            case 'ALTER':
                return $this->buildAlterQuery();
            default:
                throw new InvalidArgumentException("Invalid operation: {$this->operation}");
        }
    }

    private function buildInsertQuery(): string
    {
        $columns = implode(', ', array_keys($this->data));
        $values = implode(', ', array_fill(0, count($this->data), '?'));
        return "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
    }

    private function buildUpdateQuery(): string
    {
        $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($this->data)));
        $where = $this->buildWhereClause();
        return "UPDATE {$this->table} SET {$set} {$where}";
    }

    private function buildDeleteQuery(): string
    {
        $where = $this->buildWhereClause();
        return "DELETE FROM {$this->table} {$where}";
    }

    private function buildWhereClause(): string
    {
        if (empty($this->where)) {
            return '';
        }
        $conditions = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($this->where)));
        return "WHERE {$conditions}";
    }

    private function buildSelectQuery(): string
    {
        $columns = $this->columns[0] === '*' 
            ? '*' 
            : implode(', ', array_map([$this, 'quoteIdentifier'], $this->columns));
        
        $query = "SELECT {$columns} FROM " . $this->quoteIdentifier($this->table);
        
        // Add JOIN clauses
        foreach ($this->joins as $join) {
            $query .= " {$join['type']} " . $this->quoteIdentifier($join['table']) . 
                      " ON {$join['first']} {$join['operator']} {$join['second']}";
        }
        
        $where = $this->buildWhereClause();
        if (!empty($where)) {
            $query .= " " . $where;
        }
        
        if (!empty($this->groupBy)) {
            $query .= " GROUP BY " . implode(', ', array_map([$this, 'quoteIdentifier'], $this->groupBy));
        }
        
        if (!empty($this->having)) {
            $query .= " HAVING " . $this->buildHavingClause();
        }
        
        if (!empty($this->orderBy)) {
            $query .= " ORDER BY " . $this->buildOrderByClause();
        }
        
        if ($this->limit !== null) {
            $query .= " LIMIT " . intval($this->limit);
        }
        
        if ($this->offset !== null) {
            $query .= " OFFSET " . intval($this->offset);
        }
        
        return $query;
    }

    private function buildHavingClause(): string
    {
        return implode(' AND ', array_map(function($condition) {
            return "{$condition[0]} {$condition[1]} ?";
        }, $this->having));
    }

    private function buildOrderByClause(): string
    {
        return implode(', ', array_map(function($order) {
            return $this->quoteIdentifier($order[0]) . ' ' . $order[1];
        }, $this->orderBy));
    }

    private function buildCreateQuery(): string
    {
        $columnDefinitions = [];
        foreach ($this->data as $column => $definition) {
            $columnDefinitions[] = "`$column` $definition";
        }
        
        return "CREATE TABLE IF NOT EXISTS `{$this->table}` (" . 
               implode(', ', $columnDefinitions) . 
               ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }

    private function buildAlterQuery(): string
    {
        $alterStatements = [];
        foreach ($this->data as $action => $details) {
            switch (strtoupper($action)) {
                case 'ADD':
                    foreach ($details as $column => $definition) {
                        $alterStatements[] = "ADD COLUMN `$column` $definition";
                    }
                    break;
                case 'MODIFY':
                    foreach ($details as $column => $definition) {
                        $alterStatements[] = "MODIFY COLUMN `$column` $definition";
                    }
                    break;
                case 'DROP':
                    foreach ($details as $column) {
                        $alterStatements[] = "DROP COLUMN `$column`";
                    }
                    break;
                case 'RENAME':
                    foreach ($details as $oldColumn => $newColumn) {
                        $alterStatements[] = "RENAME COLUMN `$oldColumn` TO `$newColumn`";
                    }
                    break;
            }
        }

        if (empty($alterStatements)) {
            throw new InvalidArgumentException("No valid alter statements provided");
        }

        return "ALTER TABLE `{$this->table}` " . implode(', ', $alterStatements);
    }
    private function quoteIdentifier(string $identifier): string
    {
        // Remove backticks and return the identifier as-is
        return str_replace('`', '', $identifier);
    }

    private function getParams(): array
    {
        $params = array_values($this->data);
        $params = array_merge($params, array_values($this->where));
        foreach ($this->having as $condition) {
            $params[] = $condition[2];
        }
        return $params;
    }

    private function interpolateQuery(string $query, array $params): string
    {
        $keys = array();
        $values = $params;

        // build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:' . $key . '/';
            } else {
                $keys[] = '/[?]/';
            }

            if (is_array($value)) {
                $values[$key] = "'" . implode("','", array_map([$this->pdo, 'quote'], $value)) . "'";
            } elseif (is_null($value)) {
                $values[$key] = 'NULL';
            } elseif (is_string($value)) {
                $values[$key] = $this->pdo->quote($value);
            } elseif (is_bool($value)) {
                $values[$key] = $value ? '1' : '0';
            } else {
                $values[$key] = $value;
            }
        }

        $query = preg_replace($keys, $values, $query, 1, $count);

        return $query;
    }

   
}