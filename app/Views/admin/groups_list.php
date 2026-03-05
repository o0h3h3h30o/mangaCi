<?php if ($flash = ($flash ?? null)): ?>
<div class="mb-5 px-4 py-3 rounded-lg text-sm <?= $flash['type']==='success' ? 'bg-green-900/40 border border-green-700 text-green-300' : 'bg-red-900/40 border border-red-700 text-red-300' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- Toolbar -->
<div class="flex items-center justify-between mb-5">
  <p class="text-sm text-gray-500"><?= count($groups) ?> group<?= count($groups) !== 1 ? 's' : '' ?></p>
  <a href="/admin/groups/new" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg transition-colors flex items-center gap-1.5">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    New Group
  </a>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-gray-800/50">
        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">#</th>
        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
        <th class="px-5 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-800">
      <?php foreach ($groups as $g): ?>
      <tr class="hover:bg-gray-800/30 transition-colors">
        <td class="px-5 py-4 text-gray-600 text-xs"><?= $g['id'] ?></td>
        <td class="px-5 py-4">
          <span class="font-medium text-gray-200"><?= esc($g['name']) ?></span>
          <?php if ($g['name'] === 'admin'): ?>
          <span class="ml-2 bg-indigo-900/40 text-indigo-400 text-xs px-1.5 py-0.5 rounded">admin</span>
          <?php endif; ?>
        </td>
        <td class="px-5 py-4">
          <a href="/admin/users?group=<?= $g['id'] ?>" class="text-gray-300 hover:text-indigo-400 text-sm transition-colors">
            <?= number_format((int)$g['member_count']) ?>
            <span class="text-gray-600 text-xs">user<?= (int)$g['member_count'] !== 1 ? 's' : '' ?></span>
          </a>
        </td>
        <td class="px-5 py-4">
          <div class="flex items-center justify-end gap-2">
            <a href="/admin/groups/<?= $g['id'] ?>/edit"
               class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs px-3 py-1.5 rounded-md transition-colors">
              Edit
            </a>
            <form method="post" action="/admin/groups/<?= $g['id'] ?>/delete"
                  onsubmit="return confirm('Delete group &quot;<?= esc($g['name']) ?>&quot;? This will remove all users from this group.')">
              <?= csrf_field() ?>
              <button type="submit" class="bg-red-900/40 hover:bg-red-900/70 text-red-400 text-xs px-3 py-1.5 rounded-md transition-colors">
                Delete
              </button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($groups)): ?>
      <tr><td colspan="4" class="px-5 py-10 text-center text-gray-600">No groups yet. <a href="/admin/groups/new" class="text-indigo-400 hover:underline">Create one</a></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
