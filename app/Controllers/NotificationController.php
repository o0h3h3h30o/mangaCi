<?php

namespace App\Controllers;

class NotificationController extends BaseController
{
    private function db(): \CodeIgniter\Database\BaseConnection
    {
        return \Config\Database::connect();
    }

    private function json(array $data, int $status = 200): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON($data);
    }

    // GET /notifications  → trang quản lý toàn bộ notification
    public function listPage(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!$user = $this->currentUser) {
            return redirect()->to('/login');
        }

        $rows = $this->db()->table('notifications n')
            ->select('n.id, n.type, n.manga_slug, n.chapter_slug, n.manga_name, n.preview, n.is_read, n.created_at,
                      u.name AS actor_name, u.username AS actor_username')
            ->join('users u', 'n.actor_id = u.id', 'left')
            ->where('n.site_id', site_id())
            ->where('n.user_id', $user['id'])
            ->orderBy('n.created_at', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        $unread = array_reduce($rows, fn($c, $r) => $c + ($r['is_read'] == 0 ? 1 : 0), 0);

        return $this->themeView('notifications/index', [
            'title'         => 'Thông báo',
            'description'   => 'Quản lý thông báo',
            'categories'    => $this->categories,
            'currentUser'   => $this->currentUser,
            'notifications' => $rows,
            'unread'        => $unread,
        ]);
    }

    // GET api/notifications  → list + unread count
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$user = $this->currentUser) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $db = $this->db();

        // Chỉ trả về noti chưa đọc
        $rows = $db->table('notifications n')
            ->select('n.id, n.type, n.comment_id, n.manga_id, n.manga_slug, n.chapter_slug, n.manga_name, n.preview, n.created_at,
                      u.name AS actor_name, u.username AS actor_username')
            ->join('users u', 'n.actor_id = u.id', 'left')
            ->where('n.site_id', site_id())
            ->where('n.user_id', $user['id'])
            ->where('n.is_read', 0)
            ->orderBy('n.created_at', 'DESC')
            ->limit(30)
            ->get()->getResultArray();

        return $this->json(['notifications' => $rows, 'unread' => count($rows)]);
    }

    // POST api/notifications/read-all
    public function readAll(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$user = $this->currentUser) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $this->db()->table('notifications')
            ->where('site_id', site_id())
            ->where('user_id', $user['id'])
            ->update(['is_read' => 1]);

        return $this->json(['ok' => true]);
    }

    // POST api/notifications/(:num)/read
    public function read(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$user = $this->currentUser) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $this->db()->table('notifications')
            ->where('id', $id)
            ->where('site_id', site_id())
            ->where('user_id', $user['id'])
            ->update(['is_read' => 1]);

        return $this->json(['ok' => true]);
    }
}
