<?php
$isEdit = !empty($chapter);
$action = $isEdit
    ? "/admin/chapters/{$chapter['id']}/edit"
    : "/admin/manga/{$manga['id']}/chapters/new";
?>

<!-- Breadcrumb -->
<div class="a-crumbs">
  <a href="/admin/manga">&larr; Manga</a>
  <span class="sep">/</span>
  <a href="/admin/manga/<?= $manga['id'] ?>/chapters" class="accent a-truncate" style="max-width:200px"><?= esc($manga['name']) ?></a>
  <span class="sep">/</span>
  <span><?= $isEdit ? 'Edit Chapter' : 'New Chapter' ?></span>
</div>

<?php if ($flash = ($flash ?? null)): ?>
<div class="a-flash <?= $flash['type']==='success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<form method="post" action="<?= $action ?>">
  <?= csrf_field() ?>
  <div class="a-grid-form">

    <!-- Left -->
    <div class="a-space-y-5">

      <div class="a-panel">
        <div class="a-panel-head">Chapter Info</div>
        <div class="a-panel-body">

          <div>
            <label class="a-label">Name <span class="hint">(optional)</span></label>
            <input type="text" name="name" id="ch-name"
                   value="<?= esc($chapter['name'] ?? '') ?>"
                   placeholder="Chapter title"
                   class="a-input">
          </div>

          <div class="a-grid-2">
            <div>
              <label class="a-label">Number <span class="req">*</span></label>
              <input type="text" name="number" id="ch-number"
                     value="<?= esc($chapter['number'] ?? '') ?>"
                     required
                     placeholder="e.g. 1 or 1.5"
                     class="a-input mono">
            </div>
            <div>
              <label class="a-label">Slug <span class="hint">(auto)</span></label>
              <input type="text" name="slug" id="ch-slug"
                     value="<?= esc($chapter['slug'] ?? '') ?>"
                     placeholder="chapter-1"
                     class="a-input mono">
            </div>
          </div>

          <div>
            <label class="a-label">Source URL <span class="hint">(crawl source)</span></label>
            <input type="text" name="source_url"
                   value="<?= esc($chapter['source_url'] ?? '') ?>"
                   placeholder="https://source-site.com/manga/chapter-1"
                   class="a-input mono">
          </div>

        </div>
      </div>

    </div>

    <!-- Right -->
    <div class="a-space-y-5">

      <div class="a-panel">
        <div class="a-panel-head">Settings</div>
        <div class="a-panel-body">

          <div class="a-toggle-row">
            <div>
              <div class="a-label" style="margin-bottom:0">Visible</div>
              <div class="a-hint" style="margin-top:0">Show to readers</div>
            </div>
            <label class="a-toggle">
              <input type="checkbox" name="is_show" value="1"
                     <?= ($chapter['is_show'] ?? 1) ? 'checked' : '' ?>>
              <div class="a-toggle-track"></div>
              <div class="a-toggle-thumb"></div>
            </label>
          </div>

          <div>
            <label class="a-label">Crawl Status</label>
            <?php $crawling = (int)($chapter['is_crawling'] ?? 0); ?>
            <select name="is_crawling" class="a-select">
              <option value="0" <?= $crawling === 0 ? 'selected' : '' ?>>0 &mdash; Free / Done</option>
              <option value="1" <?= $crawling === 1 ? 'selected' : '' ?>>1 &mdash; Crawling (locked)</option>
              <option value="2" <?= $crawling === 2 ? 'selected' : '' ?>>2 &mdash; Urgent</option>
            </select>
          </div>

          <?php if ($isEdit): ?>
          <div class="a-meta" style="padding-top:8px;border-top:1px solid var(--a-border)">
            <div>ID: <?= $chapter['id'] ?></div>
            <div>Pages: <?= number_format((int)($chapter['page_count'] ?? 0)) ?></div>
            <div>Views: <?= number_format((int)($chapter['view'] ?? 0)) ?></div>
            <?php if (!empty($chapter['created_at'])): ?>
            <div>Created: <?= date('M d, Y', strtotime($chapter['created_at'])) ?></div>
            <?php endif; ?>
          </div>
          <?php endif; ?>

        </div>
      </div>

      <button type="submit" class="a-btn a-btn-block a-btn-xl">
        <?= $isEdit ? 'Save Changes' : 'Create Chapter' ?>
      </button>

      <?php if ($isEdit): ?>
      <a href="/admin/manga/<?= $manga['id'] ?>/chapters"
         class="a-btn-sec a-btn-block a-btn-xl a-text-center">
        &larr; Back to Chapters
      </a>
      <?php endif; ?>

    </div>
  </div>
