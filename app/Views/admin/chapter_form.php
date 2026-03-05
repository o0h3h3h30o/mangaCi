<?php
$isEdit = !empty($chapter);
$action = $isEdit
    ? "/admin/chapters/{$chapter['id']}/edit"
    : "/admin/manga/{$manga['id']}/chapters/new";
?>

<!-- Back -->
<div class="flex items-center gap-3 mb-5">
  <a href="/admin/manga" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">← Manga</a>
  <span class="text-gray-700">/</span>
  <a href="/admin/manga/<?= $manga['id'] ?>/chapters" class="text-sm text-indigo-400 hover:underline truncate max-w-xs"><?= esc($manga['name']) ?></a>
  <span class="text-gray-700">/</span>
  <span class="text-sm text-gray-400"><?= $isEdit ? 'Edit Chapter' : 'New Chapter' ?></span>
</div>

<?php if ($flash = ($flash ?? null)): ?>
<div class="mb-5 px-4 py-3 rounded-lg text-sm <?= $flash['type']==='success' ? 'bg-green-900/40 border border-green-700 text-green-300' : 'bg-red-900/40 border border-red-700 text-red-300' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<form method="post" action="<?= $action ?>">
  <?= csrf_field() ?>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- Left -->
    <div class="lg:col-span-2 space-y-5">

      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Chapter Info</div>
        <div class="p-5 space-y-4">

          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Name <span class="text-gray-600">(optional)</span></label>
            <input type="text" name="name" id="ch-name"
                   value="<?= esc($chapter['name'] ?? '') ?>"
                   placeholder="Chapter title"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Number <span class="text-red-500">*</span></label>
              <input type="text" name="number" id="ch-number"
                     value="<?= esc($chapter['number'] ?? '') ?>"
                     required
                     placeholder="e.g. 1 or 1.5"
                     class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500 transition-colors">
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Slug <span class="text-gray-600">(auto)</span></label>
              <input type="text" name="slug" id="ch-slug"
                     value="<?= esc($chapter['slug'] ?? '') ?>"
                     placeholder="chapter-1"
                     class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500 transition-colors">
            </div>
          </div>

          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Source URL <span class="text-gray-600">(crawl source)</span></label>
            <input type="text" name="source_url"
                   value="<?= esc($chapter['source_url'] ?? '') ?>"
                   placeholder="https://source-site.com/manga/chapter-1"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500 transition-colors">
          </div>

        </div>
      </div>

    </div>

    <!-- Right -->
    <div class="space-y-5">

      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Settings</div>
        <div class="p-5 space-y-4">

          <label class="flex items-center justify-between cursor-pointer">
            <div>
              <div class="text-sm text-gray-200">Visible</div>
              <div class="text-xs text-gray-600">Show to readers</div>
            </div>
            <div class="relative">
              <input type="checkbox" name="is_show" value="1" id="ch-show"
                     <?= ($chapter['is_show'] ?? 1) ? 'checked' : '' ?>
                     class="sr-only peer">
              <div class="w-10 h-6 bg-gray-700 peer-checked:bg-indigo-600 rounded-full transition-colors cursor-pointer" onclick="document.getElementById('ch-show').click()"></div>
              <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4 pointer-events-none"></div>
            </div>
          </label>

          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Crawl Status</label>
            <?php $crawling = (int)($chapter['is_crawling'] ?? 0); ?>
            <select name="is_crawling"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors">
              <option value="0" <?= $crawling === 0 ? 'selected' : '' ?>>0 — Free / Done</option>
              <option value="1" <?= $crawling === 1 ? 'selected' : '' ?>>1 — Crawling (locked)</option>
              <option value="2" <?= $crawling === 2 ? 'selected' : '' ?>>2 — Urgent</option>
            </select>
          </div>

          <?php if ($isEdit): ?>
          <div class="text-xs text-gray-600 space-y-0.5 pt-2 border-t border-gray-800">
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

      <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 rounded-xl transition-colors text-sm">
        <?= $isEdit ? 'Save Changes' : 'Create Chapter' ?>
      </button>

      <?php if ($isEdit): ?>
      <a href="/admin/manga/<?= $manga['id'] ?>/chapters"
         class="w-full bg-gray-800 hover:bg-gray-700 text-gray-400 font-medium py-2.5 rounded-xl transition-colors text-sm text-center block">
        ← Back to Chapters
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
<!-- ── Pages Management ───────────────────────────────────────── -->
<div class="mt-6">
  <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-800 flex items-center justify-between">
      <div>
        <span class="text-sm font-semibold text-gray-300">Pages</span>
        <span class="ml-2 text-xs text-gray-600" id="pg-pages-count"><?= count($pages ?? []) ?> pages</span>
      </div>
      <div class="flex items-center gap-2">
        <?php if (!empty($pages)): ?>
        <button type="button" id="pg-delete-all-btn" onclick="pgDeleteAll(<?= $chapter['id'] ?>)"
                class="bg-red-900/50 hover:bg-red-700 text-red-400 hover:text-white text-xs px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
          </svg>
          Delete All
        </button>
        <?php endif; ?>
        <?php if ($pushableCount > 0): ?>
        <button type="button" id="pg-push-s3-btn" onclick="pgPushAllToS3(<?= $chapter['id'] ?>)"
                class="bg-sky-700 hover:bg-sky-600 text-white text-xs px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/>
            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
          </svg>
          Push to S3
          <span class="bg-sky-500 text-white rounded-full px-1.5 text-[9px] font-bold"><?= $pushableCount ?></span>
        </button>
        <?php endif; ?>
        <button type="button" onclick="document.getElementById('pg-add-panel').classList.toggle('hidden')"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Add Pages
        </button>
      </div>
    </div>

    <!-- Add pages panel -->
    <div id="pg-add-panel" class="hidden border-b border-gray-800">
      <form method="post" action="/admin/chapters/<?= $chapter['id'] ?>/pages/add" enctype="multipart/form-data" id="pg-add-form">
        <?= csrf_field() ?>
        <div class="p-5 space-y-4">

          <!-- Source type -->
          <div>
            <label class="block text-xs text-gray-500 mb-2">Source type</label>
            <div class="flex gap-3">
              <label class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-lg border border-gray-700 hover:border-indigo-500 transition-colors has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-900/20">
                <input type="radio" name="source_type" value="cdn" class="accent-indigo-500" checked onchange="pgToggleSource(this.value)">
                <span class="text-xs text-gray-300">CDN S3</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-lg border border-gray-700 hover:border-orange-500 transition-colors has-[:checked]:border-orange-500 has-[:checked]:bg-orange-900/20">
                <input type="radio" name="source_type" value="external" class="accent-orange-500" onchange="pgToggleSource(this.value)">
                <span class="text-xs text-gray-300">External URL</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-lg border border-gray-700 hover:border-green-500 transition-colors has-[:checked]:border-green-500 has-[:checked]:bg-green-900/20">
                <input type="radio" name="source_type" value="local" class="accent-green-500" onchange="pgToggleSource(this.value)">
                <span class="text-xs text-gray-300">Local Upload</span>
              </label>
            </div>
            <p id="pg-src-hint" class="text-xs text-gray-600 mt-1.5">CDN S3: filenames (e.g. 001.jpg) — stored in image_local, served via CDN</p>
          </div>

          <!-- URLs / paths textarea (cdn + external) -->
          <div id="pg-url-block">
            <label class="block text-xs text-gray-500 mb-1.5">Paths / URLs <span class="text-gray-600">— one per line</span></label>
            <textarea name="urls" rows="6" id="pg-add-urls"
                      placeholder="001.jpg&#10;002.jpg&#10;003.jpg"
                      class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500 transition-colors resize-y"></textarea>
          </div>

          <!-- File upload block (local only) -->
          <div id="pg-file-block" class="hidden">
            <label class="block text-xs text-gray-500 mb-1.5">Chọn ảnh <span class="text-gray-600">— hỗ trợ JPG, PNG, WebP (nhiều file)</span></label>
            <div class="relative">
              <input type="file" name="page_files[]" id="pg-file-input" multiple accept="image/jpeg,image/png,image/webp,image/gif"
                     class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
            </div>
            <p id="pg-file-count" class="text-xs text-gray-600 mt-1.5">Chưa chọn file nào</p>
          </div>

          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2" id="pg-start-slug-wrap">
              <label class="text-xs text-gray-500 whitespace-nowrap">Start slug:</label>
              <input type="number" name="start_slug" value="<?= count($pages ?? []) + 1 ?>" min="1"
                     class="w-20 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500">
            </div>
            <!-- hidden fields to pass source type to controller -->
            <input type="hidden" name="is_cdn" id="pg-is-cdn" value="1">
            <input type="hidden" name="external" id="pg-external" value="0">
          </div>

          <div class="flex gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg transition-colors">Add Pages</button>
            <button type="button" onclick="document.getElementById('pg-add-panel').classList.add('hidden')"
                    class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm px-4 py-2 rounded-lg transition-colors">Cancel</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Page grid -->
    <?php if (!empty($pages)): ?>
    <div class="p-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
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
        $srcBadge   = ['cdn'=>['bg-blue-900/70 text-blue-300','S3'], 'external'=>['bg-orange-900/70 text-orange-300','EXT'], 'local'=>['bg-gray-700 text-gray-400','SRV']][$srcType];
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
      <div class="group relative" id="pg-item-<?= $pg['id'] ?>"
           data-pg-id="<?= $pg['id'] ?>"
           <?= $isPushable ? 'data-pg-push="1" data-pg-file="'.esc($pushLabel).'"' : '' ?>>
        <!-- Checkbox select -->
        <input type="checkbox" class="pg-cb absolute top-1 right-1 z-10 w-3.5 h-3.5 accent-indigo-500 cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity rounded"
               value="<?= $pg['id'] ?>" onchange="pgSelChange(this)">
        <div class="aspect-[2/3] bg-gray-800 rounded overflow-hidden">
          <img src="<?= esc($imgSrc) ?>" alt="p<?= $pg['slug'] ?>" loading="lazy"
               class="w-full h-full object-cover"
               onerror="this.style.opacity='0.2'">
        </div>
        <!-- badge -->
        <span class="absolute top-1 left-1 text-[9px] font-bold px-1 py-0.5 rounded <?= $srcBadge[0] ?>"><?= $srcBadge[1] ?></span>
        <!-- slug -->
        <div class="text-center text-xs text-gray-500 mt-0.5"><?= $pg['slug'] ?></div>
        <!-- hover actions -->
        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity rounded flex items-end justify-center gap-1 pb-6">
          <button type="button"
                  onclick="editPage(<?= $pg['id'] ?>,<?= (int)$pg['slug'] ?>,'<?= esc(addslashes($pg['image'] ?? '')) ?>','<?= esc(addslashes($pg['image_local'] ?? '')) ?>','<?= $srcType ?>')"
                  class="bg-gray-700 hover:bg-gray-600 text-white text-xs px-2 py-1 rounded transition-colors">Edit</button>
          <form method="post" action="/admin/pages/<?= $pg['id'] ?>/delete" class="inline"
                onsubmit="return confirm('Delete page <?= $pg['slug'] ?>?')">
            <?= csrf_field() ?>
            <button type="submit" class="bg-red-700 hover:bg-red-600 text-white text-xs px-2 py-1 rounded transition-colors">Del</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Bulk selection bar -->
    <div id="pg-sel-bar" class="hidden px-4 py-2.5 border-t border-gray-800 flex items-center gap-3 flex-wrap">
      <span class="text-xs text-gray-400"><span id="pg-sel-count">0</span> selected</span>
      <button type="button" onclick="pgSelectAll()" class="text-xs text-gray-500 hover:text-gray-200 transition-colors">Select all</button>
      <button type="button" onclick="pgDeselectAll()" class="text-xs text-gray-500 hover:text-gray-200 transition-colors">Deselect</button>
      <button type="button" onclick="pgBulkDelete(<?= $chapter['id'] ?>)"
              class="ml-auto bg-red-700 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
        </svg>
        Delete selected
      </button>
    </div>

    <?php else: ?>
    <div class="px-5 py-10 text-center text-gray-600 text-sm">No pages yet. Click "Add Pages" to get started.</div>
    <?php endif; ?>
  </div>
