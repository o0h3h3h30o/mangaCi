<?php

namespace App\Controllers;

use App\Models\RatingModel;

class MangaStateController extends BaseController
{
    /**
     * GET /api/manga/{id}/state
     * Returns all dynamic data for a manga page (cached 15s for public data)
     */
    public function index(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $cache    = \Config\Services::cache();
        $cacheKey = 'manga_state_' . $id;

        // Public data (rating, follow count, likes) — cached 15s
        $public = $cache->get($cacheKey);
        if (!$public) {
            $db = \Config\Database::connect();

            // Rating
            $ratingModel = new RatingModel();
            $ratingStats = $ratingModel->getStats($id);

            // Follow count
            $followCount = (int) $db->table('bookmarks')
                ->where('manga_id', $id)
                ->countAllResults();

            // Likes / Dislikes
            $likes = (int) $db->table('content_likes')
                ->where('content_type', 'manga')
                ->where('content_id', $id)
                ->where('type', 'like')
                ->countAllResults();

            $dislikes = (int) $db->table('content_likes')
                ->where('content_type', 'manga')
                ->where('content_id', $id)
                ->where('type', 'dislike')
                ->countAllResults();

            $public = [
                'rating_avg'   => $ratingStats['avg'],
                'rating_votes' => $ratingStats['votes'],
                'follow_count' => $followCount,
                'likes'        => $likes,
                'dislikes'     => $dislikes,
            ];

            $cache->save($cacheKey, $public, 15);
        }

        // User-specific data (not cached)
        $myRating   = 0;
        $isBookmarked = false;
        $myReaction = null;

        $ip = real_ip();
        $ratingModel = new RatingModel();
        $existing = $ratingModel->findByIp($id, $ip);
        if ($existing) $myRating = (int) $existing['score'];

        if ($this->currentUser) {
            $db = \Config\Database::connect();
            $userId = (int) $this->currentUser['id'];

            $isBookmarked = $db->table('bookmarks')
                ->where('manga_id', $id)
                ->where('user_id', $userId)
                ->countAllResults() > 0;

            $likeRow = $db->table('content_likes')
                ->where('content_type', 'manga')
                ->where('content_id', $id)
                ->where('user_id', $userId)
                ->get()->getRowArray();
            if ($likeRow) $myReaction = $likeRow['type'];
        }

        return $this->response->setJSON(array_merge($public, [
            'my_rating'     => $myRating,
            'is_bookmarked' => $isBookmarked,
            'my_reaction'   => $myReaction,
        ]));
    }

    /**
     * POST /api/view — track chapter view via JS (bypasses Cloudflare cache)
     */
    public function trackView(): \CodeIgniter\HTTP\ResponseInterface
    {
        $mangaId   = (int) $this->request->getPost('manga_id');
        $chapterId = (int) $this->request->getPost('chapter_id');

        if ($mangaId && $chapterId) {
            $mangaModel = new \App\Models\MangaModel();
            $mangaModel->incrementViews($mangaId, $chapterId);
        }

        return $this->response->setJSON(['ok' => true]);
    }

    /**
     * Clear manga state cache (called after rate/like/bookmark actions)
     */
    public static function clearCache(int $mangaId): void
    {
        \Config\Services::cache()->delete('manga_state_' . $mangaId);
    }
}
