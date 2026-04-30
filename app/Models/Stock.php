<?php

namespace App\Models;

class Stock extends BaseModel
{
    protected string $table = 'stocks';

    protected array $fillable = [
        'batch_id', 'transaction_type', 'quantity', 'reason', 'performed_by',
    ];

    public function create(array $data): int
    {
        (new Batch())->updateStock(
            (int) $data['batch_id'],
            (int) $data['quantity'],
            $data['transaction_type']
        );

        return parent::create($data);
    }

    public function getRecentTransactions(int $limit = 10): array
    {
        $sql = "
            SELECT s.*,
                   b.batch_number,
                   m.name AS medicine_name,
                   u.fullname AS performed_by_name
            FROM stocks s
            JOIN batches b ON b.id = s.batch_id
            JOIN medicines m ON m.id = b.medicine_id
            JOIN users u ON u.id = s.performed_by
            ORDER BY s.created_at DESC
            LIMIT ?
        ";

        return \App\Core\Database::query($sql, [$limit])->fetchAll(\PDO::FETCH_ASSOC);
    }
}