</div>

<!-- ── S3 Upload Progress Panel ──────────────────────────────────────── -->
<div id="pg-s3-panel" class="fixed bottom-4 right-4 z-50 hidden w-80 bg-gray-900 border border-gray-700 rounded-xl shadow-2xl overflow-hidden">
  <div class="px-4 py-3 border-b border-gray-800 flex items-center justify-between">
    <span class="text-sm font-semibold text-gray-200 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400">
        <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/>
        <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
      </svg>
      Uploading to S3
    </span>
    <span class="text-xs text-gray-500" id="pg-s3-counter">0 / 0</span>
  </div>
  <!-- Progress bar -->
  <div class="px-4 pt-2 pb-1">
    <div class="bg-gray-800 rounded-full h-1.5 overflow-hidden">
      <div id="pg-s3-bar" class="h-full bg-sky-500 transition-all duration-300" style="width:0%"></div>
    </div>
  </div>
  <!-- File list -->
  <div class="overflow-y-auto max-h-56" id="pg-s3-file-list"></div>
  <!-- Done button -->
  <div class="px-4 py-3 hidden" id="pg-s3-done-row">
    <button onclick="pgS3PanelClose()"
            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white text-xs py-2 rounded-lg transition-colors">
      Done — Reload
    </button>
  </div>
