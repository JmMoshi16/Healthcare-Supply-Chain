<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\ActivityLog;

class ActivityLogController extends BaseController
{
    private ActivityLog $activityLog;

    public function __construct()
    {
        $this->activityLog = new ActivityLog();
    }

    public function index(Request $request)
    {
        if (!can('users.*')) { // using superadmin permission for now
            flash('error', 'Access denied');
            return $this->redirect('/');
        }

        $page = (int) ($request->get('page', 1));
        
        // Fetch paginated logs, ordered by newest first
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $sql = "
            SELECT a.*, u.fullname as user_name 
            FROM activity_logs a 
            LEFT JOIN users u ON a.user_id = u.id 
            ORDER BY a.timestamp DESC 
            LIMIT ? OFFSET ?
        ";
        
        $logs = \App\Core\Database::query($sql, [$perPage, $offset])->fetchAll(\PDO::FETCH_ASSOC);
        
        $totalSql = "SELECT COUNT(*) FROM activity_logs";
        $total = (int) \App\Core\Database::query($totalSql)->fetchColumn();
        
        $pagination = paginate($total, $perPage, $page);

        return $this->view('activity_logs/index', [
            'logs' => $logs,
            'pagination' => $pagination
        ]);
    }
}