</form>

<?php if ($isEdit):
  $cdnChapterUrl = rtrim(env('CDN_CHAPTER_URL',''), '/');
  $pushableCount = 0;
  foreach ($pages ?? [] as $_p) {
      $isLocalUp = (empty($_p['image_local']) && ($_p['external'] ?? 0) == 0 && !empty($_p['image']) && str_starts_with($_p['image'], '/uploads/'));
      $isExtUrl  = (($_p['external'] ?? 0) == 1 && !empty($_p['image']));
      if ($isLocalUp || $isExtUrl) $pushableCount++;
  }
?>
<!-- Pages Management -->
<div style="margin-top:24px">
  <div class="a-panel">
    <div class="a-panel-head">
      <div>
        <span>Pages</span>
        <span class="a-text-xs a-txt6" id="pg-pages-count" style="margin-left:8px"><?= count($pages ?? []) ?> pages</span>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <?php if (!empty($pages)): ?>
        <button type="button" id="pg-delete-all-btn" onclick="pgDeleteAll(<?= $chapter['id'] ?>)"
                class="a-btn-danger">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
          </svg>
          Delete All
        </button>
        <?php endif; ?>
        <?php if ($pushableCount > 0): ?>
        <button type="button" id="pg-push-s3-btn" onclick="pgPushAllToS3(<?= $chapter['id'] ?>)"
                class="a-btn-sky a-btn-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/>
            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
          </svg>
          Push to S3
          <span class="a-badge a-badge-sm" style="background:rgba(14,165,233,.5);color:#fff"><?= $pushableCount ?></span>
        </button>
        <?php endif; ?>
        <button type="button" onclick="document.getElementById('pg-add-panel').classList.toggle('a-hidden')"
                class="a-btn a-btn-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Add Pages
        </button>
      </div>
    </div>

    <!-- Add pages panel -->
    <div id="pg-add-panel" class="a-hidden" style="border-bottom:1px solid var(--a-border)">
      <form method="post" action="/admin/chapters/<?= $chapter['id'] ?>/pages/add" enctype="multipart/form-data" id="pg-add-form">
        <?= csrf_field() ?>
        <div class="a-panel-body">

          <!-- Source type -->
          <div>
            <label class="a-label">Source type</label>
            <div style="display:flex;gap:12px">
              <label class="a-radio-card">
                <input type="radio" name="source_type" value="cdn" checked onchange="pgToggleSource(this.value)">
                <span>CDN S3</span>
              </label>
              <label class="a-radio-card orange">
                <input type="radio" name="source_type" value="external" onchange="pgToggleSource(this.value)">
                <span>External URL</span>
              </label>
              <label class="a-radio-card green">
                <input type="radio" name="source_type" value="local" onchange="pgToggleSource(this.value)">
                <span>Local Upload</span>
              </label>
            </div>
            <p id="pg-src-hint" class="a-hint">CDN S3: filenames (e.g. 001.jpg) &mdash; stored in image_local, served via CDN</p>
          </div>

          <!-- URLs / paths textarea (cdn + external) -->
          <div id="pg-url-block">
            <label class="a-label">Paths / URLs <span class="hint">&mdash; one per line</span></label>
            <textarea name="urls" rows="6" id="pg-add-urls"
                      placeholder="001.jpg&#10;002.jpg&#10;003.jpg"
                      class="a-textarea mono" style="resize:vertical"></textarea>
          </div>

          <!-- File upload block (local only) -->
          <div id="pg-file-block" class="a-hidden">
            <label class="a-label">Choose images <span class="hint">&mdash; JPG, PNG, WebP (multiple)</span></label>
            <input type="file" name="page_files[]" id="pg-file-input" multiple accept="image/jpeg,image/png,image/webp,image/gif"
                   class="a-file-input">
            <p id="pg-file-count" class="a-hint">No files selected</p>
          </div>

          <div style="display:flex;align-items:center;gap:16px">
            <div id="pg-start-slug-wrap" style="display:flex;align-items:center;gap:8px">
              <label class="a-label" style="margin-bottom:0;white-space:nowrap">Start slug:</label>
              <input type="number" name="start_slug" value="<?= count($pages ?? []) + 1 ?>" min="1"
                     class="a-input mono" style="width:80px">
            </div>
            <input type="hidden" name="is_cdn" id="pg-is-cdn" value="1">
            <input type="hidden" name="external" id="pg-external" value="0">
          </div>

          <div style="display:flex;gap:8px">
            <button type="submit" class="a-btn a-btn-sm">Add Pages</button>
            <button type="button" onclick="document.getElementById('pg-add-panel').classList.add('a-hidden')"
                    class="a-btn-sec a-btn-sm">Cancel</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Page grid -->
    <?php if (!empty($pages)): ?>
    <div class="a-pages-grid">
      <?php foreach ($pages as $pg):
        if (!empty($pg['image_local'])) {
            $imgSrc  = $cdnChapterUrl . '/' . $chapter['id'] . '/' . ltrim($pg['image_local'], '/');
            $srcType = 'cdn';
        } elseif (($pg['external'] ?? 0) == 1) {
            $imgSrc  = trim($pg['image'] ?? '');
            $srcType = 'external';
        } else {
            $imgSrc  = trim($pg['image'] ?? '');
            $srcType = 'local';
        }
        $srcBadge   = ['cdn'=>['badge-s3','S3'], 'external'=>['badge-ext','EXT'], 'local'=>['badge-srv','SRV']][$srcType];
        $isPushable = false;
        $pushLabel  = '';
        if ($srcType === 'local' && str_starts_with($pg['image'] ?? '', '/uploads/')) {
            $isPushable = true;
            $pushLabel  = basename($pg['image']);
        } elseif ($srcType === 'external' && !empty($pg['image'])) {
            $isPushable = true;
            $pushLabel  = basename(parse_url($pg['image'], PHP_URL_PATH) ?: $pg['image']);
        }
      ?>
      <div class="a-page-item" id="pg-item-<?= $pg['id'] ?>"
           data-pg-id="<?= $pg['id'] ?>"
           <?= $isPushable ? 'data-pg-push="1" data-pg-file="'.esc($pushLabel).'"' : '' ?>>
        <input type="checkbox" class="pg-cb"
               value="<?= $pg['id'] ?>" onchange="pgSelChange(this)">
        <div class="thumb">
          <img src="<?= esc($imgSrc) ?>" alt="p<?= $pg['slug'] ?>" loading="lazy"
               onerror="this.style.opacity='0.2'">
        </div>
        <span class="page-badge <?= $srcBadge[0] ?>"><?= $srcBadge[1] ?></span>
        <div class="page-slug"><?= $pg['slug'] ?></div>
        <div class="page-actions">
          <button type="button"
                  onclick="editPage(<?= $pg['id'] ?>,<?= (int)$pg['slug'] ?>,'<?= esc(addslashes($pg['image'] ?? '')) ?>','<?= esc(addslashes($pg['image_local'] ?? '')) ?>','<?= $srcType ?>')"
                  class="a-btn-sec a-btn-sm">Edit</button>
          <form method="post" action="/admin/pages/<?= $pg['id'] ?>/delete" style="display:inline"
                onsubmit="return confirm('Delete page <?= $pg['slug'] ?>?')">
            <?= csrf_field() ?>
            <button type="submit" class="a-btn-danger">Del</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Bulk selection bar -->
    <div id="pg-sel-bar" class="a-sel-bar a-hidden">
      <span class="a-text-xs a-txt4"><span id="pg-sel-count">0</span> selected</span>
      <button type="button" onclick="pgSelectAll()" class="a-text-xs a-txt5 a-cursor-pointer" style="background:none;border:none">Select all</button>
      <button type="button" onclick="pgDeselectAll()" class="a-text-xs a-txt5 a-cursor-pointer" style="background:none;border:none">Deselect</button>
      <button type="button" onclick="pgBulkDelete(<?= $chapter['id'] ?>)"
              class="a-btn-danger" style="margin-left:auto">
        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
        </svg>
        Delete selected
      </button>
    </div>

    <?php else: ?>
    <div class="a-empty">No pages yet. Click "Add Pages" to get started.</div>
    <?php endif; ?>
  </div>
