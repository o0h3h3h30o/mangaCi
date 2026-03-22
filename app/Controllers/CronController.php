<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class CronController extends Controller
{
    /**
     * GET /cron/reset-views?key=SECRET
     * Reset view_day daily, view_week weekly (Monday), view_month on 1st.
     * Set CRON_KEY in .env and call daily via cron.
     */
    public function resetViews()
    {
        $secret = env('CRON_KEY', '');
        if (!$secret || $this->request->getGet('key') !== $secret) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $db = \Config\Database::connect();
        $actions = [];

        // Always reset daily views
        $db->query('UPDATE `manga` SET `view_day` = 0');
        $actions[] = 'view_day reset';

        // Reset weekly on Monday
        if ((int) date('N') === 1) {
            $db->query('UPDATE `manga` SET `view_week` = 0');
            $actions[] = 'view_week reset';
        }

        // Reset monthly on 1st
        if ((int) date('j') === 1) {
            $db->query('UPDATE `manga` SET `view_month` = 0');
            $actions[] = 'view_month reset';
        }

        return $this->response
            ->setJSON(['ok' => true, 'actions' => $actions]);
    }
}
