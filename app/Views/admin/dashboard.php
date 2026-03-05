<?php
$cards = [
    ['label' => 'Total Users',    'value' => $stats['users'],    'color' => 'text-blue-400',   'bg' => 'bg-blue-500/10',   'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
    ['label' => 'Total Manga',    'value' => $stats['manga'],    'color' => 'text-purple-400', 'bg' => 'bg-purple-500/10', 'icon' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>'],
    ['label' => 'Total Comments', 'value' => $stats['comments'], 'color' => 'text-green-400',  'bg' => 'bg-green-500/10',  'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
    ['label' => 'Groups',         'value' => $stats['groups'],   'color' => 'text-orange-400', 'bg' => 'bg-orange-500/10', 'icon' => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>'],
];
?>

<!-- Stats cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <?php foreach ($cards as $c): ?>
  <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 flex items-center gap-4">
    <div class="<?= $c['bg'] ?> <?= $c['color'] ?> w-11 h-11 rounded-lg flex items-center justify-center shrink-0">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <?= $c['icon'] ?>
      </svg>
    </div>
    <div>
      <div class="text-2xl font-bold text-gray-100"><?= number_format($c['value']) ?></div>
      <div class="text-xs text-gray-500 mt-0.5"><?= $c['label'] ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Quick links -->
<div class="flex gap-3 mb-6">
  <a href="/admin/users" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg transition-colors">Manage Users</a>
  <a href="/admin/groups" class="bg-gray-700 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded-lg transition-colors">Manage Groups</a>
</div>

<!-- Recent users -->
<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
  <div class="px-5 py-4 border-b border-gray-800 flex items-center justify-between">
    <span class="font-semibold text-sm text-gray-200">Recent Users</span>
    <a href="/admin/users" class="text-xs text-indigo-400 hover:underline">View all →</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-gray-800/50">
          <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
          <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
          <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-800">
        <?php foreach ($recentUsers as $u): ?>
        <?php
          $name = $u['name'] ?: $u['username'];
          $ch   = strtoupper(mb_substr($name, 0, 1));
          $colors = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];
          $bg = $colors[$u['id'] % 6];
          $date = !empty($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : '—';
        ?>
        <tr class="hover:bg-gray-800/30 transition-colors">
          <td class="px-5 py-3">
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
          <td class="px-5 py-3 text-gray-400 text-xs"><?= esc($u['email']) ?></td>
          <td class="px-5 py-3">
            <?php if ($u['active']): ?>
              <span class="bg-green-900/40 text-green-400 text-xs px-2 py-0.5 rounded-full font-medium">Active</span>
            <?php else: ?>
              <span class="bg-gray-800 text-gray-500 text-xs px-2 py-0.5 rounded-full font-medium">Inactive</span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3 text-gray-500 text-xs"><?= $date ?></td>
          <td class="px-5 py-3 text-right">
            <a href="/admin/users/<?= $u['id'] ?>/edit" class="text-indigo-400 hover:text-indigo-300 text-xs hover:underline">Edit</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recentUsers)): ?>
        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-600 text-sm">No users found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
