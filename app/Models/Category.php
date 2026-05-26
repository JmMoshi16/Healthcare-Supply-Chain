<?php

namespace App\Models;

class Category extends BaseModel
{
    protected string $table = 'categories';
    protected array $fillable = ['name'];

    public function all(): array
    {
        return $this->query()->orderBy('name', 'ASC')->get();
    }
}
