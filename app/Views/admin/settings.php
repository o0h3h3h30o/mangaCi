<?php
$s   = $settings ?? [];
$get = fn(string $k, string $d = '') => $s[$k] ?? $d;
$flash = session()->getFlashdata('flash');
?>

<?php if (!empty($tableError)): ?>
<div class="mb-5 px-4 py-4 rounded-lg text-sm bg-red-900/30 border border-red-800 text-red-300">
  <p class="font-semibold mb-2">⚠ Bảng <code>site_settings</code> chưa tồn tại trong database.</p>
  <p class="text-xs text-red-400 mb-3">Chạy SQL dưới đây trong phpMyAdmin hoặc MySQL client, sau đó reload trang:</p>
  <pre class="bg-gray-900 rounded p-3 text-xs text-gray-300 overflow-x-auto">CREATE TABLE `site_settings` (
  `key`   VARCHAR(100) NOT NULL PRIMARY KEY,
  `value` TEXT NULL
);
INSERT INTO `site_settings` (`key`, `value`) VALUES
  ('site_title', 'MangaCI'), ('site_logo', ''),
  ('meta_description', ''), ('meta_keywords', '');</pre>
</div>
<?php endif; ?>

<?php if ($flash): ?>
<div class="mb-5 px-4 py-3 rounded-lg text-sm font-medium
  <?= $flash['type'] === 'success' ? 'bg-green-900/40 text-green-400 border border-green-800' : 'bg-red-900/40 text-red-400 border border-red-800' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<form method="post" action="/admin/settings" enctype="multipart/form-data" class="max-w-2xl space-y-6">
  <?= csrf_field() ?>

  <!-- General -->
  <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
    <h3 class="text-sm font-semibold text-gray-200 mb-4 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400">
        <polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/>
      </svg>
      General
    </h3>

    <div class="space-y-4">
      <!-- Site Title -->
      <div>
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Site Title</label>
        <input type="text" name="site_title" value="<?= esc($get('site_title', 'MangaCI')) ?>"
               placeholder="MangaCI"
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors">
        <p class="text-xs text-gray-600 mt-1">Hiện ở tab trình duyệt, sidebar mobile và suffix của title.</p>
      </div>

      <!-- Site Logo -->
      <div>
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Site Logo</label>

        <!-- Preview hiện tại -->
        <?php $currentLogo = $get('site_logo'); ?>
        <div id="logo-preview" class="mb-3 <?= $currentLogo ? '' : 'hidden' ?>">
          <div class="flex items-center gap-3">
            <img id="logo-img" src="<?= esc($currentLogo) ?>" alt="Logo"
                 class="h-12 rounded-lg object-contain bg-gray-800 px-2 border border-gray-700 max-w-[200px]">
            <button type="button" id="logo-remove-btn" class="text-xs text-red-400 hover:text-red-300 transition-colors">Remove</button>
          </div>
        </div>

        <!-- Upload file -->
        <div class="flex items-center gap-3 mb-2">
          <label class="cursor-pointer bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs px-3 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Upload image
            <input type="file" name="site_logo_file" id="logo-file-input" accept="image/*" class="hidden">
          </label>
          <span class="text-gray-600 text-xs">or</span>
          <input type="url" name="site_logo" id="logo-url-input" value="<?= esc($currentLogo) ?>"
                 placeholder="https://example.com/logo.png"
                 class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-xs text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors">
        </div>
        <p class="text-xs text-gray-600">PNG, JPG, WebP, SVG. Để trống dùng favicon.ico.</p>
      </div>
    </div>
  </div>

  <!-- SEO -->
  <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
    <h3 class="text-sm font-semibold text-gray-200 mb-4 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      SEO / Meta
    </h3>

    <div class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-gray-400 mb-1.5">
          Meta Description
          <span class="text-gray-600 font-normal ml-1">(120–160 chars recommended)</span>
        </label>
        <textarea name="meta_description" rows="3" maxlength="300" id="meta-desc-input"
                  placeholder="Mô tả ngắn về website..."
                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors resize-none"><?= esc($get('meta_description')) ?></textarea>
        <p class="text-xs text-gray-600 mt-1"><span id="meta-desc-count"><?= mb_strlen($get('meta_description')) ?></span> / 300</p>
      </div>

      <div>
        <label class="block text-xs font-medium text-gray-400 mb-1.5">
          Meta Keywords
          <span class="text-gray-600 font-normal ml-1">(comma separated)</span>
        </label>
        <input type="text" name="meta_keywords" value="<?= esc($get('meta_keywords')) ?>"
               placeholder="manga, manhwa, manhua..."
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors">
      </div>
    </div>
  </div>

  <!-- Analytics -->
  <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
    <h3 class="text-sm font-semibold text-gray-200 mb-4 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400">
        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
      </svg>
      Analytics
    </h3>
    <div>
      <label class="block text-xs font-medium text-gray-400 mb-1.5">
        Google Analytics Measurement ID
        <span class="text-gray-600 font-normal ml-1">(GA4)</span>
      </label>
      <input type="text" name="ga_id" value="<?= esc($get('ga_id')) ?>"
             placeholder="G-XXXXXXXXXX"
             class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors font-mono">
      <p class="text-xs text-gray-600 mt-1">Để trống để tắt tracking. Tìm ID tại Google Analytics → Admin → Data Streams.</p>
    </div>
  </div>

  <!-- Footer -->
  <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
    <h3 class="text-sm font-semibold text-gray-200 mb-4 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400">
        <line x1="3" y1="22" x2="21" y2="22"/><line x1="3" y1="2" x2="21" y2="2"/><line x1="6" y1="6" x2="6" y2="18"/><line x1="18" y1="6" x2="18" y2="18"/>
      </svg>
      Footer
    </h3>

    <div class="space-y-4">
      <!-- Footer Logo -->
      <div>
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Footer Logo</label>

        <?php $currentFooterLogo = $get('footer_logo'); ?>
        <div id="footer-logo-preview" class="mb-3 <?= $currentFooterLogo ? '' : 'hidden' ?>">
          <div class="flex items-center gap-3">
            <img id="footer-logo-img" src="<?= esc($currentFooterLogo) ?>" alt="Footer Logo"
                 class="h-12 rounded-lg object-contain bg-gray-800 px-2 border border-gray-700 max-w-[200px]">
            <button type="button" id="footer-logo-remove-btn" class="text-xs text-red-400 hover:text-red-300 transition-colors">Remove</button>
          </div>
        </div>

        <div class="flex items-center gap-3 mb-2">
          <label class="cursor-pointer bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs px-3 py-2 rounded-lg transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Upload image
            <input type="file" name="footer_logo_file" id="footer-logo-file-input" accept="image/*" class="hidden">
          </label>
          <span class="text-gray-600 text-xs">or</span>
          <input type="url" name="footer_logo" id="footer-logo-url-input" value="<?= esc($currentFooterLogo) ?>"
                 placeholder="https://example.com/footer-logo.png"
                 class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-xs text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors">
        </div>
        <p class="text-xs text-gray-600">Leave empty to use the same logo as the site logo.</p>
      </div>

      <!-- Footer Copyright -->
      <div>
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Copyright Text</label>
        <input type="text" name="footer_copyright" value="<?= esc($get('footer_copyright')) ?>"
               placeholder="© 2026 MangaCI. All rights reserved."
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors">
        <p class="text-xs text-gray-600 mt-1">Shown in the footer below the logo.</p>
      </div>

      <!-- Footer URL -->
      <div>
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Footer Link URL</label>
        <input type="text" name="footer_url" value="<?= esc($get('footer_url', '/')) ?>"
               placeholder="/"
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors">
        <p class="text-xs text-gray-600 mt-1">URL the footer logo/text links to.</p>
      </div>
    </div>
  </div>

  <!-- Theme -->
  <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
    <h3 class="text-sm font-semibold text-gray-200 mb-4 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400">
        <circle cx="12" cy="12" r="10"/><path d="M12 2a7 7 0 0 1 7 7"/>
      </svg>
      Theme
    </h3>
    <div>
      <label class="block text-xs font-medium text-gray-400 mb-1.5">Active Theme</label>
      <select name="active_theme"
              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors">
        <?php foreach ($themes ?? [] as $t): ?>
          <option value="<?= esc($t) ?>" <?= $get('active_theme', 'default') === $t ? 'selected' : '' ?>>
            <?= esc($t) ?>
          </option>
        <?php endforeach; ?>
        <?php if (empty($themes)): ?>
          <option value="default" selected>default</option>
        <?php endif; ?>
      </select>
      <p class="text-xs text-gray-600 mt-1">Mỗi theme là một thư mục trong <code class="text-gray-500">app/Views/themes/</code>.</p>
    </div>
  </div>

  <div class="flex justify-end">
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
      Save Settings
    </button>
  </div>
