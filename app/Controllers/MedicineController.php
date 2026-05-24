<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Medicine;
use App\Models\Category;

class MedicineController extends BaseController
{
    private Medicine $medicine;

    public function __construct()
    {
        $this->medicine = new Medicine();
    }

    public function index(Request $request)
    {
        $page   = (int) ($request->get('page', 1));
        
        // Get all medicines including inactive ones
        $offset = ($page - 1) * 20;
        $medicines = $this->medicine->query()
            ->orderBy('created_at', 'DESC')
            ->limit(20, $offset)
            ->get();
        
        $total = $this->medicine->query()->count();
        
        $result = [
            'data' => $medicines,
            'pagination' => paginate($total, 20, $page)
        ];

        return $this->view('medicines/index', $result);
    }

    public function create(Request $request)
    {
        if (!can('medicines.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/medicines');
        }

        $categories = (new Category())->all();
        return $this->view('medicines/create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        if (!can('medicines.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/medicines');
        }

        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'name'        => 'required|min:3|max:255',
            'category_id' => 'required|numeric',
            'unit'        => 'required',
        ])) {
            Session::flash('errors', $validator->errors());
            Session::flash('old_input', $data);
            return $this->redirect('/medicines/create');
        }

        if ($request->hasFile('image')) {
            $data['image'] = upload_file($request->file('image'), 'uploads/medicines');
        }

        $this->medicine->create($data);
        flash('success', 'Medicine created successfully');
        return $this->redirect('/medicines');
    }

    public function show(Request $request, string $id)
    {
        $medicine = $this->medicine->withBatches((int) $id);

        if (!$medicine) {
            flash('error', 'Medicine not found');
            return $this->redirect('/medicines');
        }

        return $this->view('medicines/show', ['medicine' => $medicine]);
    }

    public function edit(Request $request, string $id)
    {
        if (!can('medicines.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/medicines');
        }

        $medicine = $this->medicine->find((int) $id);

        if (!$medicine) {
            flash('error', 'Medicine not found');
            return $this->redirect('/medicines');
        }

        $categories = (new Category())->all();
        return $this->view('medicines/edit', ['medicine' => $medicine, 'categories' => $categories]);
    }

    public function update(Request $request, string $id)
    {
        if (!can('medicines.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/medicines');
        }

        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'name'        => 'required|min:3|max:255',
            'category_id' => 'required|numeric',
            'unit'        => 'required',
        ])) {
            Session::flash('errors', $validator->errors());
            return $this->redirect("/medicines/{$id}/edit");
        }

        if ($request->hasFile('image')) {
            $data['image'] = upload_file($request->file('image'), 'uploads/medicines');
        }
        
        // Ensure is_active is set correctly (0 or 1)
        $data['is_active'] = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        $this->medicine->update((int) $id, $data);
        
        $statusMsg = $data['is_active'] ? 'activated' : 'deactivated';
        flash('success', "Medicine updated and {$statusMsg} successfully");
        return $this->redirect('/medicines');
    }

    public function destroy(Request $request, string $id)
    {
        if (!can('medicines.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/medicines');
        }

        $this->medicine->delete((int) $id);
        flash('success', 'Medicine deleted successfully');
        return $this->redirect('/medicines');
    }
}
