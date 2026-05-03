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
        // Auto-update expired batches before fetching
        $this->updateExpiredBatches();
        
        $sql = "
            SELECT b.*, m.name AS medicine_name, m.generic_name, m.category
            FROM batches b
            JOIN medicines m ON m.id = b.medicine_id
            WHERE b.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
              AND b.status = 'active'
              AND b.current_quantity > 0
            ORDER BY b.expiry_date ASC
        ";

        return \App\Core\Database::query($sql, [$days])->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getExpiringByDateRange(string $startDate, string $endDate): array
    {
        $sql = "
            SELECT b.*, 
                   m.name AS medicine_name, 
                   m.generic_name,
                   m.category,
                   m.id AS medicine_id,
                   DATEDIFF(b.expiry_date, CURDATE()) AS days_until_expiry
            FROM batches b
            JOIN medicines m ON m.id = b.medicine_id
            WHERE b.expiry_date BETWEEN ? AND ?
              AND b.status = 'active'
              AND b.current_quantity > 0
            ORDER BY b.expiry_date ASC
        ";

        return \App\Core\Database::query($sql, [$startDate, $endDate])->fetchAll(\PDO::FETCH_ASSOC);
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

    public function getBatchStats(): array
    {
        // Auto-update expired batches before calculating stats
        $this->updateExpiredBatches();
        
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired,
                COALESCE(SUM(current_quantity * selling_price), 0) as total_value
            FROM batches
        ";

        return \App\Core\Database::query($sql)->fetch(\PDO::FETCH_ASSOC) ?: [];
    }

    public function getExpiringGroupedByDate(string $startDate, string $endDate): array
    {
        // Auto-update expired batches before fetching
        $this->updateExpiredBatches();
        
        // Use the existing method and group in PHP for better compatibility
        $sql = "
            SELECT b.id,
                   b.batch_number,
                   b.current_quantity,
                   b.expiry_date,
                   m.id AS medicine_id,
                   m.name AS medicine_name,
                   m.generic_name,
                   m.category,
                   DATEDIFF(b.expiry_date, CURDATE()) AS days_until_expiry
            FROM batches b
            JOIN medicines m ON m.id = b.medicine_id
            WHERE b.expiry_date BETWEEN ? AND ?
              AND b.status = 'active'
              AND b.current_quantity > 0
            ORDER BY b.expiry_date ASC
        ";

        $results = \App\Core\Database::query($sql, [$startDate, $endDate])->fetchAll(\PDO::FETCH_ASSOC);
        
        // Group by expiry date in PHP
        $grouped = [];
        foreach ($results as $row) {
            $date = $row['expiry_date'];
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $row;
        }
        
        return $grouped;
    }
    
    /**
     * Automatically update expired batches
     * Called on each request to ensure data accuracy
     */
    public function updateExpiredBatches(): int
    {
        $sql = "
            UPDATE batches 
            SET status = 'expired',
                updated_at = NOW()
            WHERE expiry_date < CURDATE() 
              AND status = 'active'
        ";
        
        $stmt = \App\Core\Database::query($sql);
        $count = $stmt->rowCount();
        
        // Log if any batches were expired
        if ($count > 0) {
            \App\Core\Logger::info('Auto-expired batches', [
                'count' => $count,
                'date' => date('Y-m-d'),
            ]);
        }
        
        return $count;
    }
    
    /**
     * Get all expired batches
     */
    public function getExpiredBatches(): array
    {
        // Update expired batches first
        $this->updateExpiredBatches();
        
        $sql = "
            SELECT b.*, 
                   m.name AS medicine_name,
                   m.generic_name,
                   m.category,
                   DATEDIFF(CURDATE(), b.expiry_date) AS days_expired
            FROM batches b
            JOIN medicines m ON m.id = b.medicine_id
            WHERE b.status = 'expired'
            ORDER BY b.expiry_date DESC
        ";
        
        return \App\Core\Database::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if a specific batch is expired
     */
    public function isExpired(int $id): bool
    {
        $batch = $this->find($id);
        
        if (!$batch) {
            return false;
        }
        
        // Check if expiry date has passed
        $expiryDate = strtotime($batch['expiry_date']);
        $today = strtotime(date('Y-m-d'));
        
        return $expiryDate < $today;
    }
}
