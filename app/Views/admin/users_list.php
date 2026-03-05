<?php
$colors = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];

function adminPagerUrl(array $params): string {
    $p = array_filter($params, fn($v) => $v !== '' && $v !== 0 && $v !== null);
    return '/admin/users' . ($p ? '?' . http_build_query($p) : '');
}
?>

<!-- Toolbar -->
<div class="flex flex-col sm:flex-row gap-3 mb-5">
  <form method="get" action="/admin/users" class="flex gap-2 flex-1">
    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search name, username, email…"
           class="flex-1 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500">
    <?php if ($gf): ?><input type="hidden" name="group" value="<?= $gf ?>"><?php endif; ?>
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg transition-colors">Search</button>
    <?php if ($q || $gf): ?>
    <a href="/admin/users" class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm px-4 py-2 rounded-lg transition-colors">Clear</a>
    <?php endif; ?>
  </form>

  <!-- Group filter -->
  <form method="get" action="/admin/users">
    <?php if ($q): ?><input type="hidden" name="q" value="<?= esc($q) ?>"><?php endif; ?>
    <select name="group" onchange="this.form.submit()"
            class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-indigo-500">
      <option value="0">All groups</option>
      <?php foreach ($groups as $g): ?>
      <option value="<?= $g['id'] ?>" <?= $gf === (int)$g['id'] ? 'selected' : '' ?>><?= esc($g['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<!-- Count -->
<p class="text-xs text-gray-500 mb-3"><?= number_format($total) ?> user<?= $total !== 1 ? 's' : '' ?> found</p>

<!-- Table -->
<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-4">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-gray-800/50">
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">#</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Groups</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-800">
        <?php foreach ($users as $u): ?>
        <?php
          $name   = $u['name'] ?: $u['username'];
          $ch     = strtoupper(mb_substr($name, 0, 1));
          $bg     = $colors[$u['id'] % 6];
          $joined = !empty($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : '—';
          $ll     = !empty($u['last_login']) ? date('M d, Y', strtotime($u['last_login'])) : '—';
        ?>
        <tr class="hover:bg-gray-800/30 transition-colors">
          <td class="px-4 py-3 text-gray-600 text-xs"><?= $u['id'] ?></td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0" style="background:<?= $bg ?>">
                <?= esc($ch) ?>
              </div>
              <div>
                <div class="font-medium text-gray-200"><?= esc($name) ?></div>
                <div class="text-xs text-gray-500">@<?= esc($u['username']) ?></div>
              </div>
            </div>
          </td>
          <td class="px-4 py-3 text-gray-400 text-xs"><?= esc($u['email']) ?></td>
          <td class="px-4 py-3">
            <span class="text-xs text-gray-400"><?= esc($u['user_groups']) ?></span>
          </td>
          <td class="px-4 py-3">
            <?php if ($u['active']): ?>
              <span class="bg-green-900/40 text-green-400 text-xs px-2 py-0.5 rounded-full">Active</span>
            <?php else: ?>
              <span class="bg-gray-800 text-gray-500 text-xs px-2 py-0.5 rounded-full">Inactive</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-gray-500 text-xs"><?= $ll ?></td>
          <td class="px-4 py-3 text-gray-500 text-xs"><?= $joined ?></td>
          <td class="px-4 py-3 text-right">
            <a href="/admin/users/<?= $u['id'] ?>/edit"
               class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs px-3 py-1.5 rounded-md transition-colors inline-block">
              Edit
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-600">No users found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="flex items-center justify-center gap-1">
  <?php
    $pBase = ['q' => $q ?: null, 'group' => $gf ?: null];
    $showPages = [];
    // Always show: 1, current-1, current, current+1, last
    $pts = array_unique(array_filter([1, $page-1, $page, $page+1, $totalPages], fn($p)=>$p>=1&&$p<=$totalPages));
    sort($pts);
    $prev = 0;
    foreach ($pts as $pt) {
        if ($prev && $pt - $prev > 1) {
            echo '<span class="px-2 text-gray-600">…</span>';
        }
        $active = $pt === $page;
        $url = adminPagerUrl(array_merge($pBase, ['page' => $pt > 1 ? $pt : null]));
        echo '<a href="'.esc($url).'" class="'.($active
            ? 'bg-indigo-600 text-white'
            : 'bg-gray-800 text-gray-400 hover:bg-gray-700').
            ' w-9 h-9 flex items-center justify-center rounded-lg text-sm transition-colors">'.$pt.'</a>';
        $prev = $pt;
    }
  ?>
</div>
<?php endif; ?>
