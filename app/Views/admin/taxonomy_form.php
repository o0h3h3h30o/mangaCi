<?php
$isEdit = !empty($item);
$action = $isEdit ? "{$baseUrl}/{$item['id']}/edit" : "{$baseUrl}/new";
?>

<div class="mb-5">
  <a href="<?= $baseUrl ?>" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">← Back</a>
</div>

<?php if ($flash = ($flash ?? null)): ?>
<div class="mb-5 px-4 py-3 rounded-lg text-sm <?= $flash['type']==='success' ? 'bg-green-900/40 border border-green-700 text-green-300' : 'bg-red-900/40 border border-red-700 text-red-300' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<div class="max-w-md">
  <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">
      <?= esc($title) ?>
    </div>
    <form method="post" action="<?= $action ?>">
      <?= csrf_field() ?>
      <div class="p-5 space-y-4">

        <div>
          <label class="block text-xs text-gray-500 mb-1.5">Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" id="tf-name" value="<?= esc($item['name'] ?? '') ?>" required
                 placeholder="Display name"
                 class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors">
        </div>

        <?php if ($hasSlug): ?>
        <div>
          <label class="block text-xs text-gray-500 mb-1.5">Slug <span class="text-gray-600">(auto-generated if empty)</span></label>
          <input type="text" name="slug" id="tf-slug" value="<?= esc($item['slug'] ?? '') ?>"
                 placeholder="url-friendly-name"
                 class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500 transition-colors">
        </div>
        <?php endif; ?>

      </div>
      <div class="px-5 pb-5 flex gap-3">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-5 py-2.5 rounded-lg transition-colors font-medium">
          <?= $isEdit ? 'Save Changes' : 'Create' ?>
        </button>
        <a href="<?= $baseUrl ?>" class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm px-5 py-2.5 rounded-lg transition-colors">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<?php if ($hasSlug): ?>
<script>
(function(){
  var nameEl = document.getElementById('tf-name');
  var slugEl = document.getElementById('tf-slug');
  var userEdited = slugEl.value !== '';
  slugEl.addEventListener('input', function(){ userEdited = true; });
  nameEl.addEventListener('input', function(){
    if (userEdited) return;
    slugEl.value = nameEl.value.toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
  });
})();
</script>
<?php endif; ?>
