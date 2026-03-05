<?php if ($flash = ($flash ?? null)): ?>
<div class="mb-5 px-4 py-3 rounded-lg text-sm <?= $flash['type']==='success' ? 'bg-green-900/40 border border-green-700 text-green-300' : 'bg-red-900/40 border border-red-700 text-red-300' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- Back + heading -->
<div class="flex items-center gap-3 mb-5">
  <a href="/admin/manga" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">← Manga List</a>
  <span class="text-gray-700">/</span>
  <a href="/admin/manga/<?= $manga['id'] ?>/edit" class="text-sm text-indigo-400 hover:underline truncate max-w-xs"><?= esc($manga['name']) ?></a>
  <span class="text-gray-700">/</span>
  <span class="text-sm text-gray-400">Chapters</span>
</div>

<!-- Toolbar -->
<div class="flex flex-wrap gap-2 mb-5">
  <form method="get" action="/admin/manga/<?= $manga['id'] ?>/chapters" class="flex gap-2 flex-1 min-w-0">
    <input type="text" name="q" value="<?= esc($q ?? '') ?>" placeholder="Search by number or name…"
           class="flex-1 min-w-0 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500">
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg transition-colors shrink-0">Search</button>
    <?php if (!empty($q)): ?>
    <a href="/admin/manga/<?= $manga['id'] ?>/chapters" class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm px-4 py-2 rounded-lg transition-colors shrink-0">Clear</a>
    <?php endif; ?>
  </form>
  <a href="/admin/manga/<?= $manga['id'] ?>/chapters/new"
     class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg transition-colors flex items-center gap-1.5 shrink-0">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    New Chapter
  </a>
</div>

<p class="text-xs text-gray-500 mb-3"><?= number_format($total ?? count($items)) ?> chapter<?= ($total ?? count($items)) !== 1 ? 's' : '' ?></p>

<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-4">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-gray-800/50">
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">#</th>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Slug</th>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Pages</th>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Show</th>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Views</th>
        <th class="px-4 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-800">
      <?php foreach ($items as $c): ?>
      <tr class="hover:bg-gray-800/30 transition-colors">
        <td class="px-4 py-3 text-gray-600 text-xs"><?= $c['id'] ?></td>
        <td class="px-4 py-3 font-mono text-gray-200 font-medium">
          <?= rtrim(rtrim(number_format((float)$c['number'], 1), '0'), '.') ?>
        </td>
        <td class="px-4 py-3 text-gray-300">
          <a href="/admin/chapters/<?= $c['id'] ?>/edit" class="hover:text-indigo-400 transition-colors">
            <?= esc($c['name'] ?: '—') ?>
          </a>
        </td>
        <td class="px-4 py-3 text-gray-600 text-xs font-mono hidden md:table-cell"><?= esc($c['slug'] ?? '') ?></td>
        <td class="px-4 py-3 text-gray-400 text-xs hidden md:table-cell"><?= number_format((int)($c['page_count'] ?? 0)) ?></td>
        <td class="px-4 py-3">
          <button type="button"
                  data-id="<?= $c['id'] ?>"
                  data-show="<?= $c['is_show'] ? '1' : '0' ?>"
                  onclick="toggleShow(this)"
                  class="show-toggle relative inline-flex w-10 h-6 rounded-full transition-colors duration-200 focus:outline-none <?= $c['is_show'] ? 'bg-indigo-600' : 'bg-gray-700' ?>">
            <span class="inline-block w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-200 mt-0.5 <?= $c['is_show'] ? 'translate-x-4 ml-0.5' : 'translate-x-0.5' ?>"></span>
          </button>
        </td>
        <td class="px-4 py-3 text-gray-500 text-xs hidden md:table-cell"><?= number_format((int)($c['view'] ?? 0)) ?></td>
        <td class="px-4 py-3 text-right">
          <div class="flex items-center justify-end gap-2">
            <a href="/admin/chapters/<?= $c['id'] ?>/edit"
               class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs px-3 py-1.5 rounded-md transition-colors">
              Edit
            </a>
            <form method="post" action="/admin/chapters/<?= $c['id'] ?>/delete"
                  onsubmit="return confirm('Delete Chapter <?= rtrim(rtrim(number_format((float)$c['number'],1),'0'),'.') ?>?')">
              <?= csrf_field() ?>
              <button type="submit" class="bg-red-900/40 hover:bg-red-900/70 text-red-400 text-xs px-3 py-1.5 rounded-md transition-colors">
                Delete
              </button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
      <tr>
        <td colspan="8" class="px-5 py-10 text-center text-gray-600">
          No chapters yet. <a href="/admin/manga/<?= $manga['id'] ?>/chapters/new" class="text-indigo-400 hover:underline">Add first chapter</a>
        </td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Pagination -->
<?php if (($totalPages ?? 1) > 1): ?>
<div class="flex items-center justify-center gap-1">
  <?php
    $prev = 0;
    $base = '/admin/manga/' . $manga['id'] . '/chapters';
    $pts  = array_unique(array_filter([1, ($page??1)-1, ($page??1), ($page??1)+1, $totalPages], fn($p)=>$p>=1&&$p<=$totalPages));
    sort($pts);
    foreach ($pts as $pt) {
        if ($prev && $pt - $prev > 1) echo '<span class="px-2 text-gray-600">…</span>';
        $active = $pt === ($page ?? 1);
        $url    = $base . '?' . http_build_query(array_filter(['q' => $q ?? '', 'page' => $pt > 1 ? $pt : null]));
        echo '<a href="'.esc($url).'" class="'.($active ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700').
            ' w-9 h-9 flex items-center justify-center rounded-lg text-sm transition-colors">'.$pt.'</a>';
        $prev = $pt;
    }
  ?>
</div>
<?php endif; ?>

<script>
function toggleShow(btn) {
  var id      = btn.dataset.id;
  var current = btn.dataset.show === '1';
  var next    = current ? 0 : 1;

  // Optimistic UI update
  btn.dataset.show = next ? '1' : '0';
  btn.classList.toggle('bg-indigo-600', !!next);
  btn.classList.toggle('bg-gray-700',   !next);
  var dot = btn.querySelector('span');
  dot.classList.toggle('translate-x-4',   !!next);
  dot.classList.toggle('ml-0.5',          !!next);
  dot.classList.toggle('translate-x-0.5', !next);

  fetch('/admin/chapters/' + id + '/toggle-show', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
    body: '<?= csrf_token() ?>=<?= csrf_hash() ?>&is_show=' + next
  })
  .then(function(r){ return r.json(); })
  .then(function(data){
    if (!data.ok) {
      // Revert on failure
      btn.dataset.show = current ? '1' : '0';
      btn.classList.toggle('bg-indigo-600', current);
      btn.classList.toggle('bg-gray-700',   !current);
      dot.classList.toggle('translate-x-4',   current);
      dot.classList.toggle('ml-0.5',          current);
      dot.classList.toggle('translate-x-0.5', !current);
    }
  })
  .catch(function(){});
}
</script>
