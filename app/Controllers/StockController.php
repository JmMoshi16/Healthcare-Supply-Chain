<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Database;
use App\Models\Stock;
use App\Models\Batch;

class StockController extends BaseController
{
    private Stock $stock;

    public function __construct()
    {
        $this->stock = new Stock();
    }

    public function index(Request $request)
    {
        $filters = [
            'type'         => $request->get('type'),
            'reason_code'  => $request->get('reason_code'),
            'ward'         => $request->get('ward'),
            'performed_by' => $request->get('performed_by'),
            'date_from'    => $request->get('date_from'),
            'date_to'      => $request->get('date_to'),
            'search'       => $request->get('search'),
        ];

        $sort   = $request->get('sort', 'created_at');
        $dir    = $request->get('dir', 'DESC');
        $page   = (int) $request->get('page', 1);
        $result = $this->stock->getFiltered($filters, $sort, $dir, 20, $page);

        $users = (new \App\Models\User())->all();
        return $this->view('stocks/index', array_merge($result, ['filters' => $filters, 'users' => $users]));
    }

    public function create(Request $request)
    {
        $sql     = "SELECT b.*, m.name AS medicine_name FROM batches b JOIN medicines m ON m.id = b.medicine_id WHERE b.status = 'active' ORDER BY m.name, b.batch_number";
        $batches = \App\Core\Database::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return $this->view('stocks/create', ['batches' => $batches]);
    }

    public function store(Request $request)
    {
        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'batch_id'         => 'required',
            'transaction_type' => 'required',
            'reason_code'      => 'required',
            'quantity'         => 'required',
        ])) {
            Session::flash('errors', $validator->errors());
            Session::flash('old_input', $data);
            return $this->redirect('/stocks/create');
        }

        $data['performed_by'] = auth()['id'];

        try {
            $this->stock->create($data);
            flash('success', 'Stock transaction recorded');
        } catch (\Exception $e) {
            flash('error', $e->getMessage());
        }

        return $this->redirect('/stocks');
    }

    public function show(Request $request, string $id)
    {
        $stock = $this->stock->find((int) $id);

        if (!$stock) {
            flash('error', 'Transaction not found');
            return $this->redirect('/stocks');
        }

        return $this->view('stocks/show', ['stock' => $stock]);
    }

    public function edit(Request $request, string $id)
    {
        flash('error', 'Stock transactions cannot be edited');
        return $this->redirect('/stocks');
    }

    public function update(Request $request, string $id)
    {
        flash('error', 'Stock transactions cannot be edited');
        return $this->redirect('/stocks');
    }

    public function destroy(Request $request, string $id)
    {
        flash('error', 'Stock transactions cannot be deleted');
        return $this->redirect('/stocks');
    }
}
