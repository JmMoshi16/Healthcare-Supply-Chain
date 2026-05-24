<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Medicine;
use App\Models\Batch;

class MedicineApiController extends BaseController
{
    private Medicine $medicine;
    private Batch $batch;

    public function __construct()
    {
        $this->medicine = new Medicine();
        $this->batch    = new Batch();
    }

    public function index(Request $request)
    {
        $validator = new Validator($request->all());
        if (!$validator->validate(['page' => 'numeric'])) {
            return $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $page   = max(1, (int) $request->get('page', 1));
        $result = $this->medicine->paginate(20, $page);

        return $this->json([
            'success'    => true,
            'data'       => $result['data'],
            'pagination' => $result['pagination'],
        ]);
    }

    public function show(Request $request, string $id)
    {
        if (!ctype_digit($id)) {
            return $this->json(['success' => false, 'message' => 'Invalid medicine ID'], 422);
        }

        $medicine = $this->medicine->find((int) $id);

        if (!$medicine) {
            return $this->json(['success' => false, 'message' => 'Medicine not found'], 404);
        }

        return $this->json(['success' => true, 'data' => $medicine]);
    }

    public function stock(Request $request, string $id)
    {
        if (!ctype_digit($id)) {
            return $this->json(['success' => false, 'message' => 'Invalid medicine ID'], 422);
        }

        $medicine = $this->medicine->withBatches((int) $id);

        if (!$medicine) {
            return $this->json(['success' => false, 'message' => 'Medicine not found'], 404);
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
    }

    public function toggleStatus(Request $request, string $id)
    {
        if (!ctype_digit($id)) {
            return $this->json(['success' => false, 'message' => 'Invalid medicine ID'], 422);
        }

        $medicine = $this->medicine->find((int) $id);

        if (!$medicine) {
            return $this->json(['success' => false, 'message' => 'Medicine not found'], 404);
        }

        $newStatus = $medicine['is_active'] ? 0 : 1;
        $this->medicine->update((int) $id, ['is_active' => $newStatus]);

        return $this->json([
            'success'   => true,
            'message'   => 'Status updated successfully',
            'is_active' => $newStatus,
        ]);
    }

    public function expiring(Request $request)
    {
        $validator = new Validator($request->all());
        if (!$validator->validate(['days' => 'numeric'])) {
            return $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $days    = max(1, (int) $request->get('days', 30));
        $batches = $this->batch->getExpiringSoon($days);

        return $this->json([
            'success' => true,
            'data'    => $batches,
            'count'   => count($batches),
            'message' => count($batches) . " batches expiring in {$days} days",
        ]);
    }

    public function lowStock(Request $request)
    {
        $validator = new Validator($request->all());
        if (!$validator->validate(['threshold' => 'numeric'])) {
            return $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $threshold = max(0, (int) $request->get('threshold', 10));
        $medicines = $this->medicine->getLowStock($threshold);

        return $this->json([
            'success' => true,
            'data'    => $medicines,
            'count'   => count($medicines),
            'message' => count($medicines) . " medicines below threshold of {$threshold}",
        ]);
    }
}