</form>

<script>
// Upload preview
document.getElementById('logo-file-input').addEventListener('change', function(){
  var file = this.files[0];
  if(!file) return;
  var reader = new FileReader();
  reader.onload = function(e){
    document.getElementById('logo-img').src = e.target.result;
    document.getElementById('logo-preview').classList.remove('hidden');
    document.getElementById('logo-url-input').value = '';
  };
  reader.readAsDataURL(file);
});

// URL preview
document.getElementById('logo-url-input').addEventListener('input', function(){
  var url = this.value.trim();
  var preview = document.getElementById('logo-preview');
  if(url){
    document.getElementById('logo-img').src = url;
    preview.classList.remove('hidden');
  } else {
    preview.classList.add('hidden');
  }
});

// Remove logo
document.getElementById('logo-remove-btn').addEventListener('click', function(){
  document.getElementById('logo-url-input').value = '';
  document.getElementById('logo-file-input').value = '';
  document.getElementById('logo-preview').classList.add('hidden');
});

// Char counter
document.getElementById('meta-desc-input').addEventListener('input', function(){
  document.getElementById('meta-desc-count').textContent = this.value.length;
});

// Footer logo: Upload preview
document.getElementById('footer-logo-file-input').addEventListener('change', function(){
  var file = this.files[0];
  if(!file) return;
  var reader = new FileReader();
  reader.onload = function(e){
    document.getElementById('footer-logo-img').src = e.target.result;
    document.getElementById('footer-logo-preview').classList.remove('hidden');
    document.getElementById('footer-logo-url-input').value = '';
  };
  reader.readAsDataURL(file);
});

// Footer logo: URL preview
document.getElementById('footer-logo-url-input').addEventListener('input', function(){
  var url = this.value.trim();
  var preview = document.getElementById('footer-logo-preview');
  if(url){
    document.getElementById('footer-logo-img').src = url;
    preview.classList.remove('hidden');
  } else {
    preview.classList.add('hidden');
  }
});

// Footer logo: Remove
document.getElementById('footer-logo-remove-btn').addEventListener('click', function(){
  document.getElementById('footer-logo-url-input').value = '';
  document.getElementById('footer-logo-file-input').value = '';
  document.getElementById('footer-logo-preview').classList.add('hidden');
});
</script>
