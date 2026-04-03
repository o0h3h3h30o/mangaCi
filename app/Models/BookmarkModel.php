<?php

namespace App\Models;

use CodeIgniter\Model;

class BookmarkModel extends Model
{
    protected $table         = 'bookmarks';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['site_id', 'manga_id', 'user_id'];
    protected $useTimestamps = true;

    public function isBookmarked(int $userId, int $mangaId): bool
    {
        return $this->where('site_id', site_id())
            ->where('user_id', $userId)
            ->where('manga_id', $mangaId)
            ->countAllResults() > 0;
    }

    /**
     * Toggle bookmark. Returns true if added, false if removed.
     */
    public function toggle(int $userId, int $mangaId): bool
    {
        $existing = $this->where('site_id', site_id())
            ->where('user_id', $userId)
            ->where('manga_id', $mangaId)
            ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return false;
        }

        $this->insert(['site_id' => site_id(), 'user_id' => $userId, 'manga_id' => $mangaId]);
        return true;
    }

    public function getMangaBookmarkCount(int $mangaId): int
    {
        return (int) $this->where('site_id', site_id())->where('manga_id', $mangaId)->countAllResults();
    }

    public function getUserBookmarksCount(int $userId): int
    {
        return (int) $this->db->table('bookmarks b')
            ->join('manga m', 'm.id = b.manga_id')
            ->where('b.site_id', site_id())
            ->where('b.user_id', $userId)
            ->where('m.is_public', 1)
            ->countAllResults();
    }

    public function getUserBookmarks(int $userId, int $limit = 0, int $offset = 0): array
    {
        $builder = $this->db->table('bookmarks b')
            ->select('m.id, m.name, m.slug, m.cover, m.image, m.chapter_1, m.chap_1_slug, m.update_at, b.created_at AS bookmarked_at')
            ->join('manga m', 'm.id = b.manga_id')
            ->where('b.site_id', site_id())
            ->where('b.user_id', $userId)
            ->where('m.is_public', 1)
            ->orderBy('b.created_at', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }
}
