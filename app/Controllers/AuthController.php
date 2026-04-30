<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class AuthController extends BaseController
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    // GET /login
    public function loginForm(Request $request)
    {
        if (is_logged_in()) {
            return $this->redirect($this->dashboardByRole(auth()['role'] ?? ''));
        }

        return $this->view('auth/login');
    }

    // POST /login
    public function login(Request $request)
    {
        $data      = $request->all();
        $validator = new Validator($data);

        if (!$validator->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ])) {
            Session::flash('error', 'Please enter a valid email and password.');
            Session::flash('old_input', $data);
            return $this->redirect('/login');
        }

        $user = $this->user->authenticate($data['email'], $data['password']);

        if (!$user) {
            Session::flash('error', 'Invalid email or password. Please try again.');
            Session::flash('old_input', $data);
            return $this->redirect('/login');
        }

        Session::set('user', $user);

        return $this->redirect($this->dashboardByRole($user['role'] ?? ''));
    }

    // GET /register
    public function registerForm(Request $request)
    {
        if (is_logged_in()) {
            return $this->redirect('/dashboard');
        }

        return $this->view('auth/register');
    }

    // POST /register
    public function register(Request $request)
    {
        $data      = $request->all();
        $validator = new Validator($data);

        // Base validation
        if (!$validator->validate([
            'fullname'         => 'required|min:2|max:255',
            'role'             => 'required|in:superadmin,manager,staff',
            'verify_code'      => 'required',
            'email'            => 'required|email',
            'password'         => 'required|min:8',
            'password_confirm' => 'required',
        ])) {
            Session::flash('error', implode(' ', array_merge(...array_values($validator->errors()))));
            Session::flash('old_input', $data);
            return $this->redirect('/register');
        }

        // Password confirmation
        if ($data['password'] !== $data['password_confirm']) {
            Session::flash('error', 'Passwords do not match.');
            Session::flash('old_input', $data);
            return $this->redirect('/register');
        }

        // Email uniqueness
        if ($this->user->findByEmail($data['email'])) {
            Session::flash('error', 'That email address is already registered.');
            Session::flash('old_input', $data);
            return $this->redirect('/register');
        }

        // Verify role code
        if (!$this->verifyRoleCode($data['role'], trim($data['verify_code']))) {
            $labels = ['superadmin' => 'Admin code', 'manager' => 'Manager ID', 'staff' => 'Staff ID'];
            Session::flash('error', ($labels[$data['role']] ?? 'Verification code') . ' is invalid. Please check and try again.');
            Session::flash('old_input', $data);
            return $this->redirect('/register');
        }

        // Create user
        $this->user->create([
            'fullname'  => clean($data['fullname']),
            'email'     => strtolower(trim($data['email'])),
            'password'  => $data['password'],
            'role'      => $data['role'],
            'is_active' => 1,
        ]);

        Session::flash('success', 'Account created successfully! Please sign in.');
        return $this->redirect('/login');
    }

    // GET /logout
    public function logout(Request $request)
    {
        Session::destroy();
        return $this->redirect('/login');
    }

    // ── Private helpers ──────────────────────────────────────────

    private function dashboardByRole(string $role): string
    {
        return match($role) {
            'superadmin', 'manager' => '/dashboard',
            'staff'                 => '/dashboard',
            default                 => '/dashboard',
        };
    }

    private function verifyRoleCode(string $role, string $code): bool
    {
        $codes = require __DIR__ . '/../../config/registration_codes.php';

        return match($role) {
            'superadmin' => $code === $codes['admin_code'],
            'manager'    => in_array($code, $codes['manager_ids'], true),
            'staff'      => in_array($code, $codes['staff_ids'], true),
            default      => false,
        };
    }
}