</div>

<!-- S3 Upload Progress Panel -->
<div id="pg-s3-panel" class="a-s3-panel a-hidden">
  <div class="a-s3-panel-head">
    <span style="display:flex;align-items:center;gap:8px">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--a-sky)">
        <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/>
        <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
      </svg>
      Uploading to S3
    </span>
    <span class="a-text-xs a-txt5" id="pg-s3-counter">0 / 0</span>
  </div>
  <div class="a-s3-bar-wrap">
    <div class="a-s3-bar-bg">
      <div id="pg-s3-bar" class="a-s3-bar" style="width:0%"></div>
    </div>
  </div>
  <div class="a-s3-files" id="pg-s3-file-list"></div>
  <div class="a-hidden" id="pg-s3-done-row" style="padding:12px 16px">
    <button onclick="pgS3PanelClose()" class="a-btn a-btn-block a-btn-sm">
      Done &mdash; Reload
    </button>
  </div>
</div>

<!-- Edit page modal -->
<div id="pg-edit-modal" class="a-modal-overlay">
  <div class="a-modal">
    <div class="a-panel-head">Edit Page</div>
    <form method="post" id="pg-edit-form">
      <?= csrf_field() ?>
      <div class="a-panel-body">

        <div>
          <label class="a-label">Slug (page order)</label>
          <input type="number" name="slug" id="pg-edit-slug" min="1" class="a-input mono" style="width:120px">
        </div>

        <div>
          <label class="a-label">Source type</label>
          <div style="display:flex;gap:8px">
            <label class="a-radio-card">
              <input type="radio" name="edit_source_type" value="cdn" id="pg-edit-src-cdn" onchange="pgEditToggle('cdn')">
              <span>CDN S3</span>
            </label>
            <label class="a-radio-card orange">
              <input type="radio" name="edit_source_type" value="external" id="pg-edit-src-ext" onchange="pgEditToggle('external')">
              <span>External URL</span>
            </label>
            <label class="a-radio-card green">
              <input type="radio" name="edit_source_type" value="local" id="pg-edit-src-local" onchange="pgEditToggle('local')">
              <span>Local Server</span>
            </label>
          </div>
        </div>

        <div id="pg-edit-field-cdn">
          <label class="a-label">Filename on CDN S3 <span class="hint">(image_local)</span></label>
          <input type="text" name="image_local" id="pg-edit-local" placeholder="001.jpg" class="a-input mono">
        </div>

        <div id="pg-edit-field-image" class="a-hidden">
          <label class="a-label" id="pg-edit-image-label">URL / path</label>
          <input type="text" name="image" id="pg-edit-image" placeholder="https://... or /path/to/image.jpg" class="a-input mono">
          <input type="hidden" name="external" id="pg-edit-external" value="0">
        </div>

      </div>
      <div style="padding:0 20px 20px;display:flex;gap:8px">
        <button type="submit" class="a-btn">Save</button>
        <button type="button" onclick="closeEditModal()" class="a-btn-sec">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
