<?php

namespace App\Controllers;

use App\Models\BookmarkModel;
use App\Models\UserModel;

class Profile extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!$this->currentUser) {
            return redirect()->to('/login');
        }

        $user = (new UserModel())->find($this->currentUser['id']);

        $bookmarkCount = (new BookmarkModel())->getUserBookmarksCount($this->currentUser['id']);

        $raw     = $this->request->getCookie('_history');
        $history = json_decode(base64_decode($raw ?: '') ?: '[]', true) ?? [];

        $unreadNotiCount = (int) \Config\Database::connect()
            ->table('notifications')
            ->where('user_id', $this->currentUser['id'])
            ->where('is_read', 0)
            ->countAllResults();

        $data = [
            'title'            => 'Profile',
            'description'      => 'Your profile',
            'categories'       => $this->categories,
            'currentUser'      => $this->currentUser,
            'user'             => $user,
            'bookmarkCount'    => $bookmarkCount,
            'historyCount'     => count($history),
            'unreadNotiCount'  => $unreadNotiCount,
        ];

        return $this->themeView('profile/index', $data);
    }

    public function changePassword(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!$this->currentUser) {
            return redirect()->to('/login');
        }

        $data = [
            'title'       => 'Change Password',
            'description' => 'Change your password',
            'categories'  => $this->categories,
            'currentUser' => $this->currentUser,
        ];

        return $this->themeView('profile/change_password', $data);
    }

    public function changePasswordPost(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (!$this->currentUser) {
            return redirect()->to('/login');
        }

        $current = $this->request->getPost('current_password');
        $new     = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        $userModel = new UserModel();
        $user      = $userModel->find($this->currentUser['id']);

        if (!password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        if (strlen($new) < 6) {
            return redirect()->back()->with('error', 'New password must be at least 6 characters.');
        }

        if ($new !== $confirm) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        $userModel->update($user['id'], ['password' => password_hash($new, PASSWORD_BCRYPT)]);

        return redirect()->to('/profile')->with('success', 'Password changed successfully.');
    }
}
