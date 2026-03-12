<?php

namespace App\Controllers;

use App\Models\RatingModel;

class RatingController extends BaseController
{
    public function rate(): \CodeIgniter\HTTP\ResponseInterface
    {
        $itemId = (int) ($this->request->getPost('item_id') ?? 0);
        $score  = (int) ($this->request->getPost('score') ?? 0);

        if ($itemId <= 0 || $score < 1 || $score > 10) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid parameters']);
        }

        $ip          = $this->request->getIPAddress();
        $ratingModel = new RatingModel();
        $result      = $ratingModel->rate($itemId, $score, $ip);

        return $this->response->setJSON($result);
    }

    public function stats(int $itemId): \CodeIgniter\HTTP\ResponseInterface
    {
        $ratingModel = new RatingModel();
        $stats       = $ratingModel->getStats($itemId);

        $existing = $ratingModel->findByIp($itemId, $this->request->getIPAddress());
        $stats['your_score'] = $existing ? (int) $existing['score'] : 0;

        return $this->response->setJSON($stats);
    }
}
