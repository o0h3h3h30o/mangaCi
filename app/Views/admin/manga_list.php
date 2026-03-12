<?php
$statusMap = [];
foreach ($statuses as $s) $statusMap[(int)$s['id']] = $s['name'] ?? $s['label'] ?? $s['title'] ?? 'Status '.$s['id'];
$statusColors = [1=>'yellow', 2=>'green', 3=>'orange', 4=>'red'];
?>

<?php if ($flash = ($flash ?? null)): ?>
<div class="mb-5 px-4 py-3 rounded-lg text-sm <?= $flash['type']==='success' ? 'bg-green-900/40 border border-green-700 text-green-300' : 'bg-red-900/40 border border-red-700 text-red-300' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- Toolbar -->
<div class="flex flex-wrap gap-2 mb-5">
  <form method="get" action="/admin/manga" class="flex gap-2 flex-1 min-w-0">
    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search title, slug…"
           class="flex-1 min-w-0 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500">
    <?php if ($sf): ?><input type="hidden" name="status" value="<?= $sf ?>"><?php endif; ?>
    <?php if ($pf !== ''): ?><input type="hidden" name="pub" value="<?= esc($pf) ?>"><?php endif; ?>
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg transition-colors shrink-0">Search</button>
    <?php if ($q || $sf || $pf !== ''): ?>
    <a href="/admin/manga" class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm px-4 py-2 rounded-lg transition-colors shrink-0">Clear</a>
    <?php endif; ?>
  </form>

  <!-- Status filter -->
  <form method="get" action="/admin/manga" class="flex gap-2">
    <?php if ($q): ?><input type="hidden" name="q" value="<?= esc($q) ?>"><?php endif; ?>
    <?php if ($pf !== ''): ?><input type="hidden" name="pub" value="<?= esc($pf) ?>"><?php endif; ?>
    <select name="status" onchange="this.form.submit()"
            class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none">
      <option value="0">All statuses</option>
      <?php foreach ($statuses as $s): ?>
      <option value="<?= $s['id'] ?>" <?= $sf === (int)$s['id'] ? 'selected' : '' ?>><?= esc($s['name'] ?? $s['label'] ?? $s['title'] ?? 'Status '.$s['id']) ?></option>
      <?php endforeach; ?>
    </select>
    <!-- Public filter -->
    <select name="pub" onchange="this.form.submit()"
            class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none">
      <option value="">All visibility</option>
      <option value="1" <?= $pf==='1'?'selected':'' ?>>Public</option>
      <option value="0" <?= $pf==='0'?'selected':'' ?>>Hidden</option>
    </select>
  </form>

  <a href="/admin/manga/new" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg transition-colors flex items-center gap-1.5 shrink-0">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    New Manga
  </a>
</div>

<p class="text-xs text-gray-500 mb-3"><?= number_format($total) ?> manga found</p>

<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-4">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-gray-800/50">
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">#</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manga</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Categories</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Authors</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Artists</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vis.</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Views</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-800">
        <?php foreach ($items as $m): ?>
        <?php
          $sName  = $statusMap[$m['status_id']] ?? 'Unknown';
          $sColor = $statusColors[$m['status_id']] ?? 'gray';
          $cover  = esc(manga_cover_url($m, $cdnBase));
        ?>
        <tr class="hover:bg-gray-800/30 transition-colors">
          <td class="px-4 py-3 text-gray-600 text-xs"><?= $m['id'] ?></td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <img src="<?= $cover ?>" alt="" loading="lazy"
                   class="w-8 h-11 object-cover rounded shrink-0 bg-gray-800"
                   onerror="this.style.display='none'">
              <div class="min-w-0">
                <a href="/admin/manga/<?= $m['id'] ?>/edit"
                   class="font-medium text-gray-200 hover:text-indigo-400 truncate max-w-xs block transition-colors"><?= esc($m['name']) ?></a>
              </div>
            </div>
          </td>
          <td class="px-4 py-3">
            <span class="bg-<?= $sColor ?>-900/40 text-<?= $sColor ?>-400 text-xs px-2 py-0.5 rounded-full whitespace-nowrap"><?= esc($sName) ?></span>
          </td>
          <td class="px-4 py-3 text-gray-500 text-xs hidden lg:table-cell max-w-[180px]">
            <span class="line-clamp-1"><?= esc($m['categories'] ?: '—') ?></span>
          </td>
          <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell max-w-[140px]">
            <span class="line-clamp-1"><?= esc($m['authors'] ?: '—') ?></span>
          </td>
          <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell max-w-[140px]">
            <span class="line-clamp-1"><?= esc($m['artists'] ?: '—') ?></span>
          </td>
          <td class="px-4 py-3">
            <?php if ($m['is_public']): ?>
              <span class="text-green-500 text-xs">●</span>
            <?php else: ?>
              <span class="text-gray-600 text-xs">●</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-gray-500 text-xs hidden md:table-cell"><?= number_format((int)$m['views']) ?></td>
          <td class="px-4 py-3 text-right">
            <div class="flex items-center justify-end gap-2">
              <a href="/admin/manga/<?= $m['id'] ?>/chapters"
                 class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs px-3 py-1.5 rounded-md transition-colors">
                Chapters
              </a>
              <a href="/admin/manga/<?= $m['id'] ?>/edit"
                 class="bg-indigo-700 hover:bg-indigo-600 text-gray-200 text-xs px-3 py-1.5 rounded-md transition-colors">
                Edit
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?>
        <tr><td colspan="9" class="px-5 py-10 text-center text-gray-600">No manga found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="flex items-center justify-center gap-1">
  <?php
    $pBase = array_filter(['q'=>$q,'status'=>$sf,'pub'=>$pf], fn($v)=>$v!==''&&$v!==0&&$v!==null);
    $prev  = 0;
    $pts   = array_unique(array_filter([1,$page-1,$page,$page+1,$totalPages],fn($p)=>$p>=1&&$p<=$totalPages));
    sort($pts);
    foreach ($pts as $pt) {
      if ($prev && $pt-$prev>1) echo '<span class="px-2 text-gray-600">…</span>';
      $url = '/admin/manga?' . http_build_query(array_filter(array_merge($pBase,['page'=>$pt>1?$pt:null])));
      $cls = $pt===$page ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700';
      echo "<a href=\"".esc($url)."\" class=\"{$cls} w-9 h-9 flex items-center justify-center rounded-lg text-sm transition-colors\">{$pt}</a>";
      $prev = $pt;
    }
  ?>
</div>
<?php endif; ?>
