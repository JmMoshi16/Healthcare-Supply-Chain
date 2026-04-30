<?php

namespace App\Core;

use PDO;

class QueryBuilder
{
    protected string $table;
    protected array $wheres = [];
    protected array $bindings = [];
    protected string $orderBy = '';
    protected string $limit = '';
    protected array $joins = [];
    protected array $selects = ['*'];
    
    public function __construct(string $table)
    {
        $this->table = $table;
    }
    
    public function select(...$columns): self
    {
        $this->selects = $columns;
        return $this;
    }
    
    public function where(string $column, $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->wheres[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }
    
    public function orWhere(string $column, $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->wheres[] = "OR {$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }
    
    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "INNER JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }
    
    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = "ORDER BY {$column} {$direction}";
        return $this;
    }
    
    public function limit(int $limit, int $offset = 0): self
    {
        $this->limit = "LIMIT {$limit} OFFSET {$offset}";
        return $this;
    }
    
    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        return Database::query($sql, $this->bindings)->fetchAll();
    }
    
    public function first(): ?array
    {
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }
    
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }
        
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        
        $result = Database::query($sql, $this->bindings)->fetch();
        return (int) $result['count'];
    }
    
    public function insert(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        Database::query($sql, array_values($data));
        
        return (int) Database::lastInsertId();
    }
    
    public function update(array $data): bool
    {
        $sets = [];
        $bindings = [];
        
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = ?";
            $bindings[] = $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
        
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
            $bindings = array_merge($bindings, $this->bindings);
        }
        
        Database::query($sql, $bindings);
        return true;
    }
    
    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        
        Database::query($sql, $this->bindings);
        return true;
    }
    
    private function buildSelectQuery(): string
    {
        $columns = implode(', ', $this->selects);
        $sql = "SELECT {$columns} FROM {$this->table}";
        
        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }
        
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        
        if ($this->orderBy) {
            $sql .= ' ' . $this->orderBy;
        }
        
        if ($this->limit) {
            $sql .= ' ' . $this->limit;
        }
        
        return $sql;
    }
}
