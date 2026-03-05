<?php if ($flash = ($flash ?? null)): ?>
<div class="mb-5 px-4 py-3 rounded-lg text-sm <?= $flash['type']==='success' ? 'bg-green-900/40 border border-green-700 text-green-300' : 'bg-red-900/40 border border-red-700 text-red-300' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- Toolbar -->
<div class="flex flex-col sm:flex-row gap-3 mb-5">
  <form method="get" action="<?= $baseUrl ?>" class="flex gap-2 flex-1">
    <input type="text" name="q" value="<?= esc($q ?? '') ?>" placeholder="Search by name…"
           class="flex-1 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500">
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg transition-colors">Search</button>
    <?php if (!empty($q)): ?>
    <a href="<?= $baseUrl ?>" class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm px-4 py-2 rounded-lg transition-colors">Clear</a>
    <?php endif; ?>
  </form>
  <a href="<?= $baseUrl ?>/new" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg transition-colors flex items-center gap-1.5 shrink-0">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    New
  </a>
</div>

<p class="text-xs text-gray-500 mb-3"><?= number_format($total ?? count($items)) ?> item<?= ($total ?? count($items)) !== 1 ? 's' : '' ?></p>

<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-4">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-gray-800/50">
        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">#</th>
        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
        <?php if ($hasSlug): ?>
        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
        <?php endif; ?>
        <?php if ($hasMangaCount): ?>
        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manga</th>
        <?php endif; ?>
        <th class="px-5 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-800">
      <?php foreach ($items as $item): ?>
      <tr class="hover:bg-gray-800/30 transition-colors">
        <td class="px-5 py-3.5 text-gray-600 text-xs"><?= $item['id'] ?></td>
        <td class="px-5 py-3.5 font-medium text-gray-200"><?= esc($item['name']) ?></td>
        <?php if ($hasSlug): ?>
        <td class="px-5 py-3.5 text-gray-500 text-xs font-mono"><?= esc($item['slug'] ?? '') ?></td>
        <?php endif; ?>
        <?php if ($hasMangaCount): ?>
        <td class="px-5 py-3.5 text-gray-400 text-sm"><?= number_format((int)($item['manga_count'] ?? 0)) ?></td>
        <?php endif; ?>
        <td class="px-5 py-3.5">
          <div class="flex items-center justify-end gap-2">
            <a href="<?= $baseUrl ?>/<?= $item['id'] ?>/edit"
               class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs px-3 py-1.5 rounded-md transition-colors">
              Edit
            </a>
            <form method="post" action="<?= $baseUrl ?>/<?= $item['id'] ?>/delete"
                  onsubmit="return confirm('Delete &quot;<?= esc($item['name']) ?>&quot;?')">
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
        <td colspan="<?= 2 + ($hasSlug?1:0) + ($hasMangaCount?1:0) + 1 ?>"
            class="px-5 py-10 text-center text-gray-600">
          No items found. <a href="<?= $baseUrl ?>/new" class="text-indigo-400 hover:underline">Create one</a>
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
    $pts  = array_unique(array_filter([1, ($page??1)-1, ($page??1), ($page??1)+1, $totalPages], fn($p)=>$p>=1&&$p<=$totalPages));
    sort($pts);
    foreach ($pts as $pt) {
        if ($prev && $pt - $prev > 1) echo '<span class="px-2 text-gray-600">…</span>';
        $active = $pt === ($page ?? 1);
        $url    = $baseUrl . '?' . http_build_query(array_filter(['q' => $q ?? '', 'page' => $pt > 1 ? $pt : null]));
        echo '<a href="'.esc($url).'" class="'.($active
            ? 'bg-indigo-600 text-white'
            : 'bg-gray-800 text-gray-400 hover:bg-gray-700').
            ' w-9 h-9 flex items-center justify-center rounded-lg text-sm transition-colors">'.$pt.'</a>';
        $prev = $pt;
    }
  ?>
</div>
<?php endif; ?>
