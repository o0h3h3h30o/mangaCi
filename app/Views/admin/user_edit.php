<?php
$colors = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];
$name   = $user['name'] ?: $user['username'];
$ch     = strtoupper(mb_substr($name, 0, 1));
$bg     = $colors[$user['id'] % 6];
?>

<!-- Back link -->
<div class="mb-5">
  <a href="/admin/users" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">← Back to Users</a>
</div>

<!-- Flash -->
<?php if ($flash = ($flash ?? null)): ?>
<div class="mb-5 px-4 py-3 rounded-lg text-sm <?= $flash['type']==='success' ? 'bg-green-900/40 border border-green-700 text-green-300' : 'bg-red-900/40 border border-red-700 text-red-300' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<form method="post" action="/admin/users/<?= $user['id'] ?>/edit">
  <?= csrf_field() ?>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- Left: info -->
    <div class="lg:col-span-2 space-y-5">

      <!-- Profile header card -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold shrink-0" style="background:<?= $bg ?>">
          <?= esc($ch) ?>
        </div>
        <div>
          <div class="font-semibold text-gray-100"><?= esc($name) ?></div>
          <div class="text-sm text-gray-500">@<?= esc($user['username']) ?> · ID <?= $user['id'] ?></div>
          <?php if (!empty($user['created_at'])): ?>
          <div class="text-xs text-gray-600 mt-0.5">Joined <?= date('M d, Y', strtotime($user['created_at'])) ?></div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Basic info -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Basic Info</div>
        <div class="p-5 space-y-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Display Name</label>
            <input type="text" name="name" value="<?= esc($user['name']) ?>" required
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Username</label>
            <input type="text" name="username" value="<?= esc($user['username']) ?>" required
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Email</label>
            <input type="email" name="email" value="<?= esc($user['email']) ?>" required
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">New Password <span class="text-gray-600">(leave blank to keep current)</span></label>
            <input type="password" name="password" autocomplete="new-password"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors"
                   placeholder="Min. 6 characters">
          </div>
        </div>
      </div>

    </div>

    <!-- Right: status + groups -->
    <div class="space-y-5">

      <!-- Status -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Status</div>
        <div class="p-5 space-y-3">
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="radio" name="active" value="1" <?= $user['active'] ? 'checked' : '' ?>
                   class="w-4 h-4 accent-green-500">
            <div>
              <div class="text-sm text-gray-200 font-medium">Active</div>
              <div class="text-xs text-gray-600">User can log in and comment</div>
            </div>
          </label>
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="radio" name="active" value="0" <?= !$user['active'] ? 'checked' : '' ?>
                   class="w-4 h-4 accent-red-500">
            <div>
              <div class="text-sm text-gray-200 font-medium">Inactive</div>
              <div class="text-xs text-gray-600">User cannot log in</div>
            </div>
          </label>
        </div>
      </div>

      <!-- Groups -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Groups</div>
        <div class="p-5 space-y-2.5">
          <?php if (empty($allGroups)): ?>
          <p class="text-xs text-gray-600">No groups defined. <a href="/admin/groups/new" class="text-indigo-400 hover:underline">Create one</a></p>
          <?php else: ?>
          <?php foreach ($allGroups as $g): ?>
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="groups[]" value="<?= $g['id'] ?>"
                   <?= in_array((int)$g['id'], $userGroups, true) ? 'checked' : '' ?>
                   class="w-4 h-4 accent-indigo-500 rounded">
            <span class="text-sm text-gray-300"><?= esc($g['name']) ?></span>
            <?php if ($g['description']): ?>
            <span class="text-xs text-gray-600 truncate"><?= esc($g['description']) ?></span>
            <?php endif; ?>
          </label>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Save -->
      <button type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 rounded-xl transition-colors text-sm">
        Save Changes
      </button>
    </div>

  </div>
</form>
