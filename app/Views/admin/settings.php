<?php
$s   = $settings ?? [];
$get = fn(string $k, string $d = '') => $s[$k] ?? $d;
$flash = session()->getFlashdata('flash');
?>

<?php if (!empty($tableError)): ?>
<div class="a-flash a-flash-err">
  <p style="font-weight:600;margin-bottom:0.5rem">⚠ Bảng <code>site_settings</code> chưa tồn tại trong database.</p>
  <p style="font-size:12px;margin-bottom:0.75rem">Chạy SQL dưới đây trong phpMyAdmin hoặc MySQL client, sau đó reload trang:</p>
  <pre style="background:#111827;border-radius:0.375rem;padding:0.75rem;font-size:12px;overflow-x:auto">CREATE TABLE `site_settings` (
  `key`   VARCHAR(100) NOT NULL PRIMARY KEY,
  `value` TEXT NULL
);
INSERT INTO `site_settings` (`key`, `value`) VALUES
  ('site_title', 'MangaCI'), ('site_logo', ''),
  ('meta_description', ''), ('meta_keywords', '');</pre>
</div>
<?php endif; ?>

<?php if ($flash): ?>
<div class="a-flash <?= $flash['type'] === 'success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<form method="post" action="/admin/settings" enctype="multipart/form-data" class="a-max-w-2xl a-space-y-6">
  <?= csrf_field() ?>

  <!-- General -->
  <div class="a-panel" style="padding:1.25rem">
    <h3 class="a-label" style="font-size:0.875rem;font-weight:600;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#818cf8">
        <polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/>
      </svg>
      General
    </h3>

    <div class="a-space-y-4">
      <!-- Site Title -->
      <div>
        <label class="a-label">Site Title</label>
        <input type="text" name="site_title" value="<?= esc($get('site_title', 'MangaCI')) ?>"
               placeholder="MangaCI"
               class="a-input">
        <p class="a-hint">Short name shown in browser tab, sidebar and as title suffix.</p>
      </div>

      <!-- Home Heading -->
      <div>
        <label class="a-label">Home Heading Title</label>
        <input type="text" name="home_heading" value="<?= esc($get('home_heading', '')) ?>"
               placeholder="ManhwasMe : Read Webtoon (Korean Manhwa) Online Free!"
               class="a-input">
        <p class="a-hint">SEO heading displayed on home page instead of site title. Leave empty to use Site Title.</p>
      </div>

      <!-- Site Logo -->
      <div>
        <label class="a-label">Site Logo</label>

        <!-- Preview hiện tại -->
        <?php $currentLogo = $get('site_logo'); ?>
        <div id="logo-preview" class="<?= $currentLogo ? '' : 'a-hidden' ?>" style="margin-bottom:0.75rem">
          <div style="display:flex;align-items:center;gap:0.75rem">
            <img id="logo-img" src="<?= esc($currentLogo) ?>" alt="Logo"
                 style="height:3rem;border-radius:0.5rem;object-fit:contain;background:#1f2937;padding:0 0.5rem;border:1px solid #374151;max-width:200px">
            <button type="button" id="logo-remove-btn" style="font-size:12px;color:#f87171;cursor:pointer;background:none;border:none">Remove</button>
          </div>
        </div>

        <!-- Upload file -->
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem">
          <label class="a-btn-sec a-btn-sm" style="cursor:pointer;display:flex;align-items:center;gap:0.5rem">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Upload image
            <input type="file" name="site_logo_file" id="logo-file-input" accept="image/*" class="a-hidden">
          </label>
          <span style="font-size:12px;color:#4b5563">or</span>
          <input type="url" name="site_logo" id="logo-url-input" value="<?= esc($currentLogo) ?>"
                 placeholder="https://example.com/logo.png"
                 class="a-input" style="flex:1;font-size:12px">
        </div>
        <p class="a-hint">PNG, JPG, WebP, SVG. Để trống dùng favicon.ico.</p>
      </div>
    </div>
  </div>

  <!-- SEO -->
  <div class="a-panel" style="padding:1.25rem">
    <h3 class="a-label" style="font-size:0.875rem;font-weight:600;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#818cf8">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      SEO / Meta
    </h3>

    <div class="a-space-y-4">
      <div>
        <label class="a-label">
          Meta Description
          <span style="color:#4b5563;font-weight:normal;margin-left:0.25rem">(120–160 chars recommended)</span>
        </label>
        <textarea name="meta_description" rows="3" maxlength="300" id="meta-desc-input"
                  placeholder="Mô tả ngắn về website..."
                  class="a-textarea"><?= esc($get('meta_description')) ?></textarea>
        <p class="a-hint"><span id="meta-desc-count"><?= mb_strlen($get('meta_description')) ?></span> / 300</p>
      </div>

      <div>
        <label class="a-label">
          Meta Keywords
          <span style="color:#4b5563;font-weight:normal;margin-left:0.25rem">(comma separated)</span>
        </label>
        <input type="text" name="meta_keywords" value="<?= esc($get('meta_keywords')) ?>"
               placeholder="manga, manhwa, manhua..."
               class="a-input">
      </div>
    </div>
  </div>

  <!-- Analytics -->
  <div class="a-panel" style="padding:1.25rem">
    <h3 class="a-label" style="font-size:0.875rem;font-weight:600;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#818cf8">
        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
      </svg>
      Analytics
    </h3>
    <div>
      <label class="a-label">
        Google Analytics Measurement ID
        <span style="color:#4b5563;font-weight:normal;margin-left:0.25rem">(GA4)</span>
      </label>
      <input type="text" name="ga_id" value="<?= esc($get('ga_id')) ?>"
             placeholder="G-XXXXXXXXXX"
             class="a-input mono">
      <p class="a-hint">Để trống để tắt tracking. Tìm ID tại Google Analytics → Admin → Data Streams.</p>
    </div>
  </div>

  <!-- Footer -->
  <div class="a-panel" style="padding:1.25rem">
    <h3 class="a-label" style="font-size:0.875rem;font-weight:600;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#818cf8">
        <line x1="3" y1="22" x2="21" y2="22"/><line x1="3" y1="2" x2="21" y2="2"/><line x1="6" y1="6" x2="6" y2="18"/><line x1="18" y1="6" x2="18" y2="18"/>
      </svg>
      Footer
    </h3>

    <div class="a-space-y-4">
      <!-- Footer Logo -->
      <div>
        <label class="a-label">Footer Logo</label>

        <?php $currentFooterLogo = $get('footer_logo'); ?>
        <div id="footer-logo-preview" class="<?= $currentFooterLogo ? '' : 'a-hidden' ?>" style="margin-bottom:0.75rem">
          <div style="display:flex;align-items:center;gap:0.75rem">
            <img id="footer-logo-img" src="<?= esc($currentFooterLogo) ?>" alt="Footer Logo"
                 style="height:3rem;border-radius:0.5rem;object-fit:contain;background:#1f2937;padding:0 0.5rem;border:1px solid #374151;max-width:200px">
            <button type="button" id="footer-logo-remove-btn" style="font-size:12px;color:#f87171;cursor:pointer;background:none;border:none">Remove</button>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem">
          <label class="a-btn-sec a-btn-sm" style="cursor:pointer;display:flex;align-items:center;gap:0.5rem">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Upload image
            <input type="file" name="footer_logo_file" id="footer-logo-file-input" accept="image/*" class="a-hidden">
          </label>
          <span style="font-size:12px;color:#4b5563">or</span>
          <input type="url" name="footer_logo" id="footer-logo-url-input" value="<?= esc($currentFooterLogo) ?>"
                 placeholder="https://example.com/footer-logo.png"
                 class="a-input" style="flex:1;font-size:12px">
        </div>
        <p class="a-hint">Leave empty to use the same logo as the site logo.</p>
      </div>

      <!-- Footer Copyright -->
      <div>
        <label class="a-label">Copyright Text</label>
        <input type="text" name="footer_copyright" value="<?= esc($get('footer_copyright')) ?>"
               placeholder="© 2026 MangaCI. All rights reserved."
               class="a-input">
        <p class="a-hint">Shown in the footer below the logo.</p>
      </div>

      <!-- Footer URL -->
      <div>
        <label class="a-label">Footer Link URL</label>
        <input type="text" name="footer_url" value="<?= esc($get('footer_url', '/')) ?>"
               placeholder="/"
               class="a-input">
        <p class="a-hint">URL the footer logo/text links to.</p>
      </div>
    </div>
  </div>

  <!-- Theme -->
  <div class="a-panel" style="padding:1.25rem">
    <h3 class="a-label" style="font-size:0.875rem;font-weight:600;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#818cf8">
        <circle cx="12" cy="12" r="10"/><path d="M12 2a7 7 0 0 1 7 7"/>
      </svg>
      Theme
    </h3>
    <div>
      <label class="a-label">Active Theme</label>
      <select name="active_theme" class="a-select">
        <?php foreach ($themes ?? [] as $t): ?>
          <option value="<?= esc($t) ?>" <?= $get('active_theme', 'default') === $t ? 'selected' : '' ?>>
            <?= esc($t) ?>
          </option>
        <?php endforeach; ?>
        <?php if (empty($themes)): ?>
          <option value="default" selected>default</option>
        <?php endif; ?>
      </select>
      <p class="a-hint">Mỗi theme là một thư mục trong <code style="color:#6b7280">app/Views/themes/</code>.</p>
    </div>
    <div style="margin-top:16px">
      <label class="a-label">Site Language</label>
      <select name="site_language" class="a-select">
        <option value="en" <?= $get('site_language', 'en') === 'en' ? 'selected' : '' ?>>English</option>
        <option value="es" <?= $get('site_language', 'en') === 'es' ? 'selected' : '' ?>>Español</option>
        <option value="ja" <?= $get('site_language', 'en') === 'ja' ? 'selected' : '' ?>>日本語</option>
      </select>
      <p class="a-hint">Language for the frontend theme. Language files in <code style="color:#6b7280">app/Language/</code>.</p>
    </div>
  </div>

  <div style="display:flex;justify-content:flex-end">
    <button type="submit" class="a-btn">
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
    document.getElementById('logo-preview').classList.remove('a-hidden');
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
    preview.classList.remove('a-hidden');
  } else {
    preview.classList.add('a-hidden');
  }
});

// Remove logo
document.getElementById('logo-remove-btn').addEventListener('click', function(){
  document.getElementById('logo-url-input').value = '';
  document.getElementById('logo-file-input').value = '';
  document.getElementById('logo-preview').classList.add('a-hidden');
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
    document.getElementById('footer-logo-preview').classList.remove('a-hidden');
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
    preview.classList.remove('a-hidden');
  } else {
    preview.classList.add('a-hidden');
  }
});

// Footer logo: Remove
document.getElementById('footer-logo-remove-btn').addEventListener('click', function(){
  document.getElementById('footer-logo-url-input').value = '';
  document.getElementById('footer-logo-file-input').value = '';
  document.getElementById('footer-logo-preview').classList.add('a-hidden');
});
</script>
