<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Models\Medicine;
use App\Models\Batch;
use App\Models\Stock;

class SearchApiController extends BaseController
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return $this->json([
                'success' => false,
                'message' => 'Query too short'
            ], 400);
        }
        
        $medicines = $this->searchMedicines($query);
        $batches = $this->searchBatches($query);
        
        return $this->json([
            'success' => true,
            'medicines' => $medicines,
            'batches' => $batches,
            'total' => count($medicines) + count($batches)
        ]);
    }
    
    private function searchMedicines(string $query): array
    {
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
