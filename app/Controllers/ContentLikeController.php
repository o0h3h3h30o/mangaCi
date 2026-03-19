<?php

namespace App\Controllers;

class ContentLikeController extends BaseController
{
    private function db(): \CodeIgniter\Database\BaseConnection
    {
        return \Config\Database::connect();
    }

    private function json(array $data, int $status = 200): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON($data);
    }

    /**
     * POST api/content-like  {content_type: manga|chapter, content_id, type: like|dislike}
     */
    public function toggle(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$this->currentUser) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $contentType = $this->request->getPost('content_type') ?? '';
        $contentId   = (int) ($this->request->getPost('content_id') ?? 0);
        $type        = $this->request->getPost('type') ?? '';

        if (!in_array($contentType, ['manga', 'chapter'], true) || $contentId <= 0) {
            return $this->json(['error' => 'Invalid data'], 400);
        }
        if (!in_array($type, ['like', 'dislike'], true)) {
            return $this->json(['error' => 'type must be like or dislike'], 400);
        }

        $db     = $this->db();
        $userId = (int) $this->currentUser['id'];

        $existing = $db->table('content_likes')
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('user_id', $userId)
            ->get()->getRowArray();

        if (!$existing) {
            $db->table('content_likes')->insert([
                'content_type' => $contentType,
                'content_id'   => $contentId,
                'user_id'      => $userId,
                'type'         => $type,
            ]);
            $myReaction = $type;
        } elseif ($existing['type'] === $type) {
            // Same type → remove (toggle off)
            $db->table('content_likes')
                ->where('content_type', $contentType)
                ->where('content_id', $contentId)
                ->where('user_id', $userId)
                ->delete();
            $myReaction = null;
        } else {
            // Different type → switch
            $db->table('content_likes')
                ->where('content_type', $contentType)
                ->where('content_id', $contentId)
                ->where('user_id', $userId)
                ->update(['type' => $type]);
            $myReaction = $type;
        }

        $likes = (int) $db->table('content_likes')
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('type', 'like')
            ->countAllResults();

        $dislikes = (int) $db->table('content_likes')
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('type', 'dislike')
            ->countAllResults();

        return $this->json([
            'my_reaction' => $myReaction,
            'likes'       => $likes,
            'dislikes'    => $dislikes,
        ]);
    }

    /**
     * GET api/content-like?content_type=manga&content_id=123
     */
    public function get(): \CodeIgniter\HTTP\ResponseInterface
    {
        $contentType = $this->request->getGet('content_type') ?? '';
        $contentId   = (int) ($this->request->getGet('content_id') ?? 0);
        $userId      = (int) ($this->currentUser['id'] ?? 0);

        $db = $this->db();

        $likes = (int) $db->table('content_likes')
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('type', 'like')
            ->countAllResults();

        $dislikes = (int) $db->table('content_likes')
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('type', 'dislike')
            ->countAllResults();

        $myReaction = null;
        if ($userId > 0) {
            $row = $db->table('content_likes')
                ->where('content_type', $contentType)
                ->where('content_id', $contentId)
                ->where('user_id', $userId)
                ->get()->getRowArray();
            if ($row) {
                $myReaction = $row['type'];
            }
        }

        return $this->json([
            'my_reaction' => $myReaction,
            'likes'       => $likes,
            'dislikes'    => $dislikes,
        ]);
    }
}
