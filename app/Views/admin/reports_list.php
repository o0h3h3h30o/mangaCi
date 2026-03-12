<?php
$reasonLabels = [
    'wrong_images'  => ['Wrong images',        'a-badge-red'],
    'missing_pages' => ['Missing pages',        'a-badge-orange'],
    'low_quality'   => ['Low quality',          'a-badge-yellow'],
    'cant_load'     => ['Images not loading',   'a-badge-purple'],
    'wrong_order'   => ['Wrong page order',     'a-badge-blue'],
    'other'         => ['Other',                'a-badge-gray'],
];
?>

<!-- Tabs -->
<div class="a-tabs">
  <a href="/admin/reports?status=pending"
     class="a-tab <?= $status==='pending' ? 'active' : '' ?>">
    Pending
    <?php if ($pendingCount > 0): ?>
    <span class="a-badge a-badge-red" style="font-size:10px"><?= $pendingCount ?></span>
    <?php endif; ?>
  </a>
  <a href="/admin/reports?status=all"
     class="a-tab <?= $status==='all' ? 'active' : '' ?>">
    All Reports
  </a>
  <span class="a-text-xs a-txt6" style="margin-left:auto"><?= number_format($total) ?> report<?= $total !== 1 ? 's' : '' ?></span>
</div>

<!-- Table -->
<div class="a-panel" style="margin-bottom:1rem">
  <div style="overflow-x:auto">
    <table class="a-table">
      <thead>
        <tr>
          <th>Chapter</th>
          <th>Reason</th>
          <th>Note</th>
          <th>Reporter</th>
          <th>Date</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <?php
          [$reasonLabel, $reasonClass] = $reasonLabels[$r['reason']] ?? ['Unknown', 'a-badge-gray'];
          $date = !empty($r['created_at']) ? date('M d, Y H:i', strtotime($r['created_at'])) : '—';
          $isPending = $r['status'] === 'pending';
        ?>
        <tr <?= $isPending ? '' : 'style="opacity:0.5"' ?>>
          <td>
            <div style="font-weight:500"><?= esc($r['manga_name']) ?></div>
            <?php if (!empty($r['chapter_slug'])): ?>
            <a href="/manga/<?= esc($r['manga_slug']) ?>/<?= esc($r['chapter_slug']) ?>" target="_blank"
               class="a-link" style="font-size:12px">
              <?= esc($r['chapter_name']) ?> ↗
            </a>
            <?php else: ?>
            <span class="a-txt6" style="font-size:12px"><?= esc($r['chapter_name'] ?? '—') ?></span>
            <?php endif; ?>
          </td>
          <td>
            <span class="a-badge <?= $reasonClass ?>"><?= $reasonLabel ?></span>
          </td>
          <td style="max-width:20rem">
            <?php if (!empty($r['note'])): ?>
            <span title="<?= esc($r['note']) ?>"><?= esc(mb_substr($r['note'], 0, 80)) ?><?= mb_strlen($r['note']) > 80 ? '…' : '' ?></span>
            <?php else: ?>
            <span class="a-txt6">—</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($r['reporter_username'])): ?>
            <span>@<?= esc($r['reporter_username']) ?></span>
            <?php else: ?>
            <span class="a-txt6">Guest</span>
            <?php endif; ?>
            <div class="a-txt6" style="margin-top:2px"><?= esc($r['ip_address']) ?></div>
          </td>
          <td style="white-space:nowrap"><?= $date ?></td>
          <td>
            <?php if ($isPending): ?>
            <span class="a-badge a-badge-yellow">Pending</span>
            <?php else: ?>
            <span class="a-badge a-badge-green">Resolved</span>
            <?php endif; ?>
          </td>
          <td style="text-align:right">
            <?php if ($isPending): ?>
            <form method="post" action="/admin/reports/<?= (int)$r['id'] ?>/resolve" onsubmit="return confirm('Mark as resolved?')">
              <?= csrf_field() ?>
              <input type="hidden" name="status" value="<?= esc($status) ?>">
              <input type="hidden" name="page" value="<?= (int)$page ?>">
              <button type="submit" class="a-btn-success">
                Resolve
              </button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="7" class="a-empty">No reports found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="a-pager">
  <?php
    $pts  = array_unique(array_filter([1, $page-1, $page, $page+1, $totalPages], fn($p)=>$p>=1&&$p<=$totalPages));
    sort($pts); $prev = 0;
    foreach ($pts as $pt) {
        if ($prev && $pt - $prev > 1) echo '<span class="a-txt6" style="padding:0 0.5rem">…</span>';
        $url = '/admin/reports?status='.urlencode($status).'&page='.$pt;
        echo '<a href="'.esc($url).'" class="a-pg'.($pt===$page ? ' active' : '').'">'.$pt.'</a>';
        $prev = $pt;
    }
  ?>
</div>
<?php endif; ?>
