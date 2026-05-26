<?php

namespace App\Models;

class Medicine extends BaseModel
{
    protected string $table = 'medicines';

    // Swap 'category' with 'category_id'
    protected array $fillable = [
        'name', 'generic_name', 'category_id', 'description', 'unit', 'image', 'is_active',
    ];

    /**
     * Fetch all medicines along with their related category names
     */
    public function getAllWithCategory(): array
    {
        $sql = "
            SELECT m.*, c.name AS category_name
            FROM medicines m
            LEFT JOIN categories c ON m.category_id = c.id
            ORDER BY m.name ASC
        ";
        return \App\Core\Database::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function withBatches(int $id): ?array
    {
        // Join with categories to ensure the detailed view has the category name string
        $sql = "
            SELECT m.*, c.name AS category_name 
            FROM medicines m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.id = ?
        ";
        $medicine = \App\Core\Database::query($sql, [$id])->fetch(\PDO::FETCH_ASSOC);
        
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

    public function getLowStock(int $threshold = 10): array
    {
        // Added c.name AS category_name and the LEFT JOIN to categories
        $sql = "
            SELECT m.*, c.name AS category_name, COALESCE(SUM(b.current_quantity), 0) AS total_stock
            FROM medicines m
            LEFT JOIN categories c ON m.category_id = c.id
            LEFT JOIN batches b ON b.medicine_id = m.id AND b.status = 'active'
            WHERE m.is_active = 1
            GROUP BY m.id
            HAVING total_stock < ?
            ORDER BY total_stock ASC
        ";

        return \App\Core\Database::query($sql, [$threshold])->fetchAll(\PDO::FETCH_ASSOC);
    }
}