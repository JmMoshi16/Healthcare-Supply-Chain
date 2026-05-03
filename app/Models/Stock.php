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

    public function getWeeklyStats(): array
    {
        $sql = "
            SELECT DAYNAME(s.created_at) as day_name,
                   DAYOFWEEK(s.created_at) as day_num,
                   COUNT(*) as transaction_count,
                   SUM(s.quantity) as total_quantity
            FROM stocks s
            WHERE s.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(s.created_at), day_name, day_num
            ORDER BY DATE(s.created_at)
        ";

        return \App\Core\Database::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getMonthlyStats(): array
    {
        $sql = "
            SELECT MONTHNAME(s.created_at) as month_name,
                   MONTH(s.created_at) as month_num,
                   COUNT(*) as transaction_count,
                   SUM(s.quantity) as total_quantity
            FROM stocks s
            WHERE s.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY YEAR(s.created_at), MONTH(s.created_at), month_name, month_num
            ORDER BY YEAR(s.created_at), MONTH(s.created_at)
        ";

        return \App\Core\Database::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTransactionStats(): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total_count,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_count,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as week_count
            FROM stocks
        ";

        return \App\Core\Database::query($sql)->fetch(\PDO::FETCH_ASSOC) ?: [];
    }
}
