<?php
$colors = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];

function adminPagerUrl(array $params): string {
    $p = array_filter($params, fn($v) => $v !== '' && $v !== 0 && $v !== null);
    return '/admin/users' . ($p ? '?' . http_build_query($p) : '');
}
?>

<!-- Toolbar -->
<div class="a-toolbar">
  <form method="get" action="/admin/users" class="a-search-wrap">
    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search name, username, email…" class="a-search-input">
    <?php if ($gf): ?><input type="hidden" name="group" value="<?= $gf ?>"><?php endif; ?>
    <button type="submit" class="a-btn a-btn-sm">Search</button>
    <?php if ($q || $gf): ?>
    <a href="/admin/users" class="a-btn-sec a-btn-sm">Clear</a>
    <?php endif; ?>
  </form>

  <!-- Group filter -->
  <form method="get" action="/admin/users">
    <?php if ($q): ?><input type="hidden" name="q" value="<?= esc($q) ?>"><?php endif; ?>
    <select name="group" onchange="this.form.submit()" class="a-select" style="width:auto">
      <option value="0">All groups</option>
      <?php foreach ($groups as $g): ?>
      <option value="<?= $g['id'] ?>" <?= $gf === (int)$g['id'] ? 'selected' : '' ?>><?= esc($g['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<p class="a-count"><?= number_format($total) ?> user<?= $total !== 1 ? 's' : '' ?> found</p>

<div class="a-panel" style="margin-bottom:16px">
  <div class="a-overflow-x">
    <table class="a-table">
      <thead>
        <tr>
          <th style="width:40px">#</th>
          <th>User</th>
          <th>Email</th>
          <th>Groups</th>
          <th>Status</th>
          <th>Last Login</th>
          <th>Joined</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <?php
          $name   = $u['name'] ?: $u['username'];
          $ch     = strtoupper(mb_substr($name, 0, 1));
          $bg     = $colors[$u['id'] % 6];
          $joined = !empty($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : '—';
          $ll     = !empty($u['last_login']) ? date('M d, Y', strtotime($u['last_login'])) : '—';
        ?>
        <tr>
          <td class="a-txt6 a-text-xs"><?= $u['id'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:12px">
              <div class="a-avatar a-avatar-sm" style="background:<?= $bg ?>"><?= esc($ch) ?></div>
              <div>
                <div class="a-font-medium a-txt2"><?= esc($name) ?></div>
                <div class="a-text-xs a-txt5">@<?= esc($u['username']) ?></div>
              </div>
            </div>
          </td>
          <td class="a-txt4 a-text-xs"><?= esc($u['email']) ?></td>
          <td class="a-txt4 a-text-xs"><?= esc($u['user_groups']) ?></td>
          <td>
            <?php if ($u['active']): ?>
              <span class="a-badge a-badge-green">Active</span>
            <?php else: ?>
              <span class="a-badge a-badge-gray">Inactive</span>
            <?php endif; ?>
          </td>
          <td class="a-txt5 a-text-xs"><?= $ll ?></td>
          <td class="a-txt5 a-text-xs"><?= $joined ?></td>
          <td class="a-text-right">
            <a href="/admin/users/<?= $u['id'] ?>/edit" class="a-btn-sec a-btn-sm">Edit</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
        <tr><td colspan="8" class="a-empty">No users found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="a-pager">
  <?php
    $pBase = ['q' => $q ?: null, 'group' => $gf ?: null];
    $pts = array_unique(array_filter([1, $page-1, $page, $page+1, $totalPages], fn($p)=>$p>=1&&$p<=$totalPages));
    sort($pts); $prev = 0;
    foreach ($pts as $pt) {
        if ($prev && $pt - $prev > 1) echo '<span class="a-pg-dots">…</span>';
        $url = adminPagerUrl(array_merge($pBase, ['page' => $pt > 1 ? $pt : null]));
        echo '<a href="'.esc($url).'" class="a-pg'.($pt===$page ? ' active' : '').'">'.$pt.'</a>';
        $prev = $pt;
    }
  ?>
</div>
<?php endif; ?>
