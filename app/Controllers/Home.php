<?php

namespace App\Controllers;

use App\Models\CommentModel;
use App\Models\MangaModel;

class Home extends BaseController
{
    public function index(): string
    {
        $mangaModel  = new MangaModel();
        $commentModel = new CommentModel();

        $data = [
            'title'       => '',
            'description' => '',
            'newestManga' => $mangaModel->getNewestManga(24),
            'hotToday' => $mangaModel->getHotToday(12),
            'recentlyUpdated' => $mangaModel->where('is_public', 1)
                ->orderBy('update_at', 'DESC')
                ->paginate(28),
            'pager' => $mangaModel->pager,
            'topDay' => $mangaModel->getHotToday(10),
            'topMonth' => $mangaModel->getTopMonth(10),
            'topAll' => $mangaModel->getTopAll(10),
            'categories'     => $this->categories,
            'currentUser'    => $this->currentUser,
            'recentComments' => $commentModel->getRecentComments(15),
        ];
        return $this->themeView('home/index', $data);
    }
}
