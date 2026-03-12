<?php
$isEdit = !empty($item);
$action = $isEdit ? "{$baseUrl}/{$item['id']}/edit" : "{$baseUrl}/new";
?>

<div class="a-mb-5">
  <a href="<?= $baseUrl ?>" class="a-link-back">← Back</a>
</div>

<?php if ($flash = ($flash ?? null)): ?>
<div class="a-flash <?= $flash['type']==='success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<div class="a-max-w-md">
  <div class="a-panel">
    <div class="a-panel-head"><?= esc($title) ?></div>
    <form method="post" action="<?= $action ?>">
      <?= csrf_field() ?>
      <div class="a-panel-body">

        <div>
          <label class="a-label">Name <span class="req">*</span></label>
          <input type="text" name="name" id="tf-name" value="<?= esc($item['name'] ?? '') ?>" required
                 placeholder="Display name"
                 class="a-input">
        </div>

        <?php if ($hasSlug): ?>
        <div>
          <label class="a-label">Slug <span class="hint">(auto-generated if empty)</span></label>
          <input type="text" name="slug" id="tf-slug" value="<?= esc($item['slug'] ?? '') ?>"
                 placeholder="url-friendly-name"
                 class="a-input mono">
        </div>
        <?php endif; ?>

      </div>
      <div style="padding:0 20px 20px;display:flex;gap:12px">
        <button type="submit" class="a-btn"><?= $isEdit ? 'Save Changes' : 'Create' ?></button>
        <a href="<?= $baseUrl ?>" class="a-btn-sec">Cancel</a>
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
