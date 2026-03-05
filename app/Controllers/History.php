<?php

namespace App\Controllers;

class History extends BaseController
{
    public function index(): string
    {
        $history = [];
        $raw = $this->request->getCookie('_history');
        if ($raw) {
            $decoded = json_decode(base64_decode($raw, true) ?: '[]', true);
            if (is_array($decoded)) {
                $history = $decoded;
            }
        }

        $data = [
            'title'       => 'Reading History',
            'description' => 'Your manga reading history',
            'categories'  => $this->categories,
            'currentUser' => $this->currentUser,
            'history'     => $history,
        ];
        return $this->themeView('history/index', $data);
    }
}
