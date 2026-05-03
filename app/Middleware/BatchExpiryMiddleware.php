<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\Batch;

class BatchExpiryMiddleware
{
    private static bool $checked = false;
    
    public function handle(Request $request): ?Response
    {
        // Only check once per request to avoid multiple updates
        if (self::$checked) {
            return null;
        }
        
        // Update expired batches
        $batch = new Batch();
        $batch->updateExpiredBatches();
        
        self::$checked = true;
        
        return null; // Allow request to proceed
    }
}
