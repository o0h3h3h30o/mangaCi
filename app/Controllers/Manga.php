<?php

namespace App\Controllers;

use App\Models\BookmarkModel;
use App\Models\MangaModel;

class Manga extends BaseController
{
    public function detail(string $slug = ''): string
    {
        $mangaModel = new MangaModel();

        $manga = $mangaModel->getBySlug($slug);

        if (!$manga) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $id = (int) $manga['id'];

        $isBookmarked = false;
        if ($this->currentUser) {
            $bookmarkModel = new BookmarkModel();
            $isBookmarked  = $bookmarkModel->isBookmarked($this->currentUser['id'], $id);
        }

        $data = [
            'title'        => $manga['name'],
            'description'  => mb_strimwidth(strip_tags($manga['summary'] ?? ''), 0, 160, '...')
                              ?: 'Read ' . $manga['name'] . ' manga online for free',
            'manga'        => $manga,
            'chapters'     => $mangaModel->getChapters($id),
            'mangaCats'    => $mangaModel->getCategories($id),
            'authors'      => $mangaModel->getAuthors($id),
            'recommended'  => $mangaModel->getHotToday(6),
            'categories'   => $this->categories,
            'currentUser'  => $this->currentUser,
            'isBookmarked' => $isBookmarked,
        ];

        return $this->themeView('manga/detail', $data);
    }

    public function chapter(string $slug = '', string $chapterSlug = ''): string
    {
        $mangaModel = new MangaModel();
        $manga      = $mangaModel->getBySlug($slug);

        if (!$manga) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $chapter = $mangaModel->getChapterBySlug((int) $manga['id'], $chapterSlug);

        if (!$chapter) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mangaModel->incrementViews((int) $manga['id'], (int) $chapter['id']);

        $pages    = $mangaModel->getChapterPages((int) $chapter['id']);
        $chapters = $mangaModel->getChapters((int) $manga['id']);
        $pn       = $mangaModel->getPrevNextChapter((int) $manga['id'], (float) $chapter['number']);

        $chapNum   = rtrim(rtrim(number_format((float) $chapter['number'], 1), '0'), '.');
        $chapTitle = $chapter['name'] ?: 'Chapter ' . $chapNum;

        $this->saveHistory($manga, $chapterSlug);

        $data = [
            'title'       => $manga['name'] . ' - ' . $chapTitle,
            'description' => 'Read ' . $manga['name'] . ' ' . $chapTitle . ' online',
            'slug'        => $slug,
            'manga'       => $manga,
            'chapter'     => $chapter,
            'chapTitle'   => $chapTitle,
            'pages'       => $pages,
            'chapters'    => $chapters,
            'prevChapter' => $pn['prev'],
            'nextChapter' => $pn['next'],
            'categories'  => $this->categories,
            'currentUser' => $this->currentUser,
        ];

        return $this->themeView('manga/chapter', $data);
    }

    private function saveHistory(array $manga, string $chapSlug): void
    {
        $raw     = $this->request->getCookie('_history');
        $history = [];

        if ($raw) {
            $decoded = json_decode(base64_decode($raw, true) ?: '[]', true);
            if (is_array($decoded)) {
                $history = $decoded;
            }
        }

        // Bỏ entry cũ của manga này nếu có (luôn để mới nhất lên đầu)
        $history = array_values(array_filter($history, fn($e) => ($e['manga_slug'] ?? '') !== $manga['slug']));

        // Thêm entry mới vào đầu
        array_unshift($history, [
            'manga_slug' => $manga['slug'],
            'manga_name' => $manga['name'],
            'chap_slug'  => $chapSlug,
            'cover'      => manga_cover_url($manga),
            'time'       => time(),
        ]);

        // Giữ tối đa 30 entry
        $history = array_slice($history, 0, 30);

        $value = base64_encode(json_encode($history));
        // setCookie(name, value, expire, domain, path, prefix, secure, httponly)
        $this->response->setCookie('_history', $value, time() + 30 * 24 * 3600, '', '/', '', false, false);
    }
}