(function(){
  var nameEl = document.getElementById('ch-name');
  var numEl  = document.getElementById('ch-number');
  var slugEl = document.getElementById('ch-slug');
  var userEdited = false;

  slugEl.addEventListener('input', function(){ userEdited = true; });

  function slugify(str) {
    return str.toLowerCase()
      .replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g,'a')
      .replace(/[èéẹẻẽêềếệểễ]/g,'e')
      .replace(/[ìíịỉĩ]/g,'i')
      .replace(/[òóọỏõôồốộổỗơờớợởỡ]/g,'o')
      .replace(/[ùúụủũưừứựửữ]/g,'u')
      .replace(/[ỳýỵỷỹ]/g,'y')
      .replace(/đ/g,'d')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  function autoSlug() {
    if (userEdited) return;
    var name = nameEl ? nameEl.value.trim() : '';
    var num  = numEl.value.trim();
    if (name) {
      slugEl.value = slugify(name);
    } else if (num) {
      var n = parseFloat(num);
      slugEl.value = 'chapter-' + (Number.isInteger(n) ? n : n.toString().replace('.', '-'));
    }
  }

  if (nameEl) nameEl.addEventListener('input', autoSlug);
  numEl.addEventListener('input', autoSlug);
})();

// ── Add Pages: source type toggle ──────────────────────────────
var pgSrcHints = {
  cdn:      'CDN S3: filenames (e.g. 001.jpg) stored in image_local, served via CDN',
  external: 'External URL: full URL stored in image field (external=1)',
  local:    'Local Upload: choose image files from computer, will upload to server'
};
var pgSrcPlaceholders = {
  cdn:      '001.jpg\n002.jpg\n003.jpg',
  external: 'https://site.com/img/001.jpg\nhttps://site.com/img/002.jpg',
  local:    ''
};
function pgToggleSource(type) {
  var urlBlock  = document.getElementById('pg-url-block');
  var fileBlock = document.getElementById('pg-file-block');
  var urlsTA    = document.getElementById('pg-add-urls');
  document.getElementById('pg-src-hint').textContent = pgSrcHints[type] || '';
  document.getElementById('pg-is-cdn').value      = (type === 'cdn')      ? '1' : '0';
  document.getElementById('pg-external').value    = (type === 'external') ? '1' : '0';

  if (type === 'local') {
    urlBlock.classList.add('a-hidden');
    fileBlock.classList.remove('a-hidden');
    urlsTA.required = false;
  } else {
    urlBlock.classList.remove('a-hidden');
    fileBlock.classList.add('a-hidden');
    urlsTA.required = true;
    urlsTA.placeholder = pgSrcPlaceholders[type] || '';
  }
}

