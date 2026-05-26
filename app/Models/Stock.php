<?php

namespace App\Models;

class Stock extends BaseModel
{
    protected string $table = 'stocks';

    protected array $fillable = [
        'batch_id', 'transaction_type', 'reason_code', 'reference_number',
        'quantity', 'reason', 'recipient', 'ward_department', 'performed_by',
    ];

    public function create(array $data): int
    {
        (new Batch())->updateStock(
            (int) $data['batch_id'],
            (int) $data['quantity'],
            $data['transaction_type']
        );

        $data = $this->filterFillable($data);
        $data['created_at'] = now();

        $cols        = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql         = "INSERT INTO stocks ({$cols}) VALUES ({$placeholders})";

        \App\Core\Database::query($sql, array_values($data));
        return (int) \App\Core\Database::lastInsertId();
    }

    public function getFiltered(array $filters = [], string $sort = 'created_at', string $dir = 'DESC', int $perPage = 20, int $page = 1): array
    {
        $allowed_sorts = ['id', 'created_at', 'quantity', 'transaction_type', 'reason_code'];
        $sort = in_array($sort, $allowed_sorts) ? $sort : 'created_at';
        $dir  = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['type'])) {
            $where[]  = 's.transaction_type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['reason_code'])) {
            $where[]  = 's.reason_code = ?';
            $params[] = $filters['reason_code'];
        }
        if (!empty($filters['ward'])) {
            $where[]  = 's.ward_department LIKE ?';
            $params[] = '%' . $filters['ward'] . '%';
        }
        if (!empty($filters['performed_by'])) {
            $where[]  = 's.performed_by = ?';
            $params[] = $filters['performed_by'];
        }
        if (!empty($filters['date_from'])) {
            $where[]  = 'DATE(s.created_at) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[]  = 'DATE(s.created_at) <= ?';
            $params[] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $where[]  = '(b.batch_number LIKE ? OR s.reason LIKE ? OR s.recipient LIKE ? OR s.reference_number LIKE ?)';
            $term     = '%' . $filters['search'] . '%';
            $params   = array_merge($params, [$term, $term, $term, $term]);
        }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) FROM stocks s JOIN batches b ON b.id = s.batch_id WHERE {$whereStr}";
        $total    = (int) \App\Core\Database::query($countSql, $params)->fetchColumn();

        $sql = "
            SELECT s.*, b.batch_number, m.name AS medicine_name, u.fullname AS performed_by_name
            FROM stocks s
            JOIN batches b ON b.id = s.batch_id
            JOIN medicines m ON m.id = b.medicine_id
            JOIN users u ON u.id = s.performed_by
            WHERE {$whereStr}
            ORDER BY s.{$sort} {$dir}
            LIMIT {$perPage} OFFSET {$offset}
        ";

        $data = \App\Core\Database::query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'data'       => $data,
            'pagination' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'total_pages'  => (int) ceil($total / $perPage),
            ],
        ];
    }

    public function getRecentTransactions(int $limit = 10): array
    {
        $sql = "
            SELECT s.*, b.batch_number, m.name AS medicine_name, u.fullname AS performed_by_name
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
