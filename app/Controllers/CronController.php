<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class CronController extends Controller
{
    /**
     * GET /cron/reset-views
     * Reset view_day daily, view_week weekly (Monday), view_month on 1st.
     * Call daily via cron: 0 0 * * * curl -s https://domain/cron/reset-views
     */
    public function resetViews()
    {

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