// File count display
document.getElementById('pg-file-input').addEventListener('change', function() {
  var cnt = this.files.length;
  document.getElementById('pg-file-count').textContent = cnt > 0
    ? cnt + ' file(s) selected'
    : 'No files selected';
});

// ── Edit Page modal ─────────────────────────────────────────────
function pgEditToggle(type) {
  var cdnField   = document.getElementById('pg-edit-field-cdn');
  var imgField   = document.getElementById('pg-edit-field-image');
  var imgLabel   = document.getElementById('pg-edit-image-label');
  var extInput   = document.getElementById('pg-edit-external');
  if (type === 'cdn') {
    cdnField.classList.remove('a-hidden');
    imgField.classList.add('a-hidden');
  } else {
    cdnField.classList.add('a-hidden');
    imgField.classList.remove('a-hidden');
    imgLabel.textContent = type === 'external' ? 'External URL' : 'Local Server path';
    extInput.value = type === 'external' ? '1' : '0';
  }
}

function editPage(id, slug, image, imageLocal, srcType) {
  document.getElementById('pg-edit-slug').value  = slug;
  document.getElementById('pg-edit-image').value = image;
  document.getElementById('pg-edit-local').value = imageLocal;
  document.getElementById('pg-edit-form').action = '/admin/pages/' + id + '/edit';

  var radios = {cdn:'pg-edit-src-cdn', external:'pg-edit-src-ext', local:'pg-edit-src-local'};
  var radio  = document.getElementById(radios[srcType] || 'pg-edit-src-cdn');
  if (radio) radio.checked = true;
  pgEditToggle(srcType || 'cdn');

  document.getElementById('pg-edit-modal').classList.add('open');
}
function closeEditModal() {
  document.getElementById('pg-edit-modal').classList.remove('open');
}
document.getElementById('pg-edit-modal')?.addEventListener('click', function(e){
  if (e.target === this) closeEditModal();
});

