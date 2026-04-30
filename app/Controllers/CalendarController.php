<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class CalendarController extends BaseController
{
    public function getNotes()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $month = Request::get('month', date('Y-m'));
        
        $sql = "SELECT * FROM calendar_notes 
                WHERE user_id = ? AND DATE_FORMAT(note_date, '%Y-%m') = ?
                ORDER BY note_date ASC, created_at DESC";
        
        $stmt = Database::query($sql, [$userId, $month]);
        $notes = $stmt->fetchAll();
        
        Response::json(['success' => true, 'notes' => $notes]);
    }
    
    public function store()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $data = Request::all();
        
        $sql = "INSERT INTO calendar_notes (user_id, note_date, title, description, priority) 
                VALUES (?, ?, ?, ?, ?)";
        
        Database::query($sql, [
            $userId,
            $data['note_date'],
            $data['title'],
            $data['description'] ?? '',
            $data['priority'] ?? 'medium'
        ]);
        
        Response::json(['success' => true, 'message' => 'Note added successfully']);
    }
    
    public function update()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $data = Request::all();
        
        $sql = "UPDATE calendar_notes 
                SET title = ?, description = ?, priority = ?
                WHERE id = ? AND user_id = ?";
        
        Database::query($sql, [
            $data['title'],
            $data['description'] ?? '',
            $data['priority'] ?? 'medium',
            $data['id'],
            $userId
        ]);
        
        Response::json(['success' => true, 'message' => 'Note updated successfully']);
    }
    
    public function delete()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $id = Request::post('id');
        
        $sql = "DELETE FROM calendar_notes WHERE id = ? AND user_id = ?";
        Database::query($sql, [$id, $userId]);
        
        Response::json(['success' => true, 'message' => 'Note deleted successfully']);
    }
}
