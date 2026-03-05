<?php
$isEdit = !empty($group);
$action = $isEdit ? "/admin/groups/{$group['id']}/edit" : '/admin/groups/new';
?>

<div class="mb-5">
  <a href="/admin/groups" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">← Back to Groups</a>
</div>

<?php if ($flash = ($flash ?? null)): ?>
<div class="mb-5 px-4 py-3 rounded-lg text-sm <?= $flash['type']==='success' ? 'bg-green-900/40 border border-green-700 text-green-300' : 'bg-red-900/40 border border-red-700 text-red-300' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<div class="max-w-md">
  <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">
      <?= $isEdit ? 'Edit Group' : 'Create New Group' ?>
    </div>
    <form method="post" action="<?= $action ?>">
      <?= csrf_field() ?>
      <div class="p-5">
        <label class="block text-xs text-gray-500 mb-1.5">Group Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="<?= esc($group['name'] ?? '') ?>" required
               placeholder="e.g. admin, moderator, vip"
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors">
      </div>
      <div class="px-5 pb-5 flex gap-3">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-5 py-2.5 rounded-lg transition-colors font-medium">
          <?= $isEdit ? 'Save Changes' : 'Create Group' ?>
        </button>
        <a href="/admin/groups" class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm px-5 py-2.5 rounded-lg transition-colors">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>
