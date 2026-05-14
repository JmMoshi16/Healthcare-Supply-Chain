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
    
    protected bool $timestamps = true;
    protected bool $useSoftDeletes = false;
    protected bool $logActivities = false;
    
    public function query(): QueryBuilder
    {
        $query = new QueryBuilder($this->table);
        if ($this->useSoftDeletes) {
            $query->whereNull('deleted_at');
        }
        return $query;
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
        
        if ($this->timestamps) {
            $data['created_at'] = now();
            $data['updated_at'] = now();
        }
        
        $id = $this->query()->insert($data);
        
        $this->logActivity('create', $id, null, $data);
        
        return $id;
    }
    
    public function update(int $id, array $data): bool
    {
        $oldData = $this->find($id);
        
        $data = $this->filterFillable($data);
        if ($this->timestamps) {
            $data['updated_at'] = now();
        }
        
        $success = $this->query()->where($this->primaryKey, $id)->update($data);
        
        if ($success) {
            $this->logActivity('update', $id, $oldData, $data);
        }
        
        return $success;
    }
    
    public function delete(int $id): bool
    {
        $oldData = $this->find($id);
        
        if ($this->useSoftDeletes) {
            $success = $this->query()->where($this->primaryKey, $id)->update(['deleted_at' => now()]);
        } else {
            $success = $this->query()->where($this->primaryKey, $id)->delete();
        }
        
        if ($success) {
            $this->logActivity('delete', $id, $oldData, null);
        }
        
        return $success;
    }
    
    protected function logActivity(string $action, int $modelId, ?array $oldData, ?array $newData): void
    {
        if (!$this->logActivities) {
            return;
        }
        
        $userId = auth()['id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        
        $activityLog = new ActivityLog();
        $activityLog->create([
            'user_id' => $userId,
            'action' => $action,
            'model' => basename(str_replace('\\', '/', static::class)),
            'model_id' => $modelId,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => $ipAddress
        ]);
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
