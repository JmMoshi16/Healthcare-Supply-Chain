<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\ApiValidator;
use App\Core\Logger;
use App\Models\Medicine;
use App\Models\Batch;
use App\Models\Stock;

class SearchApiController extends BaseController
{
    use ApiValidator;
    
    public function search(Request $request)
    {
        // Validate query parameter
        $query = $this->validateQueryParam($request, 'q', 'string', '', 2, 100);
        
        if (strlen($query) < 2) {
            return $this->json([
                'success' => false,
                'message' => 'Search query must be at least 2 characters'
            ], 400);
        }
        
        // Prevent SQL injection
        if (!$this->preventSqlInjection($query)) {
            Logger::warning('API: SQL injection attempt detected', [
                'query' => $query,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);
            
            return $this->json([
                'success' => false,
                'message' => 'Invalid search query'
            ], 400);
        }
        
        // Sanitize for XSS
        $query = $this->preventXss($query);
        
        try {
            $medicines = $this->searchMedicines($query);
            $batches = $this->searchBatches($query);
            
            return $this->json([
                'success' => true,
                'medicines' => $medicines,
                'batches' => $batches,
                'total' => count($medicines) + count($batches)
            ]);
        } catch (\Exception $e) {
            Logger::error('API: Search failed', [
                'error' => $e->getMessage(),
                'query' => $query,
            ]);
            
            return $this->json([
                'success' => false,
                'message' => 'Search failed'
            ], 500);
        }
    }
    
    private function searchMedicines(string $query): array
    {
        // Use parameterized query to prevent SQL injection
        $sql = "
            SELECT id, name, generic_name, category, unit
            FROM medicines
            WHERE is_active = 1
              AND (
                  name LIKE ? 
                  OR generic_name LIKE ?
                  OR category LIKE ?
              )
            LIMIT 5
        ";
        
        $searchTerm = "%{$query}%";
        return \App\Core\Database::query($sql, [$searchTerm, $searchTerm, $searchTerm])
            ->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    private function searchBatches(string $query): array
    {
        // Use parameterized query to prevent SQL injection
        $sql = "
            SELECT b.id, b.batch_number, b.current_quantity, b.expiry_date, m.name as medicine_name
            FROM batches b
            JOIN medicines m ON m.id = b.medicine_id
            WHERE b.status = 'active'
              AND (
                  b.batch_number LIKE ?
                  OR m.name LIKE ?
              )
            LIMIT 5
        ";
        
        $searchTerm = "%{$query}%";
        return \App\Core\Database::query($sql, [$searchTerm, $searchTerm])
            ->fetchAll(\PDO::FETCH_ASSOC);
    }
}
