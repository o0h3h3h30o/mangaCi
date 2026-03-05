<?php
$reasonLabels = [
    'wrong_images'  => ['Wrong images',        'bg-red-900/40 text-red-400'],
    'missing_pages' => ['Missing pages',        'bg-orange-900/40 text-orange-400'],
    'low_quality'   => ['Low quality',          'bg-yellow-900/40 text-yellow-400'],
    'cant_load'     => ['Images not loading',   'bg-purple-900/40 text-purple-400'],
    'wrong_order'   => ['Wrong page order',     'bg-blue-900/40 text-blue-400'],
    'other'         => ['Other',                'bg-gray-800 text-gray-400'],
];
?>

<!-- Tabs -->
<div class="flex items-center gap-3 mb-5">
  <a href="/admin/reports?status=pending"
     class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $status==='pending' ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' ?>">
    Pending
    <?php if ($pendingCount > 0): ?>
    <span class="ml-1.5 bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5"><?= $pendingCount ?></span>
    <?php endif; ?>
  </a>
  <a href="/admin/reports?status=all"
     class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $status==='all' ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' ?>">
    All Reports
  </a>
  <span class="ml-auto text-xs text-gray-600"><?= number_format($total) ?> report<?= $total !== 1 ? 's' : '' ?></span>
</div>

<!-- Table -->
<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-4">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-gray-800/50">
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chapter</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reporter</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-800">
        <?php foreach ($rows as $r): ?>
        <?php
          [$reasonLabel, $reasonClass] = $reasonLabels[$r['reason']] ?? ['Unknown', 'bg-gray-800 text-gray-400'];
          $date = !empty($r['created_at']) ? date('M d, Y H:i', strtotime($r['created_at'])) : '—';
          $isPending = $r['status'] === 'pending';
        ?>
        <tr class="hover:bg-gray-800/30 transition-colors <?= $isPending ? '' : 'opacity-50' ?>">
          <td class="px-4 py-3">
            <div class="font-medium text-gray-200 text-xs"><?= esc($r['manga_name']) ?></div>
            <?php if (!empty($r['chapter_slug'])): ?>
            <a href="/manga/<?= esc($r['manga_slug']) ?>/<?= esc($r['chapter_slug']) ?>" target="_blank"
               class="text-indigo-400 hover:text-indigo-300 text-xs transition-colors">
              <?= esc($r['chapter_name']) ?> ↗
            </a>
            <?php else: ?>
            <span class="text-gray-600 text-xs"><?= esc($r['chapter_name'] ?? '—') ?></span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3">
            <span class="<?= $reasonClass ?> text-xs px-2 py-0.5 rounded-full font-medium"><?= $reasonLabel ?></span>
          </td>
          <td class="px-4 py-3 text-gray-400 text-xs max-w-xs">
            <?php if (!empty($r['note'])): ?>
            <span title="<?= esc($r['note']) ?>"><?= esc(mb_substr($r['note'], 0, 80)) ?><?= mb_strlen($r['note']) > 80 ? '…' : '' ?></span>
            <?php else: ?>
            <span class="text-gray-600">—</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-xs">
            <?php if (!empty($r['reporter_username'])): ?>
            <span class="text-gray-300">@<?= esc($r['reporter_username']) ?></span>
            <?php else: ?>
            <span class="text-gray-600">Guest</span>
            <?php endif; ?>
            <div class="text-gray-600 mt-0.5"><?= esc($r['ip_address']) ?></div>
          </td>
          <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap"><?= $date ?></td>
          <td class="px-4 py-3">
            <?php if ($isPending): ?>
            <span class="bg-yellow-900/40 text-yellow-400 text-xs px-2 py-0.5 rounded-full">Pending</span>
            <?php else: ?>
            <span class="bg-green-900/40 text-green-400 text-xs px-2 py-0.5 rounded-full">Resolved</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-right">
            <?php if ($isPending): ?>
            <form method="post" action="/admin/reports/<?= (int)$r['id'] ?>/resolve" onsubmit="return confirm('Mark as resolved?')">
              <?= csrf_field() ?>
              <input type="hidden" name="status" value="<?= esc($status) ?>">
              <input type="hidden" name="page" value="<?= (int)$page ?>">
              <button type="submit" class="bg-green-700 hover:bg-green-600 text-white text-xs px-3 py-1.5 rounded-md transition-colors">
                Resolve
              </button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="7" class="px-5 py-12 text-center text-gray-600">No reports found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="flex items-center justify-center gap-1">
  <?php
    $pts  = array_unique(array_filter([1, $page-1, $page, $page+1, $totalPages], fn($p)=>$p>=1&&$p<=$totalPages));
    sort($pts); $prev = 0;
    foreach ($pts as $pt) {
        if ($prev && $pt - $prev > 1) echo '<span class="px-2 text-gray-600">…</span>';
        $url = '/admin/reports?status='.urlencode($status).'&page='.$pt;
        echo '<a href="'.esc($url).'" class="'.($pt===$page ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700').' w-9 h-9 flex items-center justify-center rounded-lg text-sm transition-colors">'.$pt.'</a>';
        $prev = $pt;
    }
  ?>
</div>
<?php endif; ?>
