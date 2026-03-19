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

        $hotToday = $mangaModel->getHotToday(12);

        $data = [
            'title'       => '',
            'description' => '',
            'newestManga' => $mangaModel->getNewestManga(24),
            'hotToday' => $hotToday,
            'recentlyUpdated' => $mangaModel->where('is_public', 1)
                ->orderBy('update_at', 'DESC')
                ->paginate(28),
            'pager' => $mangaModel->pager,
            'topDay' => array_slice($hotToday, 0, 10),
            'topMonth' => $mangaModel->getTopMonth(10),
            'topAll' => $mangaModel->getTopAll(10),
            'comictypeMap'   => $this->getComictypeMap(),
            'categories'     => $this->categories,
            'currentUser'    => $this->currentUser,
            'recentComments' => $commentModel->getRecentComments(5),
        ];
        return $this->themeView('home/index', $data);
    }

    private function getComictypeMap(): array
    {
        try {
            $rows = \Config\Database::connect()->table('comictype')->get()->getResultArray();
            $map = [];
            foreach ($rows as $r) $map[(int)$r['id']] = $r['label'] ?? $r['name'] ?? '';
            return $map;
        } catch (\Throwable $e) {
            return [];
        }
    }
}
