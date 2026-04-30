<?php

namespace App\Models;

use App\Core\QueryBuilder;
use App\Core\Validator;

abstract class BaseModel
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $rules = [];
    
    public function query(): QueryBuilder
    {
        return new QueryBuilder($this->table);
    }
    
    public function all(): array
    {
        return $this->query()->get();
    }
    
    public function find(int $id): ?array
    {
        $result = $this->query()->where($this->primaryKey, $id)->first();
        return $result ? $this->hideFields($result) : null;
    }
    
    public function where(string $column, $operator, $value = null): QueryBuilder
    {
        return $this->query()->where($column, $operator, $value);
    }
    
    public function create(array $data): int
    {
        $data = $this->filterFillable($data);
        
        if (!empty($this->rules)) {
            $validator = new Validator($data);
            if (!$validator->validate($this->rules)) {
                throw new \Exception(json_encode($validator->errors()));
            }
        }
        
        $data['created_at'] = now();
        $data['updated_at'] = now();
        
        return $this->query()->insert($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $data = $this->filterFillable($data);
        $data['updated_at'] = now();
        
        return $this->query()->where($this->primaryKey, $id)->update($data);
    }
    
    public function delete(int $id): bool
    {
        return $this->query()->where($this->primaryKey, $id)->delete();
    }
    
    public function paginate(int $perPage = 20, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        $items = $this->query()->limit($perPage, $offset)->get();
        $total = $this->query()->count();
        
        return [
            'data' => array_map([$this, 'hideFields'], $items),
            'pagination' => paginate($total, $perPage, $page)
        ];
    }
    
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    protected function hideFields(array $data): array
    {
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        return $data;
    }
}
