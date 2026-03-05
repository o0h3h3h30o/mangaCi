<?php

namespace App\Controllers;

use App\Models\CommentModel;
use CodeIgniter\Database\BaseConnection;

class CommentController extends BaseController
{
    private function db(): BaseConnection
    {
        return \Config\Database::connect();
    }

    private function json(array $data, int $status = 200): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON($data);
    }

    private function authUser(): ?array
    {
        return $this->currentUser;
    }

    // GET api/comments/manga/{mangaId}?page=1
    public function byManga(int $mangaId): \CodeIgniter\HTTP\ResponseInterface
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit   = 10;
        $userId  = $this->currentUser['id'] ?? 0;
        $order   = in_array($this->request->getGet('order'), ['newest','oldest','top'], true)
                   ? $this->request->getGet('order')
                   : 'newest';

        try {
            $model    = new CommentModel();
            $comments = $model->getByManga($mangaId, $page, $limit, $userId, $order);
            $total    = $model->getCount($mangaId);
        } catch (\Throwable $e) {
            log_message('error', 'CommentController::byManga – ' . $e->getMessage());
            return $this->json(['comments' => [], 'total' => 0, 'page' => $page, 'hasMore' => false]);
        }

        return $this->json([
            'comments' => $comments,
            'total'    => $total,
            'page'     => $page,
            'hasMore'  => ($page * $limit) < $total,
        ]);
    }

    // GET api/comments/chapter/{chapterId}?page=1
    public function byChapter(int $chapterId): \CodeIgniter\HTTP\ResponseInterface
    {
        $page   = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit  = 10;
        $userId = $this->currentUser['id'] ?? 0;
        $order  = in_array($this->request->getGet('order'), ['newest','oldest','top'], true)
                  ? $this->request->getGet('order')
                  : 'newest';

        try {
            $model    = new CommentModel();
            $comments = $model->getByChapter($chapterId, $page, $limit, $userId, $order);
            $total    = $model->getCountByChapter($chapterId);
        } catch (\Throwable $e) {
            log_message('error', 'CommentController::byChapter – ' . $e->getMessage());
            return $this->json(['comments' => [], 'total' => 0, 'page' => $page, 'hasMore' => false]);
        }

        return $this->json([
            'comments' => $comments,
            'total'    => $total,
            'page'     => $page,
            'hasMore'  => ($page * $limit) < $total,
        ]);
    }

    // GET api/comments/manga/{mangaId}/all?page=1  — tất cả comment (manga + mọi chapter)
    public function byMangaAll(int $mangaId): \CodeIgniter\HTTP\ResponseInterface
    {
        $page   = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit  = 10;
        $userId = $this->currentUser['id'] ?? 0;
        $order  = in_array($this->request->getGet('order'), ['newest','oldest','top'], true)
                  ? $this->request->getGet('order')
                  : 'newest';

        try {
            $model    = new CommentModel();
            $comments = $model->getAllByManga($mangaId, $page, $limit, $userId, $order);
            $total    = $model->getAllCountByManga($mangaId);
        } catch (\Throwable $e) {
            log_message('error', 'CommentController::byMangaAll – ' . $e->getMessage());
            return $this->json(['comments' => [], 'total' => 0, 'page' => $page, 'hasMore' => false]);
        }

        return $this->json([
            'comments' => $comments,
            'total'    => $total,
            'page'     => $page,
            'hasMore'  => ($page * $limit) < $total,
        ]);
    }

    // GET api/comments/{id}/replies
    public function replies(int $commentId): \CodeIgniter\HTTP\ResponseInterface
    {
        $userId = $this->currentUser['id'] ?? 0;

        try {
            $model   = new CommentModel();
            $replies = $model->getReplies($commentId, $userId);
        } catch (\Throwable $e) {
            log_message('error', 'CommentController::replies – ' . $e->getMessage());
            return $this->json(['replies' => []]);
        }

        return $this->json(['replies' => $replies]);
    }

    // GET api/captcha  — trả về câu hỏi math, lưu đáp án vào session
    public function captcha(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$this->authUser()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $a = rand(1, 9);
        $b = rand(1, 9);
        session()->set('captcha_answer', $a + $b);
        session()->set('captcha_ts', time());

        return $this->json(['question' => "{$a} + {$b}"]);
    }

    // POST api/comments  {manga_id, comment[, chapter_id][, captcha_answer]}
    public function create(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$user = $this->authUser()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $mangaId   = (int) ($this->request->getPost('manga_id')      ?? 0);
        $chapterId = (int) ($this->request->getPost('chapter_id')   ?? 0);
        $parentId  = (int) ($this->request->getPost('parent_comment') ?? 0);
        $comment   = trim($this->request->getPost('comment') ?? '');

        if ($mangaId <= 0 || $comment === '') {
            return $this->json(['error' => 'Invalid data'], 400);
        }
        if (mb_strlen($comment) > 1000) {
            return $this->json(['error' => 'Max 1000 characters'], 400);
        }

        // Rate limiting: kiểm tra comment gần nhất trong 5 phút
        $lastComment = $this->db()->table('comments')
            ->where('user_id', $user['id'])
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        if ($lastComment) {
            $elapsed = time() - strtotime($lastComment['created_at']);
            if ($elapsed < 300) {
                $given    = (int) trim($this->request->getPost('captcha_answer') ?? '');
                $expected = (int) session()->get('captcha_answer');
                $ts       = (int) session()->get('captcha_ts');
                $valid    = $given && $expected && $given === $expected && (time() - $ts) < 600;

                if (!$valid) {
                    return $this->json(['error' => 'Cần xác minh captcha', 'need_captcha' => true], 429);
                }

                session()->remove('captcha_answer');
                session()->remove('captcha_ts');
            }
        }

        $isChapter = $chapterId > 0;
        $model = new CommentModel();
        $row   = [
            'comment'        => $comment,
            'post_type'      => $isChapter ? 'chapter' : 'manga',
            'post_id'        => $isChapter ? $chapterId : $mangaId,
            'manga_id'       => $mangaId,
            'user_id'        => $user['id'],
            'parent_comment' => $parentId > 0 ? $parentId : null,
        ];
        $id = $model->insert($row);

        // Nếu là reply → tạo notification cho đúng người được reply
        if ($parentId > 0) {
            // reply_to_id: comment thực sự đang được reply (khác parent_comment khi reply-to-reply)
            $replyToId = (int) ($this->request->getPost('reply_to_id') ?? 0);
            $notifyId  = $replyToId > 0 ? $replyToId : $parentId;

            $target = $this->db()->table('comments')
                ->select('user_id')
                ->where('id', $notifyId)
                ->get()->getRowArray();

            if ($target && (int) $target['user_id'] !== (int) $user['id']) {
                $manga = $this->db()->table('manga')
                    ->select('slug, name')
                    ->where('id', $mangaId)
                    ->get()->getRowArray();

                $chapterSlug = '';
                if ($isChapter) {
                    $chapterRow = $this->db()->table('chapter')
                        ->select('slug')
                        ->where('id', $chapterId)
                        ->get()->getRowArray();
                    $chapterSlug = $chapterRow['slug'] ?? '';
                }

                $this->db()->table('notifications')->insert([
                    'user_id'      => (int) $target['user_id'],
                    'actor_id'     => $user['id'],
                    'type'         => 'reply',
                    'comment_id'   => $id,
                    'manga_id'     => $mangaId,
                    'manga_slug'   => $manga['slug'] ?? '',
                    'manga_name'   => $manga['name'] ?? '',
                    'chapter_slug' => $chapterSlug,
                    'preview'      => mb_substr($comment, 0, 100),
                ]);
            }
        }

        return $this->json([
            'id'             => $id,
            'comment'        => $comment,
            'user_id'        => $user['id'],
            'user_name'      => $user['name'],
            'user_username'  => $user['username'],
            'likes_count'    => 0,
            'dislikes_count' => 0,
            'my_reaction'    => null,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    // POST api/comments/{id}/react  {type: like|dislike}
    public function react(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$user = $this->authUser()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $type = $this->request->getPost('type') ?? '';
        if (!in_array($type, ['like', 'dislike'], true)) {
            return $this->json(['error' => 'type phải là like hoặc dislike'], 400);
        }

        $db     = $this->db();
        $userId = $user['id'];

        $existing = $db->table('comment_likes')
            ->where('comment_id', $id)
            ->where('user_id', $userId)
            ->get()->getRowArray();

        if (!$existing) {
            $db->table('comment_likes')->insert([
                'comment_id' => $id,
                'user_id'    => $userId,
                'type'       => $type,
            ]);
            $myReaction = $type;
        } elseif ($existing['type'] === $type) {
            $db->table('comment_likes')->where('comment_id', $id)->where('user_id', $userId)->delete();
            $myReaction = null;
        } else {
            $db->table('comment_likes')->where('comment_id', $id)->where('user_id', $userId)
                ->update(['type' => $type]);
            $myReaction = $type;
        }

        $likes    = (int) $db->table('comment_likes')->where('comment_id', $id)->where('type', 'like')->countAllResults();
        $dislikes = (int) $db->table('comment_likes')->where('comment_id', $id)->where('type', 'dislike')->countAllResults();

        return $this->json([
            'my_reaction'    => $myReaction,
            'likes_count'    => $likes,
            'dislikes_count' => $dislikes,
        ]);
    }
}
