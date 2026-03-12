<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return $this->themeView('auth/login', ['title' => 'Login']);
    }

    public function loginPost(): \CodeIgniter\HTTP\RedirectResponse
    {
        $login    = trim($this->request->getPost('login') ?? '');
        $password = $this->request->getPost('password') ?? '';

        if (empty($login) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Please fill in all fields.');
        }

        $userModel = new UserModel();
        $user      = $userModel->findByLogin($login);

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid credentials.');
        }

        if (($user['active'] ?? 1) == 0) {
            return redirect()->back()->withInput()->with('error', 'Your account is inactive.');
        }

        $now = date('Y-m-d H:i:s');
        $userModel->update($user['id'], ['last_login' => $now]);

        session()->set([
            'user_id'       => $user['id'],
            'user_name'     => $user['name'] ?: $user['username'],
            'user_username' => $user['username'],
            'isLoggedIn'    => true,
            'last_login'    => $now,
        ]);

        // Remember me: cookie 7 ngày
        if ($this->request->getPost('remember')) {
            $mac   = hash_hmac('sha256', $user['id'] . '|' . $user['password'], env('encryption.key', 'fallback-secret'));
            $value = base64_encode($user['id'] . '|' . $mac);

            return redirect()->to(session()->get('redirect_back') ?: '/')
                ->with('success', 'Welcome back!')
                ->setCookie('_rm', $value, 7 * 24 * 3600);
        }

        return redirect()->to(session()->get('redirect_back') ?: '/')->with('success', 'Welcome back!');
    }

    public function register(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return $this->themeView('auth/register', ['title' => 'Register']);
    }

    public function registerPost(): \CodeIgniter\HTTP\RedirectResponse
    {
        $name     = trim($this->request->getPost('name') ?? '');
        $username = trim($this->request->getPost('username') ?? '');
        $email    = trim($this->request->getPost('email') ?? '');
        $password = $this->request->getPost('password') ?? '';
        $confirm  = $this->request->getPost('confirm_password') ?? '';

        // Validation
        if (empty($name) || empty($username) || empty($email) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Please fill in all fields.');
        }
        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            return redirect()->back()->withInput()->with('error', 'Username must be 3-30 characters (letters, numbers, underscore only).');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Invalid email address.');
        }
        if (strlen($password) < 6) {
            return redirect()->back()->withInput()->with('error', 'Password must be at least 6 characters.');
        }
        if ($password !== $confirm) {
            return redirect()->back()->withInput()->with('error', 'Passwords do not match.');
        }

        $userModel = new UserModel();

        if ($userModel->where('username', $username)->first()) {
            return redirect()->back()->withInput()->with('error', 'Username already taken.');
        }
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('error', 'Email already registered.');
        }

        $now = date('Y-m-d H:i:s');
        $id  = $userModel->insert([
            'name'       => $name,
            'username'   => $username,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'ip_address' => $this->request->getIPAddress(),
            'created_on' => time(),
            'last_login' => $now,
            'active'     => 1,
        ]);

        // Thêm vào group Members mặc định (id = 2)
        try {
            db_connect()->table('users_groups')->insert(['user_id' => $id, 'group_id' => 2]);
        } catch (\Throwable $e) {}

        session()->set([
            'user_id'       => $id,
            'user_name'     => $name ?: $username,
            'user_username' => $username,
            'isLoggedIn'    => true,
            'last_login'    => $now,
        ]);

        return redirect()->to('/')->with('success', 'Account created successfully!');
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->destroy();

        return redirect()->to('/')
            ->deleteCookie('_rm');
    }
}
