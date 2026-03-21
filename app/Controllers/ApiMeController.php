<?php

namespace App\Controllers;

class ApiMeController extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$this->currentUser) {
            return $this->response->setJSON(['logged_in' => false]);
        }

        $db = \Config\Database::connect();

        // Unread notifications count
        $unread = 0;
        try {
            $unread = (int) $db->table('notifications')
                ->where('user_id', $this->currentUser['id'])
                ->where('is_read', 0)
                ->countAllResults();
        } catch (\Throwable $e) {}

        // Check admin
        $isAdmin = false;
        try {
            $isAdmin = $db->table('users_groups')
                ->where('user_id', $this->currentUser['id'])
                ->where('group_id', 1)
                ->countAllResults() > 0;
        } catch (\Throwable $e) {}

        return $this->response->setJSON([
            'logged_in'    => true,
            'id'           => $this->currentUser['id'],
            'name'         => $this->currentUser['name'],
            'username'     => $this->currentUser['username'],
            'unread_count' => $unread,
            'is_admin'     => $isAdmin,
        ]);
    }
}
