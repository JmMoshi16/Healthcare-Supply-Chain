<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Category;

class CategoryController extends BaseController
{
    private Category $category;

    public function __construct()
    {
        $this->category = new Category();
    }

    public function index(Request $request)
    {
        $page = (int) ($request->get('page', 1));
        $result = $this->category->getPaginatedWithMedicinesCount(20, $page);

        return $this->view('categories/index', $result);
    }

    public function create(Request $request)
    {
        if (!can('categories.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/dashboard');
        }
        return $this->view('categories/create');
    }

    public function store(Request $request)
    {
        if (!can('categories.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/dashboard');
        }

        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'name' => 'required|min:3|max:255',
        ])) {
            Session::flash('errors', $validator->errors());
            Session::flash('old_input', $data);
            return $this->redirect('/categories/create');
        }

        // Check unique constraint manually to give user-friendly error
        $existing = $this->category->query()->where('name', $data['name'])->first();
        if ($existing) {
            Session::flash('errors', ['name' => ['Category name already exists.']]);
            Session::flash('old_input', $data);
            return $this->redirect('/categories/create');
        }

        $this->category->create($data);
        flash('success', 'Category created successfully');
        return $this->redirect('/categories');
    }

    public function edit(Request $request, string $id)
    {
        if (!can('categories.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/dashboard');
        }

        $category = $this->category->find((int) $id);

        if (!$category) {
            flash('error', 'Category not found');
            return $this->redirect('/categories');
        }

        return $this->view('categories/edit', ['category' => $category]);
    }

    public function update(Request $request, string $id)
    {
        if (!can('categories.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/dashboard');
        }

        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'name' => 'required|min:3|max:255',
        ])) {
            Session::flash('errors', $validator->errors());
            return $this->redirect("/categories/{$id}/edit");
        }

        // Check unique constraint but ignore self
        $existing = $this->category->query()->where('name', $data['name'])->first();
        if ($existing && (int)$existing['id'] !== (int)$id) {
            Session::flash('errors', ['name' => ['Category name already exists.']]);
            return $this->redirect("/categories/{$id}/edit");
        }

        $this->category->update((int) $id, $data);
        flash('success', 'Category updated successfully');
        return $this->redirect('/categories');
    }

    public function destroy(Request $request, string $id)
    {
        if (!can('categories.*')) {
            flash('error', 'Access denied');
            return $this->redirect('/dashboard');
        }

        // Check if there are medicines associated with this category
        $medicinesCount = \App\Core\Database::query(
            "SELECT COUNT(*) FROM medicines WHERE category_id = ?",
            [(int) $id]
        )->fetchColumn();

        if ($medicinesCount > 0) {
            flash('error', 'Cannot delete category: it has associated medicines.');
            return $this->redirect('/categories');
        }

        $this->category->delete((int) $id);
        flash('success', 'Category deleted successfully');
        return $this->redirect('/categories');
    }
}
