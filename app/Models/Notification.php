<?php

namespace App\Models;

class Notification extends BaseModel
{
    protected string $table = 'notifications';

    protected array $fillable = [
        'type', 'urgency', 'icon', 'title', 'message', 'link', 'is_read', 'user_id',
    ];

    /**
     * Get all unread notifications (broadcast + user-specific), newest first
     */
    public function getForUser(?int $userId, int $limit = 30): array
    {
        $sql = "
            SELECT *
            FROM notifications
            WHERE (user_id IS NULL OR user_id = ?)
            ORDER BY created_at DESC
            LIMIT ?
        ";
        return \App\Core\Database::query($sql, [$userId, $limit])
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Count unread notifications for a user
     */
    public function countUnread(?int $userId): int
    {
        $sql = "
            SELECT COUNT(*) as cnt
            FROM notifications
            WHERE (user_id IS NULL OR user_id = ?)
              AND is_read = 0
        ";
        $row = \App\Core\Database::query($sql, [$userId])->fetch(\PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    /**
     * Mark a single notification as read
     */
    public function markRead(int $id): void
    {
        \App\Core\Database::query(
            "UPDATE notifications SET is_read = 1 WHERE id = ?",
            [$id]
        );
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllRead(?int $userId): void
    {
        $sql = "
            UPDATE notifications
            SET is_read = 1
            WHERE (user_id IS NULL OR user_id = ?)
        ";
        \App\Core\Database::query($sql, [$userId]);
    }

    /**
     * Create a broadcast notification (visible to all users)
     */
    public static function broadcast(
        string $type,
        string $urgency,
        string $icon,
        string $title,
        string $message,
        string $link = '/dashboard'
    ): void {
        $model = new self();
        $model->create(compact('type', 'urgency', 'icon', 'title', 'message', 'link'));
    }
}
