<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class UserController extends BaseController
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    private function requireSuperAdmin(): bool
    {
        if (!has_role('superadmin')) {
            flash('error', 'Access denied');
            return false;
        }
        return true;
    }

    public function index(Request $request)
    {
        if (!$this->requireSuperAdmin()) {
            return $this->redirect('/dashboard');
        }

        $page   = (int) ($request->get('page', 1));
        $result = $this->user->paginate(20, $page);

        return $this->view('users/index', $result);
    }

    public function create(Request $request)
    {
        if (!$this->requireSuperAdmin()) {
            return $this->redirect('/dashboard');
        }

        return $this->view('users/create');
    }

    public function store(Request $request)
    {
        if (!$this->requireSuperAdmin()) {
            return $this->redirect('/dashboard');
        }

        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'fullname' => 'required|min:3',
            'email'    => 'required|email',
            'password' => 'required|min:8',
            'role'     => 'required',
        ])) {
            Session::flash('errors', $validator->errors());
            Session::flash('old_input', $data);
            return $this->redirect('/users/create');
        }

        $this->user->create($data);
        flash('success', 'User created successfully');
        return $this->redirect('/users');
    }

    public function edit(Request $request, string $id)
    {
        if (!$this->requireSuperAdmin()) {
            return $this->redirect('/dashboard');
        }

        $user = $this->user->find((int) $id);

        if (!$user) {
            flash('error', 'User not found');
            return $this->redirect('/users');
        }

        return $this->view('users/edit', ['user' => $user]);
    }

    public function update(Request $request, string $id)
    {
        if (!$this->requireSuperAdmin()) {
            return $this->redirect('/dashboard');
        }

        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'fullname' => 'required|min:3',
            'email'    => 'required|email',
            'role'     => 'required',
        ])) {
            Session::flash('errors', $validator->errors());
            return $this->redirect("/users/{$id}/edit");
        }

        $this->user->update((int) $id, $data);
        flash('success', 'User updated successfully');
        return $this->redirect('/users');
    }

    public function destroy(Request $request, string $id)
    {
        if (!$this->requireSuperAdmin()) {
            return $this->redirect('/dashboard');
        }

        if ((int) $id === auth()['id']) {
            flash('error', 'Cannot delete your own account');
            return $this->redirect('/users');
        }

        $this->user->delete((int) $id);
        flash('success', 'User deleted successfully');
        return $this->redirect('/users');
    }
}
