<?php

namespace App\Models;

class Batch extends BaseModel
{
    protected string $table = 'batches';

    protected array $fillable = [
        'medicine_id', 'batch_number', 'manufacturing_date', 'expiry_date',
        'supplier', 'purchase_price', 'selling_price', 'initial_quantity',
        'current_quantity', 'status',
    ];

    public function create(array $data): int
    {
        $data['current_quantity'] ??= $data['initial_quantity'];
        $data['status']           ??= 'active';

        return parent::create($data);
    }

    public function getExpiringSoon(int $days = 30): array
    {
        $sql = "
            SELECT b.*, m.name AS medicine_name, m.generic_name
            FROM batches b
            JOIN medicines m ON m.id = b.medicine_id
            WHERE b.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
              AND b.status = 'active'
              AND b.current_quantity > 0
            ORDER BY b.expiry_date ASC
        ";

        return \App\Core\Database::query($sql, [$days])->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateStock(int $id, int $quantity, string $type = 'out'): bool
    {
        $batch = $this->find($id);
        if (!$batch) {
            return false;
        }

        $newQuantity = $type === 'out'
            ? $batch['current_quantity'] - $quantity
            : $batch['current_quantity'] + $quantity;

        if ($newQuantity < 0) {
            throw new \Exception('Insufficient stock');
        }

        return $this->update($id, ['current_quantity' => $newQuantity]);
    }
}