</div>

<!-- Edit page modal -->
<div id="pg-edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70">
  <div class="bg-gray-900 border border-gray-800 rounded-xl w-full max-w-lg mx-4">
    <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Edit Page</div>
    <form method="post" id="pg-edit-form">
      <?= csrf_field() ?>
      <div class="p-5 space-y-4">

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Slug (page order)</label>
            <input type="number" name="slug" id="pg-edit-slug" min="1"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500">
          </div>
        </div>

        <!-- Source type -->
        <div>
          <label class="block text-xs text-gray-500 mb-2">Source type</label>
          <div class="flex gap-2">
            <label class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-lg border border-gray-700 hover:border-indigo-500 transition-colors has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-900/20">
              <input type="radio" name="edit_source_type" value="cdn" id="pg-edit-src-cdn" class="accent-indigo-500" onchange="pgEditToggle('cdn')">
              <span class="text-xs text-gray-300">CDN S3</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-lg border border-gray-700 hover:border-orange-500 transition-colors has-[:checked]:border-orange-500 has-[:checked]:bg-orange-900/20">
              <input type="radio" name="edit_source_type" value="external" id="pg-edit-src-ext" class="accent-orange-500" onchange="pgEditToggle('external')">
              <span class="text-xs text-gray-300">External URL</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-lg border border-gray-700 hover:border-gray-400 transition-colors has-[:checked]:border-gray-400 has-[:checked]:bg-gray-800">
              <input type="radio" name="edit_source_type" value="local" id="pg-edit-src-local" class="accent-gray-400" onchange="pgEditToggle('local')">
              <span class="text-xs text-gray-300">Local Server</span>
            </label>
          </div>
        </div>

        <!-- CDN S3 field -->
        <div id="pg-edit-field-cdn">
          <label class="block text-xs text-gray-500 mb-1.5">Filename on CDN S3 <span class="text-gray-600">(image_local)</span></label>
          <input type="text" name="image_local" id="pg-edit-local" placeholder="001.jpg"
                 class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500">
        </div>

        <!-- External URL / Local Server field -->
        <div id="pg-edit-field-image" class="hidden">
          <label class="block text-xs text-gray-500 mb-1.5" id="pg-edit-image-label">URL / path</label>
          <input type="text" name="image" id="pg-edit-image" placeholder="https://... or /path/to/image.jpg"
                 class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500">
          <input type="hidden" name="external" id="pg-edit-external" value="0">
        </div>

      </div>
      <div class="px-5 pb-5 flex gap-2">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-5 py-2.5 rounded-lg transition-colors">Save</button>
        <button type="button" onclick="closeEditModal()"
                class="bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm px-5 py-2.5 rounded-lg transition-colors">Cancel</button>
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
  local:    'Local Upload: chọn file ảnh từ máy tính, sẽ lưu lên server'
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
    urlBlock.classList.add('hidden');
    fileBlock.classList.remove('hidden');
    urlsTA.required = false;
  } else {
    urlBlock.classList.remove('hidden');
    fileBlock.classList.add('hidden');
    urlsTA.required = true;
    urlsTA.placeholder = pgSrcPlaceholders[type] || '';
  }
}