// ── Page selection & bulk delete ────────────────────────────────────
function pgSelChange(cb) {
  var item = document.getElementById('pg-item-' + cb.value);
  if (cb.checked) {
    item.classList.add('selected');
  } else {
    item.classList.remove('selected');
  }
  var checked = document.querySelectorAll('.pg-cb:checked');
  var bar     = document.getElementById('pg-sel-bar');
  document.getElementById('pg-sel-count').textContent = checked.length;
  checked.length > 0 ? bar.classList.remove('a-hidden') : bar.classList.add('a-hidden');
}

function pgSelectAll() {
  document.querySelectorAll('.pg-cb').forEach(function(cb) {
    if (!cb.checked) { cb.checked = true; pgSelChange(cb); }
  });
}

function pgDeselectAll() {
  document.querySelectorAll('.pg-cb:checked').forEach(function(cb) {
    cb.checked = false; pgSelChange(cb);
  });
}

function pgUpdatePageCount(delta) {
  var el = document.querySelector('#pg-pages-count');
  if (el) el.textContent = (parseInt(el.textContent) + delta) + ' pages';
}

async function pgBulkDelete(chapterId) {
  var checked = Array.from(document.querySelectorAll('.pg-cb:checked'));
  if (!checked.length) return;
  if (!confirm('Delete ' + checked.length + ' selected pages?')) return;

  var fd = new FormData();
  checked.forEach(function(cb) { fd.append('page_ids[]', cb.value); });
  var csrf = document.querySelector('input[name^="csrf_"]');
  if (csrf) fd.append(csrf.name, csrf.value);

  var resp = await fetch('/admin/chapters/' + chapterId + '/pages/bulk-delete', { method: 'POST', body: fd });
  var json = await resp.json();
  if (json.success) {
    checked.forEach(function(cb) {
      var el = document.getElementById('pg-item-' + cb.value);
      if (el) el.remove();
    });
    pgDeselectAll();
    pgUpdatePageCount(-json.deleted);
  } else {
    alert('Error: ' + (json.error || 'unknown'));
  }
}

async function pgDeleteAll(chapterId) {
  var items = document.querySelectorAll('[id^="pg-item-"]');
  if (!items.length) return;
  if (!confirm('Delete ALL ' + items.length + ' pages? This cannot be undone!')) return;

  var fd = new FormData();
  var csrf = document.querySelector('input[name^="csrf_"]');
  if (csrf) fd.append(csrf.name, csrf.value);

  var resp = await fetch('/admin/chapters/' + chapterId + '/pages/delete-all', { method: 'POST', body: fd });
  var json = await resp.json();
  if (json.success) {
    items.forEach(function(el) { el.remove(); });
    document.getElementById('pg-sel-bar')?.classList.add('a-hidden');
    ['pg-delete-all-btn', 'pg-push-s3-btn'].forEach(function(id) {
      var btn = document.getElementById(id);
      if (btn) btn.remove();
    });
    var grid = document.querySelector('.a-pages-grid');
    if (grid) grid.outerHTML = '<div class="a-empty">No pages yet. Click "Add Pages" to get started.</div>';
    pgUpdatePageCount(-json.deleted);
  } else {
    alert('Error: ' + (json.error || 'unknown'));
  }
}

