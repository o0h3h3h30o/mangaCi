<?php if ($flash = ($flash ?? null)): ?>
<div class="a-flash <?= $flash['type']==='success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- Breadcrumb -->
<div class="a-crumbs">
  <a href="/admin/manga">&larr; Manga List</a>
  <span class="sep">/</span>
  <a href="/admin/manga/<?= $manga['id'] ?>/edit" class="accent a-truncate" style="max-width:200px"><?= esc($manga['name']) ?></a>
  <span class="sep">/</span>
  <span>Chapters</span>
</div>

<!-- Toolbar -->
<div class="a-toolbar">
  <form method="get" action="/admin/manga/<?= $manga['id'] ?>/chapters" class="a-search-wrap">
    <input type="text" name="q" value="<?= esc($q ?? '') ?>" placeholder="Search by number or name&hellip;" class="a-search-input">
    <button type="submit" class="a-btn a-btn-sm">Search</button>
    <?php if (!empty($q)): ?>
    <a href="/admin/manga/<?= $manga['id'] ?>/chapters" class="a-btn-sec a-btn-sm">Clear</a>
    <?php endif; ?>
  </form>
  <a href="/admin/manga/<?= $manga['id'] ?>/chapters/new" class="a-btn a-btn-sm">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    New Chapter
  </a>
</div>

<p class="a-count"><?= number_format($total ?? count($items)) ?> chapter<?= ($total ?? count($items)) !== 1 ? 's' : '' ?></p>

<div class="a-panel" style="margin-bottom:16px">
  <table class="a-table">
    <thead>
      <tr>
        <th style="width:48px">#</th>
        <th>Number</th>
        <th>Name</th>
        <th class="col-hidden col-md">Slug</th>
        <th class="col-hidden col-md">Pages</th>
        <th>Show</th>
        <th class="col-hidden col-md">Views</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $c): ?>
      <tr>
        <td class="a-txt6 a-text-xs"><?= $c['id'] ?></td>
        <td class="a-font-mono a-txt2 a-font-medium">
          <?= rtrim(rtrim(number_format((float)$c['number'], 1), '0'), '.') ?>
        </td>
        <td class="a-txt3">
          <a href="/admin/chapters/<?= $c['id'] ?>/edit" class="a-link-muted">
            <?= esc($c['name'] ?: '&mdash;') ?>
          </a>
        </td>
        <td class="a-txt6 a-text-xs a-font-mono col-hidden col-md"><?= esc($c['slug'] ?? '') ?></td>
        <td class="a-txt4 a-text-xs col-hidden col-md"><?= number_format((int)($c['page_count'] ?? 0)) ?></td>
        <td>
          <label class="a-toggle"
                 data-id="<?= $c['id'] ?>"
                 data-show="<?= $c['is_show'] ? '1' : '0' ?>">
            <input type="checkbox" <?= $c['is_show'] ? 'checked' : '' ?> onchange="toggleShow(this.parentNode)">
            <div class="a-toggle-track"></div>
            <div class="a-toggle-thumb"></div>
          </label>
        </td>
        <td class="a-txt5 a-text-xs col-hidden col-md"><?= number_format((int)($c['view'] ?? 0)) ?></td>
        <td class="a-text-right">
          <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
            <a href="/admin/chapters/<?= $c['id'] ?>/edit" class="a-btn-sec a-btn-sm">Edit</a>
            <form method="post" action="/admin/chapters/<?= $c['id'] ?>/delete"
                  onsubmit="return confirm('Delete Chapter <?= rtrim(rtrim(number_format((float)$c['number'],1),'0'),'.') ?>?')">
              <?= csrf_field() ?>
              <button type="submit" class="a-btn-danger">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
      <tr>
        <td colspan="8" class="a-empty">
          No chapters yet. <a href="/admin/manga/<?= $manga['id'] ?>/chapters/new" class="a-link">Add first chapter</a>
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
    $base = '/admin/manga/' . $manga['id'] . '/chapters';
    $pts  = array_unique(array_filter([1, ($page??1)-1, ($page??1), ($page??1)+1, $totalPages], fn($p)=>$p>=1&&$p<=$totalPages));
    sort($pts);
    foreach ($pts as $pt) {
        if ($prev && $pt - $prev > 1) echo '<span class="a-pg-dots">&hellip;</span>';
        $active = $pt === ($page ?? 1);
        $url    = $base . '?' . http_build_query(array_filter(['q' => $q ?? '', 'page' => $pt > 1 ? $pt : null]));
        echo '<a href="'.esc($url).'" class="a-pg'.($active ? ' active' : '').'">'.$pt.'</a>';
        $prev = $pt;
    }
  ?>
</div>
<?php endif; ?>

<script>
function toggleShow(label) {
  var id      = label.dataset.id;
  var cb      = label.querySelector('input[type="checkbox"]');
  var current = label.dataset.show === '1';
  var next    = current ? 0 : 1;

  // Optimistic UI update
  label.dataset.show = next ? '1' : '0';
  cb.checked = !!next;

  fetch('/admin/chapters/' + id + '/toggle-show', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
    body: '<?= csrf_token() ?>=<?= csrf_hash() ?>&is_show=' + next
  })
  .then(function(r){ return r.json(); })
  .then(function(data){
    if (!data.ok) {
      // Revert on failure
      label.dataset.show = current ? '1' : '0';
      cb.checked = current;
    }
  })
  .catch(function(){});
}
</script>
