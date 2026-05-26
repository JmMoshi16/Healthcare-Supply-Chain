<?php

namespace App\Models;

class Medicine extends BaseModel
{
    protected string $table = 'medicines';
    protected bool $useSoftDeletes = true;
    protected bool $logActivities = true;

    protected array $fillable = [
        'name', 'generic_name', 'category_id', 'description', 'unit', 'image', 'is_active', 'minimum_stock', 'reorder_quantity'
    ];

    public function withBatches(int $id): ?array
    {
        $medicine = $this->find($id);
        if (!$medicine) {
            return null;
        }

        $batches = (new Batch())->query()
            ->where('medicine_id', $id)
            ->orderBy('expiry_date', 'ASC')
            ->get();

        $medicine['batches']     = $batches;
        $medicine['total_stock'] = array_sum(array_column($batches, 'current_quantity'));

        return $medicine;
    }

    public function getLowStock(): array
    {
        $sql = "
            SELECT m.*, COALESCE(SUM(b.current_quantity), 0) AS total_stock,
                   (m.minimum_stock - COALESCE(SUM(b.current_quantity), 0)) as deficit,
                   m.reorder_quantity as suggested_order
            FROM medicines m
            LEFT JOIN batches b ON b.medicine_id = m.id AND b.status = 'active' AND b.deleted_at IS NULL
            WHERE m.is_active = 1 AND m.deleted_at IS NULL
            GROUP BY m.id
            HAVING total_stock <= m.minimum_stock
            ORDER BY deficit DESC, total_stock ASC
        ";

        return \App\Core\Database::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
