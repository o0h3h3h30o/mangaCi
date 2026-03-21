<?php

namespace App\Controllers;

use App\Models\BookmarkModel;

class BookmarkController extends BaseController
{
    public function toggle(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $mangaId = (int) ($this->request->getPost('manga_id') ?? 0);
        if ($mangaId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid manga_id']);
        }

        $userId        = (int) session()->get('user_id');
        $bookmarkModel = new BookmarkModel();
        $bookmarked    = $bookmarkModel->toggle($userId, $mangaId);

        MangaStateController::clearCache($mangaId);

        return $this->response->setJSON(['bookmarked' => $bookmarked]);
    }
}
