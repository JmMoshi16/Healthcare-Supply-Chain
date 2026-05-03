<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\ApiValidator;
use App\Core\Logger;
use App\Models\Medicine;
use App\Models\Batch;

class MedicineApiController extends BaseController
{
    use ApiValidator;
    
    private Medicine $medicine;
    private Batch $batch;

    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->batch = new Batch();
    }

    public function index(Request $request)
    {
        // Validate query parameters
        $page = $this->validateQueryParam($request, 'page', 'integer', 1, 1, 1000);
        $perPage = $this->validateQueryParam($request, 'per_page', 'integer', 20, 1, 100);
        
        try {
            $result = $this->medicine->paginate($perPage, $page);

            return $this->json([
                'success'    => true,
                'data'       => $result['data'],
                'pagination' => $result['pagination'],
            ]);
        } catch (\Exception $e) {
            Logger::error('API: Failed to fetch medicines', [
                'error' => $e->getMessage(),
                'page' => $page,
            ]);
            
            return $this->json([
                'success' => false,
                'message' => 'Failed to fetch medicines',
            ], 500);
        }
    }

    public function show(Request $request, string $id)
    {
        // Validate ID
        $id = $this->validateId($id);
        
        try {
            $medicine = $this->medicine->find($id);

            if (!$medicine) {
                return $this->json([
                    'success' => false,
                    'message' => 'Medicine not found'
                ], 404);
            }

            return $this->json([
                'success' => true,
                'data' => $medicine
            ]);
        } catch (\Exception $e) {
            Logger::error('API: Failed to fetch medicine', [
                'error' => $e->getMessage(),
                'id' => $id,
            ]);
            
            return $this->json([
                'success' => false,
                'message' => 'Failed to fetch medicine',
            ], 500);
        }
    }

    public function stock(Request $request, string $id)
    {
        // Validate ID
        $id = $this->validateId($id);
        
        try {
            $medicine = $this->medicine->withBatches($id);

            if (!$medicine) {
                return $this->json([
                    'success' => false,
                    'message' => 'Medicine not found'
                ], 404);
            }

            return $this->json([
                'success' => true,
                'data'    => [
                    'medicine_id' => $medicine['id'],
                    'name'        => $medicine['name'],
                    'total_stock' => $medicine['total_stock'],
                    'batches'     => $medicine['batches'],
                ],
            ]);
        } catch (\Exception $e) {
            Logger::error('API: Failed to fetch medicine stock', [
                'error' => $e->getMessage(),
                'id' => $id,
            ]);
            
            return $this->json([
                'success' => false,
                'message' => 'Failed to fetch stock information',
            ], 500);
        }
    }

    public function toggleStatus(Request $request, string $id)
    {
        // Validate ID
        $id = $this->validateId($id);
        
        try {
            $medicine = $this->medicine->find($id);

            if (!$medicine) {
                return $this->json([
                    'success' => false,
                    'message' => 'Medicine not found'
                ], 404);
            }

            $newStatus = $medicine['is_active'] ? 0 : 1;
            $this->medicine->update($id, ['is_active' => $newStatus]);
            
            Logger::info('API: Medicine status toggled', [
                'medicine_id' => $id,
                'new_status' => $newStatus,
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'is_active' => $newStatus
            ]);
        } catch (\Exception $e) {
            Logger::error('API: Failed to toggle medicine status', [
                'error' => $e->getMessage(),
                'id' => $id,
            ]);
            
            return $this->json([
                'success' => false,
                'message' => 'Failed to update status',
            ], 500);
        }
    }

    public function expiring(Request $request)
    {
        // Validate query parameters
        $days = $this->validateQueryParam($request, 'days', 'integer', 30, 1, 365);
        
        try {
            $batches = $this->batch->getExpiringSoon($days);

            return $this->json([
                'success' => true,
                'data'    => $batches,
                'count'   => count($batches),
                'message' => count($batches) . " batches expiring in {$days} days",
            ]);
        } catch (\Exception $e) {
            Logger::error('API: Failed to fetch expiring batches', [
                'error' => $e->getMessage(),
                'days' => $days,
            ]);
            
            return $this->json([
                'success' => false,
                'message' => 'Failed to fetch expiring batches',
            ], 500);
        }
    }

    public function lowStock(Request $request)
    {
        // Validate query parameters
        $threshold = $this->validateQueryParam($request, 'threshold', 'integer', 10, 1, 10000);
        
        try {
            $medicines = $this->medicine->getLowStock($threshold);

            return $this->json([
                'success' => true,
                'data'    => $medicines,
                'count'   => count($medicines),
                'message' => count($medicines) . " medicines below threshold of {$threshold}",
            ]);
        } catch (\Exception $e) {
            Logger::error('API: Failed to fetch low stock medicines', [
                'error' => $e->getMessage(),
                'threshold' => $threshold,
            ]);
            
            return $this->json([
                'success' => false,
                'message' => 'Failed to fetch low stock medicines',
            ], 500);
        }
    }
}