// ── Push local pages to S3 ───────────────────────────────────────────
function pgS3PanelClose() {
  document.getElementById('pg-s3-panel').classList.add('a-hidden');
  location.reload();
}

async function pgPushAllToS3(chapterId) {
  var items = Array.from(document.querySelectorAll('[data-pg-push="1"]'));
  if (!items.length) { alert('No pages to push.'); return; }

  var btn      = document.getElementById('pg-push-s3-btn');
  var panel    = document.getElementById('pg-s3-panel');
  var fileList = document.getElementById('pg-s3-file-list');
  var counter  = document.getElementById('pg-s3-counter');
  var bar      = document.getElementById('pg-s3-bar');
  var doneRow  = document.getElementById('pg-s3-done-row');

  fileList.innerHTML = '';
  bar.style.width    = '0%';
  doneRow.classList.add('a-hidden');
  panel.classList.remove('a-hidden');
  if (btn) btn.disabled = true;

  var total = items.length;
  var done  = 0;
  counter.textContent = '0 / ' + total;

  var csrfInput = document.querySelector('input[name^="csrf_"]');
  var csrfName  = csrfInput ? csrfInput.name  : '';
  var csrfVal   = csrfInput ? csrfInput.value : '';

  items.forEach(function(el) {
    var row = document.createElement('div');
    row.className = 'a-s3-row';
    row.id = 'pg-s3-row-' + el.dataset.pgId;
    row.innerHTML = '<span class="a-s3-row-file">' + el.dataset.pgFile + '</span>'
                  + '<span class="a-s3-row-status a-txt6">pending</span>';
    fileList.appendChild(row);
  });

  for (var i = 0; i < items.length; i++) {
    var el     = items[i];
    var pageId = el.dataset.pgId;
    var row    = document.getElementById('pg-s3-row-' + pageId);
    var st     = row.querySelector('.a-s3-row-status');

    st.textContent = 'uploading...';
    st.style.color = 'var(--a-yellow)';
    row.scrollIntoView({ block: 'nearest' });

    try {
      var fd = new FormData();
      fd.append('page_id', pageId);
      if (csrfName) fd.append(csrfName, csrfVal);

      var resp = await fetch('/admin/chapters/' + chapterId + '/push-pages-s3', {
        method: 'POST', body: fd
      });

      var newToken = resp.headers.get('X-CSRF-TOKEN');
      if (newToken && csrfName) {
        csrfVal = newToken;
        var inp = document.querySelector('input[name="' + csrfName + '"]');
        if (inp) inp.value = newToken;
      }

      var json = await resp.json();

      if (json.success) {
        st.textContent = 'done';
        st.style.color = 'var(--a-green)';
        var pgEl = document.getElementById('pg-item-' + pageId);
        if (pgEl) {
          var badge = pgEl.querySelector('.page-badge');
          if (badge) {
            badge.textContent = 'S3';
            badge.className   = 'page-badge badge-s3';
          }
          pgEl.removeAttribute('data-pg-push');
        }
      } else {
        st.textContent = 'error: ' + (json.error || 'unknown');
        st.style.color = 'var(--a-red)';
        st.title       = json.error || '';
      }
    } catch(e) {
      st.textContent = 'network error';
      st.style.color = 'var(--a-red)';
    }

    done++;
    counter.textContent = done + ' / ' + total;
    bar.style.width     = Math.round(done / total * 100) + '%';
  }

  if (btn) btn.disabled = false;
  doneRow.classList.remove('a-hidden');
}
</script>
