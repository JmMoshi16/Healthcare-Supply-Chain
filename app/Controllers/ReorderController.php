<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Medicine;

class ReorderController extends BaseController
{
    public function index(Request $request)
    {
        if (!can('medicines.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/dashboard');
        }

        $medicineModel = new Medicine();
        $lowStockItems = $medicineModel->getLowStock();

        return $this->view('reorder/index', [
            'lowStockItems' => $lowStockItems
        ]);
    }
}
