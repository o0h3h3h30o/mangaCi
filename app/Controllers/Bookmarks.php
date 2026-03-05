<?php

namespace App\Controllers;

use App\Models\BookmarkModel;

class Bookmarks extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!$this->currentUser) {
            return redirect()->to('/login');
        }

        $perPage = 24;
        $page    = max(1, (int) $this->request->getGet('page'));
        $offset  = ($page - 1) * $perPage;

        $bookmarkModel = new BookmarkModel();
        $total         = $bookmarkModel->getUserBookmarksCount($this->currentUser['id']);
        $bookmarks     = $bookmarkModel->getUserBookmarks($this->currentUser['id'], $perPage, $offset);

        $pager = service('pager');
        $pager->store('default', $page, $perPage, $total);

        $data = [
            'title'       => 'Following',
            'description' => 'Manga you are following',
            'categories'  => $this->categories,
            'currentUser' => $this->currentUser,
            'bookmarks'   => $bookmarks,
            'pager'       => $pager,
            'total'       => $total,
        ];
        return $this->themeView('bookmarks/index', $data);
    }
}
