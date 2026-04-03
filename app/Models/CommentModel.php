<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table         = 'comments';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['site_id', 'comment', 'post_type', 'post_id', 'manga_id', 'user_id', 'parent_comment'];
    protected $useTimestamps = true;

    public function getRecentComments(int $limit = 15): array
    {
        return $this->db->table('comments c')
            ->select('c.id, c.comment, c.post_type, c.post_id, c.created_at,
                      m.name AS manga_name, m.slug AS manga_slug,
                      u.name AS user_name, u.username AS user_username,
                      ch.name AS chapter_name, ch.slug AS chapter_slug')
            ->join('manga m', 'c.manga_id = m.id', 'left')
            ->join('users u', 'c.user_id = u.id', 'left')
            ->join('chapter ch', 'c.post_id = ch.id AND c.post_type = \'chapter\'', 'left')
            ->where('c.site_id', site_id())
            ->where('m.is_public', 1)
            ->whereNotIn('c.comment', [''])
            ->orderBy('c.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Lấy danh sách comment của manga, kèm like/dislike counts và reaction của user hiện tại.
     */
    public function getByManga(int $mangaId, int $page = 1, int $limit = 20, int $userId = 0, string $order = 'newest'): array
    {
        $offset = ($page - 1) * $limit;

        $orderMap = [
            'newest' => 'c.created_at DESC',
            'oldest' => 'c.created_at ASC',
            'top'    => 'likes_count DESC, c.created_at DESC',
        ];
        $orderSql = $orderMap[$order] ?? 'c.created_at DESC';

        $sql = "
            SELECT
                c.id, c.comment, c.user_id, c.created_at,
                u.name       AS user_name,
                u.username   AS user_username,
                (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.type = 'like')    AS likes_count,
                (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.type = 'dislike') AS dislikes_count,
                (SELECT cl.type  FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.user_id = ?)      AS my_reaction,
                (SELECT COUNT(*) FROM comments r WHERE r.parent_comment = c.id)                            AS reply_count
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.site_id = ? AND c.manga_id = ? AND c.post_type = 'manga' AND c.parent_comment IS NULL
            ORDER BY {$orderSql}
            LIMIT ? OFFSET ?
        ";

        return $this->db->query($sql, [site_id(), $userId, $mangaId, $limit, $offset])->getResultArray();
    }

    /**
     * Đếm tổng số comment của manga.
     */
    public function getCount(int $mangaId): int
    {
        return (int) $this->db->table('comments')
            ->where('site_id', site_id())
            ->where('manga_id', $mangaId)
            ->where('post_type', 'manga')
            ->where('parent_comment', null)
            ->countAllResults();
    }

    /**
     * Lấy replies của một comment.
     */
    public function getReplies(int $parentId, int $userId = 0): array
    {
        $sql = "
            SELECT
                c.id, c.comment, c.user_id, c.created_at,
                u.name     AS user_name,
                u.username AS user_username,
                (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.type = 'like')    AS likes_count,
                (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.type = 'dislike') AS dislikes_count,
                (SELECT cl.type  FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.user_id = ?)      AS my_reaction
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.site_id = ? AND c.parent_comment = ?
            ORDER BY c.created_at ASC
        ";

        return $this->db->query($sql, [site_id(), $userId, $parentId])->getResultArray();
    }

    /**
     * Lấy tất cả comment của manga (cả manga lẫn chapter), không phân biệt post_type.
     */
    public function getAllByManga(int $mangaId, int $page = 1, int $limit = 10, int $userId = 0, string $order = 'newest'): array
    {
        $offset = ($page - 1) * $limit;

        $orderMap = [
            'newest' => 'c.created_at DESC',
            'oldest' => 'c.created_at ASC',
            'top'    => 'likes_count DESC, c.created_at DESC',
        ];
        $orderSql = $orderMap[$order] ?? 'c.created_at DESC';

        $sql = "
            SELECT
                c.id, c.comment, c.user_id, c.created_at, c.post_type,
                u.name       AS user_name,
                u.username   AS user_username,
                (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.type = 'like')    AS likes_count,
                (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.type = 'dislike') AS dislikes_count,
                (SELECT cl.type  FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.user_id = ?)      AS my_reaction,
                (SELECT COUNT(*) FROM comments r WHERE r.parent_comment = c.id)                            AS reply_count,
                ch.slug AS chapter_slug, ch.name AS chapter_name
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN chapter ch ON c.post_type = 'chapter' AND c.post_id = ch.id
            WHERE c.site_id = ? AND c.manga_id = ? AND c.parent_comment IS NULL
            ORDER BY {$orderSql}
            LIMIT ? OFFSET ?
        ";

        return $this->db->query($sql, [site_id(), $userId, $mangaId, $limit, $offset])->getResultArray();
    }

    public function getAllCountByManga(int $mangaId): int
    {
        return (int) $this->db->table('comments')
            ->where('site_id', site_id())
            ->where('manga_id', $mangaId)
            ->where('parent_comment', null)
            ->countAllResults();
    }

    /**
     * Lấy danh sách comment của chapter.
     */
    public function getByChapter(int $chapterId, int $page = 1, int $limit = 10, int $userId = 0, string $order = 'newest'): array
    {
        $offset = ($page - 1) * $limit;

        $orderMap = [
            'newest' => 'c.created_at DESC',
            'oldest' => 'c.created_at ASC',
            'top'    => 'likes_count DESC, c.created_at DESC',
        ];
        $orderSql = $orderMap[$order] ?? 'c.created_at DESC';

        $sql = "
            SELECT
                c.id, c.comment, c.user_id, c.created_at,
                u.name       AS user_name,
                u.username   AS user_username,
                (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.type = 'like')    AS likes_count,
                (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.type = 'dislike') AS dislikes_count,
                (SELECT cl.type  FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.user_id = ?)      AS my_reaction,
                (SELECT COUNT(*) FROM comments r WHERE r.parent_comment = c.id)                            AS reply_count
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.site_id = ? AND c.post_id = ? AND c.post_type = 'chapter' AND c.parent_comment IS NULL
            ORDER BY {$orderSql}
            LIMIT ? OFFSET ?
        ";

        return $this->db->query($sql, [site_id(), $userId, $chapterId, $limit, $offset])->getResultArray();
    }

    /**
     * Đếm tổng số comment của chapter.
     */
    public function getCountByChapter(int $chapterId): int
    {
        return (int) $this->db->table('comments')
            ->where('site_id', site_id())
            ->where('post_id', $chapterId)
            ->where('post_type', 'chapter')
            ->where('parent_comment', null)
            ->countAllResults();
    }
}
