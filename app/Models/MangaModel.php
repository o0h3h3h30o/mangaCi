<?php

namespace App\Models;

use CodeIgniter\Model;

class MangaModel extends Model
{
    protected $table = 'manga';
    protected $primaryKey = 'id';
    protected $allowedFields = [];

    public function getNewestManga(int $limit = 12): array
    {
        return $this->where('is_public', 1)
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->find();
    }

    public function getHotToday(int $limit = 6): array
    {
        return $this->where('is_public', 1)
            ->orderBy('view_day', 'DESC')
            ->limit($limit)
            ->find();
    }

    public function getRecentlyUpdated(int $limit = 12): array
    {
        return $this->where('is_public', 1)
            ->orderBy('update_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    public function getTopMonth(int $limit = 10): array
    {
        return $this->where('is_public', 1)
            ->orderBy('view_month', 'DESC')
            ->limit($limit)
            ->find();
    }

    public function getTopAll(int $limit = 10): array
    {
        return $this->where('is_public', 1)
            ->orderBy('views', 'DESC')
            ->limit($limit)
            ->find();
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)
            ->where('is_public', 1)
            ->first();
    }

    public function getChapters(int $mangaId): array
    {
        return $this->db->table('chapter')
            ->where('manga_id', $mangaId)
            ->where('is_show', 1)
            ->orderBy('number', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getChapterBySlug(int $mangaId, string $slug): ?array
    {
        return $this->db->table('chapter')
            ->where('manga_id', $mangaId)
            ->where('slug', $slug)
            ->where('is_show', 1)
            ->get()
            ->getRowArray() ?: null;
    }

    /**
     * Lấy pages của chapter, GROUP BY slug để tránh duplicate page.
     */
    public function getChapterPages(int $chapterId): array
    {
        return $this->db->table('page')
            ->where('chapter_id', $chapterId)
            ->groupBy('slug')
            ->orderBy('slug', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Trả về chapter trước (number nhỏ hơn) và chapter sau (number lớn hơn).
     */
    public function getPrevNextChapter(int $mangaId, float $number): array
    {
        $prev = $this->db->table('chapter')
            ->where('manga_id', $mangaId)
            ->where('number <', $number)
            ->where('is_show', 1)
            ->orderBy('number', 'DESC')
            ->limit(1)
            ->get()->getRowArray() ?: null;

        $next = $this->db->table('chapter')
            ->where('manga_id', $mangaId)
            ->where('number >', $number)
            ->where('is_show', 1)
            ->orderBy('number', 'ASC')
            ->limit(1)
            ->get()->getRowArray() ?: null;

        return ['prev' => $prev, 'next' => $next];
    }

    public function getCategories(int $mangaId): array
    {
        return $this->db->table('category c')
            ->select('c.id, c.slug, c.name')
            ->join('category_manga cm', 'c.id = cm.category_id')
            ->where('cm.manga_id', $mangaId)
            ->get()
            ->getResultArray();
    }

    public function getAuthors(int $mangaId): array
    {
        return $this->db->table('author a')
            ->select('a.id, a.name, a.slug')
            ->join('author_manga am', 'a.id = am.author_id')
            ->where('am.manga_id', $mangaId)
            ->where('am.type', 1)
            ->get()
            ->getResultArray();
    }

    public function getArtists(int $mangaId): array
    {
        return $this->db->table('author a')
            ->select('a.id, a.name, a.slug')
            ->join('author_manga am', 'a.id = am.author_id')
            ->where('am.manga_id', $mangaId)
            ->where('am.type', 2)
            ->get()
            ->getResultArray();
    }

    /**
     * Tăng view cho chapter và manga khi người dùng đọc chapter.
     */
    public function incrementViews(int $mangaId, int $chapterId): void
    {
        $this->db->query(
            'UPDATE `chapter` SET `view` = `view` + 1 WHERE `id` = ?',
            [$chapterId]
        );

        $this->db->query(
            'UPDATE `manga` SET `views` = `views` + 1, `view_day` = `view_day` + 1, `view_month` = `view_month` + 1 WHERE `id` = ?',
            [$mangaId]
        );
    }

    /**
     * Get related manga by shared category, sorted by views.
     */
    public function getRelatedByCategory(int $mangaId, int $categoryId, int $limit = 5): array
    {
        $mangaIds = array_column(
            $this->db->table('category_manga')
                ->select('manga_id')
                ->where('category_id', $categoryId)
                ->where('manga_id !=', $mangaId)
                ->get()
                ->getResultArray(),
            'manga_id'
        );

        if (empty($mangaIds)) {
            return [];
        }

        return $this->where('is_public', 1)
            ->whereIn('id', $mangaIds)
            ->orderBy('views', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Apply search filters and return $this for chaining with paginate().
     */
    public function applySearchFilters(array $f): static
    {
        $this->where('is_public', 1);

        // Keyword by title / otherNames
        if (!empty($f['filter_name'])) {
            $this->groupStart()
                ->like('name', $f['filter_name'])
                ->orLike('otherNames', $f['filter_name'])
                ->groupEnd();
        }

        // Keyword by artist (join author table)
        if (!empty($f['filter_artist'])) {
            $authorRows = $this->db->table('author')
                ->select('id')
                ->like('name', $f['filter_artist'])
                ->get()->getResultArray();
            $authorIds = array_column($authorRows, 'id');
            if (!empty($authorIds)) {
                $amRows = $this->db->table('author_manga')
                    ->select('manga_id')
                    ->whereIn('author_id', $authorIds)
                    ->get()->getResultArray();
                $this->whereIn('id', array_column($amRows, 'manga_id') ?: [0]);
            } else {
                $this->where('id', 0);
            }
        }

        // Accept genres — input is array of slugs
        if (!empty($f['accept_genres'])) {
            $catIds = array_column(
                $this->db->table('category')->select('id')->whereIn('slug', $f['accept_genres'])->get()->getResultArray(),
                'id'
            );
            $cmRows = $this->db->table('category_manga')
                ->select('manga_id')
                ->whereIn('category_id', $catIds ?: [0])
                ->get()->getResultArray();
            $this->whereIn('id', array_unique(array_column($cmRows, 'manga_id')) ?: [0]);
        }

        // Reject genres — input is array of slugs
        if (!empty($f['reject_genres'])) {
            $catIds = array_column(
                $this->db->table('category')->select('id')->whereIn('slug', $f['reject_genres'])->get()->getResultArray(),
                'id'
            );
            if (!empty($catIds)) {
                $cmRows = $this->db->table('category_manga')
                    ->select('manga_id')
                    ->whereIn('category_id', $catIds)
                    ->get()->getResultArray();
                $ids = array_unique(array_column($cmRows, 'manga_id'));
                if (!empty($ids)) {
                    $this->whereNotIn('id', $ids);
                }
            }
        }

        // Status filter
        if (!empty($f['status'])) {
            $this->where('status_id', (int) $f['status']);
        }

        $sortMap = [
            '-updated_at' => ['update_at', 'DESC'],
            '-created_at' => ['id',        'DESC'],
            'created_at'  => ['id',        'ASC'],
            '-views'      => ['views',     'DESC'],
            '-views_day'  => ['view_day',  'DESC'],
            '-views_week' => ['view_month','DESC'],
            'name'        => ['name',      'ASC'],
            '-name'       => ['name',      'DESC'],
        ];
        [$col, $dir] = $sortMap[$f['sort'] ?? '-updated_at'] ?? ['update_at', 'DESC'];
        $this->orderBy($col, $dir);

        return $this;
    }
}
