<?php

namespace App\Models;

class ActivityLog extends BaseModel
{
    protected string $table = 'activity_logs';

    protected array $fillable = [
        'user_id', 'action', 'model', 'model_id', 'old_data', 'new_data', 'ip_address'
    ];
    
    protected bool $timestamps = false;
}
