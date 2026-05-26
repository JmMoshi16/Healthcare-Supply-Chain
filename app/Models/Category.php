<?php

namespace App\Models;

class Category extends BaseModel
{
    protected string $table = 'categories';

    protected array $fillable = [
        'name',
    ];

    public function getPaginatedWithMedicinesCount(int $perPage = 20, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        $pdo = \App\Core\Database::connect();
        
        $sql = "
            SELECT c.*, COUNT(m.id) AS medicines_count
            FROM categories c
            LEFT JOIN medicines m ON m.category_id = c.id
            GROUP BY c.id
            ORDER BY c.name ASC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $total = \App\Core\Database::query("SELECT COUNT(*) FROM categories")->fetchColumn();
        
        return [
            'data' => $data,
            'pagination' => paginate($total, $perPage, $page)
        ];
    }
}
