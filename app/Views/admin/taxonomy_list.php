<?php if ($flash = ($flash ?? null)): ?>
<div class="a-flash <?= $flash['type']==='success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- Toolbar -->
<div class="a-toolbar">
  <form method="get" action="<?= $baseUrl ?>" class="a-search-wrap">
    <input type="text" name="q" value="<?= esc($q ?? '') ?>" placeholder="Search by name…" class="a-search-input">
    <button type="submit" class="a-btn a-btn-sm">Search</button>
    <?php if (!empty($q)): ?>
    <a href="<?= $baseUrl ?>" class="a-btn-sec a-btn-sm">Clear</a>
    <?php endif; ?>
  </form>
  <a href="<?= $baseUrl ?>/new" class="a-btn a-btn-sm">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    New
  </a>
</div>

<p class="a-count"><?= number_format($total ?? count($items)) ?> item<?= ($total ?? count($items)) !== 1 ? 's' : '' ?></p>

<div class="a-panel" style="margin-bottom:16px">
  <table class="a-table">
    <thead>
      <tr>
        <th style="width:48px">#</th>
        <th>Name</th>
        <?php if ($hasSlug): ?><th>Slug</th><?php endif; ?>
        <?php if ($hasMangaCount): ?><th>Manga</th><?php endif; ?>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td class="a-txt6 a-text-xs"><?= $item['id'] ?></td>
        <td class="a-font-medium a-txt2"><?= esc($item['name']) ?></td>
        <?php if ($hasSlug): ?>
        <td class="a-txt5 a-text-xs a-font-mono"><?= esc($item['slug'] ?? '') ?></td>
        <?php endif; ?>
        <?php if ($hasMangaCount): ?>
        <td class="a-txt4"><?= number_format((int)($item['manga_count'] ?? 0)) ?></td>
        <?php endif; ?>
        <td>
          <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
            <a href="<?= $baseUrl ?>/<?= $item['id'] ?>/edit" class="a-btn-sec a-btn-sm">Edit</a>
            <form method="post" action="<?= $baseUrl ?>/<?= $item['id'] ?>/delete"
                  onsubmit="return confirm('Delete &quot;<?= esc($item['name']) ?>&quot;?')">
              <?= csrf_field() ?>
              <button type="submit" class="a-btn-danger">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
      <tr>
        <td colspan="<?= 2 + ($hasSlug?1:0) + ($hasMangaCount?1:0) + 1 ?>" class="a-empty">
          No items found. <a href="<?= $baseUrl ?>/new" class="a-link">Create one</a>
        </td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Pagination -->
<?php if (($totalPages ?? 1) > 1): ?>
<div class="a-pager">
  <?php
    $prev = 0;
    $pts  = array_unique(array_filter([1, ($page??1)-1, ($page??1), ($page??1)+1, $totalPages], fn($p)=>$p>=1&&$p<=$totalPages));
    sort($pts);
    foreach ($pts as $pt) {
        if ($prev && $pt - $prev > 1) echo '<span class="a-pg-dots">…</span>';
        $active = $pt === ($page ?? 1);
        $url    = $baseUrl . '?' . http_build_query(array_filter(['q' => $q ?? '', 'page' => $pt > 1 ? $pt : null]));
        echo '<a href="'.esc($url).'" class="a-pg'.($active ? ' active' : '').'">'.$pt.'</a>';
        $prev = $pt;
    }
  ?>
</div>
<?php endif; ?>
