<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
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
        $page   = (int) ($request->get('page', 1));
        $result = $this->stock->paginate(20, $page);

        return $this->view('stocks/index', $result);
    }

    public function create(Request $request)
    {
        $batches = (new Batch())->all();
        return $this->view('stocks/create', ['batches' => $batches]);
    }

    public function store(Request $request)
    {
        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'batch_id'         => 'required',
            'transaction_type' => 'required',
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
