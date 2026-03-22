<?php

namespace App\Models;

use CodeIgniter\Model;

class RatingModel extends Model
{
    protected $table         = 'item_ratings';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['item_id', 'score', 'ip_address'];

    /**
     * Get average score and vote count for a manga.
     */
    public function getStats(int $itemId): array
    {
        $row = $this->selectAvg('score', 'avg_score')
                    ->selectCount('id', 'votes')
                    ->where('item_id', $itemId)
                    ->get()
                    ->getRowArray();

        return [
            'avg'   => round((float) ($row['avg_score'] ?? 0), 1),
            'votes' => (int) ($row['votes'] ?? 0),
        ];
    }

    /**
     * Get existing rating by IP for a manga.
     */
    public function findByIp(int $itemId, string $ip): ?array
    {
        return $this->where('item_id', $itemId)
                    ->where('ip_address', $ip)
                    ->first();
    }

    /**
     * Rate or update rating.
     */
    public function rate(int $itemId, int $score, string $ip): array
    {
        $existing = $this->findByIp($itemId, $ip);

        if ($existing) {
            $this->update($existing['id'], ['score' => $score]);
        } else {
            $this->insert([
                'item_id'    => $itemId,
                'score'      => $score,
                'ip_address' => $ip,
            ]);
        }

        $stats = $this->getStats($itemId);
        $stats['your_score'] = $score;

        // Update cached rating in manga table
        $this->db->table('manga')
            ->where('id', $itemId)
            ->update(['rating' => $stats['avg']]);

        return $stats;
    }
}
