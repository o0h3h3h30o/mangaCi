<?php
$statusMap = [];
foreach ($statuses as $s) $statusMap[(int)$s['id']] = $s['name'] ?? $s['label'] ?? $s['title'] ?? 'Status '.$s['id'];
$statusColors = [1=>'yellow', 2=>'green', 3=>'orange', 4=>'red'];
?>

<?php if ($flash = ($flash ?? null)): ?>
<div class="a-flash <?= $flash['type']==='success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- Toolbar -->
<div class="a-toolbar">
  <form method="get" action="/admin/manga" class="a-search-wrap">
    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search title, slug&hellip;" class="a-search-input">
    <?php if ($sf): ?><input type="hidden" name="status" value="<?= $sf ?>"><?php endif; ?>
    <?php if ($pf !== ''): ?><input type="hidden" name="pub" value="<?= esc($pf) ?>"><?php endif; ?>
    <button type="submit" class="a-btn a-btn-sm">Search</button>
    <?php if ($q || $sf || $pf !== ''): ?>
    <a href="/admin/manga" class="a-btn-sec a-btn-sm">Clear</a>
    <?php endif; ?>
  </form>

  <!-- Status filter -->
  <form method="get" action="/admin/manga">
    <?php if ($q): ?><input type="hidden" name="q" value="<?= esc($q) ?>"><?php endif; ?>
    <?php if ($pf !== ''): ?><input type="hidden" name="pub" value="<?= esc($pf) ?>"><?php endif; ?>
    <select name="status" onchange="this.form.submit()" class="a-select" style="width:auto">
      <option value="0">All statuses</option>
      <?php foreach ($statuses as $s): ?>
      <option value="<?= $s['id'] ?>" <?= $sf === (int)$s['id'] ? 'selected' : '' ?>><?= esc($s['name'] ?? $s['label'] ?? $s['title'] ?? 'Status '.$s['id']) ?></option>
      <?php endforeach; ?>
    </select>
    <!-- Public filter -->
    <select name="pub" onchange="this.form.submit()" class="a-select" style="width:auto">
      <option value="">All visibility</option>
      <option value="1" <?= $pf==='1'?'selected':'' ?>>Public</option>
      <option value="0" <?= $pf==='0'?'selected':'' ?>>Hidden</option>
    </select>
  </form>

  <a href="/admin/manga/new" class="a-btn a-btn-sm">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    New Manga
  </a>
</div>

<p class="a-count"><?= number_format($total) ?> manga found</p>

<div class="a-panel" style="margin-bottom:16px">
  <div class="a-overflow-x">
    <table class="a-table">
      <thead>
        <tr>
          <th style="width:40px">#</th>
          <th>Manga</th>
          <th>Status</th>
          <th class="col-hidden col-lg">Categories</th>
          <th class="col-hidden col-lg">Authors</th>
          <th class="col-hidden col-lg">Artists</th>
          <th>Vis.</th>
          <th class="col-hidden col-md">Views</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $m): ?>
        <?php
          $sName  = $statusMap[$m['status_id']] ?? 'Unknown';
          $sColor = $statusColors[$m['status_id']] ?? 'gray';
          $cover  = esc(manga_cover_url($m, $cdnBase));
        ?>
        <tr>
          <td class="a-txt6 a-text-xs"><?= $m['id'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:12px">
              <img src="<?= $cover ?>" alt="" loading="lazy" class="a-cover-thumb"
                   onerror="this.style.display='none'">
              <div style="min-width:0">
                <a href="/admin/manga/<?= $m['id'] ?>/edit"
                   class="a-font-medium a-txt2 a-truncate a-block" style="max-width:240px"><?= esc($m['name']) ?></a>
              </div>
            </div>
          </td>
          <td>
            <span class="a-badge a-badge-<?= $sColor ?>"><?= esc($sName) ?></span>
          </td>
          <td class="a-txt5 a-text-xs col-hidden col-lg" style="max-width:180px">
            <span class="a-clamp-1"><?= esc($m['categories'] ?: '&mdash;') ?></span>
          </td>
          <td class="a-txt4 a-text-xs col-hidden col-lg" style="max-width:140px">
            <span class="a-clamp-1"><?= esc($m['authors'] ?: '&mdash;') ?></span>
          </td>
          <td class="a-txt4 a-text-xs col-hidden col-lg" style="max-width:140px">
            <span class="a-clamp-1"><?= esc($m['artists'] ?: '&mdash;') ?></span>
          </td>
          <td>
            <?php if ($m['is_public']): ?>
              <span class="a-txt-green-dot">&bull;</span>
            <?php else: ?>
              <span class="a-txt-muted-dot">&bull;</span>
            <?php endif; ?>
          </td>
          <td class="a-txt5 a-text-xs col-hidden col-md"><?= number_format((int)$m['views']) ?></td>
          <td class="a-text-right">
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
              <a href="/admin/manga/<?= $m['id'] ?>/chapters" class="a-btn-sec a-btn-sm">Chapters</a>
              <a href="/admin/manga/<?= $m['id'] ?>/edit" class="a-btn a-btn-sm">Edit</a>
              <form method="post" action="/admin/manga/<?= $m['id'] ?>/delete" style="margin:0" onsubmit="return confirm('Delete &quot;<?= esc($m['name']) ?>&quot; and all its chapters? This cannot be undone.')">
                <?= csrf_field() ?>
                <button type="submit" class="a-btn-danger a-btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?>
        <tr><td colspan="9" class="a-empty">No manga found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="a-pager">
  <?php
    $pBase = array_filter(['q'=>$q,'status'=>$sf,'pub'=>$pf], fn($v)=>$v!==''&&$v!==0&&$v!==null);
    $prev  = 0;
    $pts   = array_unique(array_filter([1,$page-1,$page,$page+1,$totalPages],fn($p)=>$p>=1&&$p<=$totalPages));
    sort($pts);
    foreach ($pts as $pt) {
      if ($prev && $pt-$prev>1) echo '<span class="a-pg-dots">&hellip;</span>';
      $url = '/admin/manga?' . http_build_query(array_filter(array_merge($pBase,['page'=>$pt>1?$pt:null])));
      echo '<a href="'.esc($url).'" class="a-pg'.($pt===$page ? ' active' : '').'">'.$pt.'</a>';
      $prev = $pt;
    }
  ?>
</div>
<?php endif; ?>
