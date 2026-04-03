<?php

namespace App\Controllers;

class ChapterReportController extends BaseController
{
    private function db(): \CodeIgniter\Database\BaseConnection
    {
        return \Config\Database::connect();
    }

    private function json(array $data, int $status = 200): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON($data);
    }

    // POST api/chapters/(:num)/report
    public function report(int $chapterId): \CodeIgniter\HTTP\ResponseInterface
    {
        $reason = trim($this->request->getPost('reason') ?? '');
        $note   = trim($this->request->getPost('note') ?? '');
        $ip     = real_ip();
        $userId = $this->currentUser ? (int) $this->currentUser['id'] : null;

        $allowed = ['wrong_images','missing_pages','low_quality','cant_load','wrong_order','other'];
        if (!in_array($reason, $allowed, true)) {
            return $this->json(['error' => lang('ComixxManga.invalid_reason')], 422);
        }

        $db = $this->db();

        // Rate limit: 1 report/chapter/IP per hour
        $recent = $db->table('chapter_reports')
            ->where('site_id', site_id())
            ->where('chapter_id', $chapterId)
            ->where('ip_address', $ip)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-1 hour')))
            ->countAllResults();

        if ($recent > 0) {
            return $this->json(['error' => lang('ComixxManga.already_reported')], 429);
        }

        $db->table('chapter_reports')->insert([
            'site_id'    => site_id(),
            'chapter_id' => $chapterId,
            'user_id'    => $userId,
            'reason'     => $reason,
            'note'       => mb_substr($note, 0, 300),
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->json(['ok' => true]);
    }
}