// hiển thị số file đã chọn
document.getElementById('pg-file-input').addEventListener('change', function() {
  var cnt = this.files.length;
  document.getElementById('pg-file-count').textContent = cnt > 0
    ? cnt + ' file đã chọn'
    : 'Chưa chọn file nào';
});

// ── Edit Page modal ─────────────────────────────────────────────
function pgEditToggle(type) {
  var cdnField   = document.getElementById('pg-edit-field-cdn');
  var imgField   = document.getElementById('pg-edit-field-image');
  var imgLabel   = document.getElementById('pg-edit-image-label');
  var extInput   = document.getElementById('pg-edit-external');
  if (type === 'cdn') {
    cdnField.classList.remove('hidden');
    imgField.classList.add('hidden');
  } else {
    cdnField.classList.add('hidden');
    imgField.classList.remove('hidden');
    imgLabel.textContent = type === 'external' ? 'External URL' : 'Local Server path';
    extInput.value = type === 'external' ? '1' : '0';
  }
}

function editPage(id, slug, image, imageLocal, srcType) {
  document.getElementById('pg-edit-slug').value  = slug;
  document.getElementById('pg-edit-image').value = image;
  document.getElementById('pg-edit-local').value = imageLocal;
  document.getElementById('pg-edit-form').action = '/admin/pages/' + id + '/edit';

  // Set radio + toggle fields
  var radios = {cdn:'pg-edit-src-cdn', external:'pg-edit-src-ext', local:'pg-edit-src-local'};
  var radio  = document.getElementById(radios[srcType] || 'pg-edit-src-cdn');
  if (radio) radio.checked = true;
  pgEditToggle(srcType || 'cdn');

  var modal = document.getElementById('pg-edit-modal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}
function closeEditModal() {
  var modal = document.getElementById('pg-edit-modal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}
document.getElementById('pg-edit-modal')?.addEventListener('click', function(e){
  if (e.target === this) closeEditModal();
});

// ── Page selection & bulk delete ────────────────────────────────────
function pgSelChange(cb) {
  var item = document.getElementById('pg-item-' + cb.value);
  if (cb.checked) {
    item.classList.add('ring-2', 'ring-inset', 'ring-indigo-500', 'rounded');
    cb.classList.add('opacity-100');
  } else {
    item.classList.remove('ring-2', 'ring-inset', 'ring-indigo-500', 'rounded');
    cb.classList.remove('opacity-100');
  }
  var checked = document.querySelectorAll('.pg-cb:checked');
  var bar     = document.getElementById('pg-sel-bar');
  document.getElementById('pg-sel-count').textContent = checked.length;
  checked.length > 0 ? bar.classList.remove('hidden') : bar.classList.add('hidden');
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
  if (!confirm('Xoá ' + checked.length + ' page đã chọn?')) return;

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
    alert('Lỗi: ' + (json.error || 'unknown'));
  }
}

async function pgDeleteAll(chapterId) {
  var items = document.querySelectorAll('[id^="pg-item-"]');
  if (!items.length) return;
  if (!confirm('Xoá TẤT CẢ ' + items.length + ' pages? Không thể hoàn tác!')) return;

  var fd = new FormData();
  var csrf = document.querySelector('input[name^="csrf_"]');
  if (csrf) fd.append(csrf.name, csrf.value);

  var resp = await fetch('/admin/chapters/' + chapterId + '/pages/delete-all', { method: 'POST', body: fd });
  var json = await resp.json();
  if (json.success) {
    items.forEach(function(el) { el.remove(); });
    document.getElementById('pg-sel-bar')?.classList.add('hidden');
    // Ẩn các button không còn cần thiết
    ['pg-delete-all-btn', 'pg-push-s3-btn'].forEach(function(id) {
      var btn = document.getElementById(id);
      if (btn) btn.remove();
    });
    // Hiển thị empty state
    var grid = document.querySelector('.p-4.grid');
    if (grid) grid.outerHTML = '<div class="px-5 py-10 text-center text-gray-600 text-sm">No pages yet. Click "Add Pages" to get started.</div>';
    pgUpdatePageCount(-json.deleted);
  } else {
    alert('Lỗi: ' + (json.error || 'unknown'));
  }
}

// ── Push local pages to S3 ───────────────────────────────────────────
function pgS3PanelClose() {
  document.getElementById('pg-s3-panel').classList.add('hidden');
  location.reload();
}

async function pgPushAllToS3(chapterId) {
  var items = Array.from(document.querySelectorAll('[data-pg-push="1"]'));
  if (!items.length) { alert('Không có page nào để push.'); return; }

  var btn      = document.getElementById('pg-push-s3-btn');
  var panel    = document.getElementById('pg-s3-panel');
  var fileList = document.getElementById('pg-s3-file-list');
  var counter  = document.getElementById('pg-s3-counter');
  var bar      = document.getElementById('pg-s3-bar');
  var doneRow  = document.getElementById('pg-s3-done-row');

  // Reset panel
  fileList.innerHTML = '';
  bar.style.width    = '0%';
  doneRow.classList.add('hidden');
  panel.classList.remove('hidden');
  if (btn) btn.disabled = true;

  var total = items.length;
  var done  = 0;
  counter.textContent = '0 / ' + total;

  // Lấy CSRF token name + value
  var csrfInput = document.querySelector('input[name^="csrf_"]');
  var csrfName  = csrfInput ? csrfInput.name  : '';
  var csrfVal   = csrfInput ? csrfInput.value : '';

  // Build danh sách rows
  items.forEach(function(el) {
    var row = document.createElement('div');
    row.className = 'flex items-center gap-2 px-4 py-2 border-b border-gray-800/60 text-xs';
    row.id = 'pg-s3-row-' + el.dataset.pgId;
    row.innerHTML = '<span class="flex-1 text-gray-400 truncate font-mono">' + el.dataset.pgFile + '</span>'
                  + '<span class="pg-s3-st whitespace-nowrap text-gray-600">pending</span>';
    fileList.appendChild(row);
  });

  // Upload từng file
  for (var i = 0; i < items.length; i++) {
    var el     = items[i];
    var pageId = el.dataset.pgId;
    var row    = document.getElementById('pg-s3-row-' + pageId);
    var st     = row.querySelector('.pg-s3-st');

    st.textContent = '⬆ uploading…';
    st.className   = 'pg-s3-st whitespace-nowrap text-yellow-400';

    // Scroll row vào view
    row.scrollIntoView({ block: 'nearest' });

    try {
      var fd = new FormData();
      fd.append('page_id', pageId);
      if (csrfName) fd.append(csrfName, csrfVal);

      var resp = await fetch('/admin/chapters/' + chapterId + '/push-pages-s3', {
        method: 'POST', body: fd
      });

      // Cập nhật CSRF token từ response header (CI4 rotate)
      var newToken = resp.headers.get('X-CSRF-TOKEN');
      if (newToken && csrfName) {
        csrfVal = newToken;
        var inp = document.querySelector('input[name="' + csrfName + '"]');
        if (inp) inp.value = newToken;
      }

      var json = await resp.json();

      if (json.success) {
        st.textContent = '✓ done';
        st.className   = 'pg-s3-st whitespace-nowrap text-green-400';
        // Cập nhật badge trên thumbnail từ SRV → S3
        var pgEl = document.getElementById('pg-item-' + pageId);
        if (pgEl) {
          var badge = pgEl.querySelector('span.absolute');
          if (badge) {
            badge.textContent = 'S3';
            badge.className   = badge.className
              .replace('bg-gray-700', 'bg-blue-900/70')
              .replace('text-gray-400', 'text-blue-300');
          }
          pgEl.removeAttribute('data-pg-push');
        }
      } else {
        st.textContent = '✗ ' + (json.error || 'error');
        st.className   = 'pg-s3-st whitespace-nowrap text-red-400 max-w-[140px] truncate';
        st.title       = json.error || '';
      }
    } catch(e) {
      st.textContent = '✗ network error';
      st.className   = 'pg-s3-st whitespace-nowrap text-red-400';
    }

    done++;
    counter.textContent = done + ' / ' + total;
    bar.style.width     = Math.round(done / total * 100) + '%';
  }

  if (btn) btn.disabled = false;
  doneRow.classList.remove('hidden');
}
</script>
