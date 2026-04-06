<?php

namespace App\Controllers;

use App\Models\MangaModel;
use App\Models\RatingModel;
use App\Models\BookmarkModel;
use Config\Database;

class Manga extends BaseController
{
    public function random()
    {
        $manga = db_connect()->table('manga')
            ->select('slug')
            ->where('is_public', 1)
            ->orderBy('RAND()')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (!$manga) return redirect()->to('/');
        return redirect()->to('/manga/' . $manga['slug']);
    }

    public function detail(string $slug = ''): string
    {
        $mangaModel = new MangaModel();

        $manga = $mangaModel->getBySlug($slug);

        if (!$manga) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $id = (int) $manga['id'];

        $ratingModel = new RatingModel();
        $ratingStats = $ratingModel->getStats($id);
        $myRating    = $ratingModel->findByIp($id, $this->request->getIPAddress());

        $data = [
            'title'        => $manga['name'],
            'description'  => mb_strimwidth(strip_tags($manga['summary'] ?? ''), 0, 160, '...')
                              ?: 'Read ' . $manga['name'] . ' manga online for free',
            'og_image'     => manga_cover_url($manga),
            'manga'        => $manga,
            'chapters'     => $mangaModel->getChapters($id),
            'mangaCats'    => $mangaCats = $mangaModel->getCategories($id),
            'authors'      => $mangaModel->getAuthors($id),
            'artists'      => $mangaModel->getArtists($id),
            'recommended'  => !empty($mangaCats)
                ? $mangaModel->getRelatedByCategory($id, (int) $mangaCats[0]['id'], 6)
                : $mangaModel->getHotToday(6),
            'categories'   => $this->categories,
            'currentUser'  => $this->currentUser,
            'ratingAvg'    => $ratingStats['avg'],
            'ratingVotes'  => $ratingStats['votes'],
            'myRating'     => $myRating ? (int) $myRating['score'] : 0,
            'isBookmarked' => $this->currentUser
                ? ($bm = new BookmarkModel())->isBookmarked((int) $this->currentUser['id'], $id)
                : false,
            'followCount'  => (isset($bm) ? $bm : new BookmarkModel())->getMangaBookmarkCount($id),
            'mangaTags'    => $mangaModel->getTags($id),
        ] + $this->getContentLikes('manga', $id);

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

        // View tracking via JS POST /api/view (bypasses Cloudflare cache)

        $pages    = $mangaModel->getChapterPages((int) $chapter['id']);
        $chapters = $mangaModel->getChapters((int) $manga['id']);
        $pn       = $mangaModel->getPrevNextChapter((int) $manga['id'], (float) $chapter['number']);
      
        $chapNum   = rtrim(rtrim(number_format((float) $chapter['number'], 1), '0'), '.');
        $chapTitle = $chapter['name'] ?: 'Chapter ' . $chapNum;

        $this->saveHistory($manga, $chapterSlug);

        $id = (int) $manga['id'];
        $bm = new BookmarkModel();

        $data = [
            'title'       => $manga['name'] . ' - ' . $chapTitle,
            'description' => 'Read ' . $manga['name'] . ' ' . $chapTitle . ' online',
            'og_image'    => manga_cover_url($manga),
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
            'isBookmarked' => $this->currentUser
                ? $bm->isBookmarked((int) $this->currentUser['id'], $id)
                : false,
        ] + $this->getContentLikes('chapter', (int) $chapter['id']);
                return $this->themeView('manga/chapter', $data);
    }

    /**
     * Get likes/dislikes/myReaction for a content item (manga or chapter).
     */
    private function getContentLikes(string $contentType, int $contentId): array
    {
        $db  = Database::connect();
        $sid = site_id();

        $likes = (int) $db->table('content_likes')
            ->where('site_id', $sid)
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('type', 'like')
            ->countAllResults();

        $dislikes = (int) $db->table('content_likes')
            ->where('site_id', $sid)
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('type', 'dislike')
            ->countAllResults();

        $myReaction = null;
        if ($this->currentUser) {
            $row = $db->table('content_likes')
                ->where('site_id', $sid)
                ->where('content_type', $contentType)
                ->where('content_id', $contentId)
                ->where('user_id', (int) $this->currentUser['id'])
                ->get()->getRowArray();
            if ($row) {
                $myReaction = $row['type'];
            }
        }

        return [
            'likes'      => $likes,
            'dislikes'   => $dislikes,
            'myReaction' => $myReaction,
        ];
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
