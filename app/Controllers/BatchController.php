<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Batch;
use App\Models\Medicine;

class BatchController extends BaseController
{
    private Batch $batch;

    public function __construct()
    {
        $this->batch = new Batch();
    }

    public function index(Request $request)
    {
        $page   = (int) ($request->get('page', 1));
        $result = $this->batch->paginate(20, $page);

        return $this->view('batches/index', $result);
    }

    public function create(Request $request)
    {
        if (!can('batches.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/batches');
        }

        $medicines = (new Medicine())->all();
        return $this->view('batches/create', ['medicines' => $medicines]);
    }

    public function store(Request $request)
    {
        if (!can('batches.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/batches');
        }

        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'medicine_id'        => 'required',
            'batch_number'       => 'required',
            'manufacturing_date' => 'required',
            'expiry_date'        => 'required',
            'purchase_price'     => 'required',
            'selling_price'      => 'required',
            'initial_quantity'   => 'required',
        ])) {
            Session::flash('errors', $validator->errors());
            Session::flash('old_input', $data);
            return $this->redirect('/batches/create');
        }

        $this->batch->create($data);
        flash('success', 'Batch created successfully');
        return $this->redirect('/batches');
    }

    public function show(Request $request, string $id)
    {
        $batch = $this->batch->find((int) $id);

        if (!$batch) {
            flash('error', 'Batch not found');
            return $this->redirect('/batches');
        }

        return $this->view('batches/show', ['batch' => $batch]);
    }

    public function edit(Request $request, string $id)
    {
        if (!can('batches.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/batches');
        }

        $batch = $this->batch->find((int) $id);

        if (!$batch) {
            flash('error', 'Batch not found');
            return $this->redirect('/batches');
        }

        $medicines = (new Medicine())->all();
        return $this->view('batches/edit', ['batch' => $batch, 'medicines' => $medicines]);
    }

    public function update(Request $request, string $id)
    {
        if (!can('batches.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/batches');
        }

        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'medicine_id'        => 'required',
            'batch_number'       => 'required',
            'manufacturing_date' => 'required',
            'expiry_date'        => 'required',
            'purchase_price'     => 'required',
            'selling_price'      => 'required',
        ])) {
            Session::flash('errors', $validator->errors());
            return $this->redirect("/batches/{$id}/edit");
        }

        $this->batch->update((int) $id, $data);
        flash('success', 'Batch updated successfully');
        return $this->redirect('/batches');
    }

    public function destroy(Request $request, string $id)
    {
        if (!can('batches.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/batches');
        }

        $this->batch->delete((int) $id);
        flash('success', 'Batch deleted successfully');
        return $this->redirect('/batches');
    }
}
