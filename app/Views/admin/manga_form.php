<?php
$isEdit  = !empty($manga);
$action  = $isEdit ? "/admin/manga/{$manga['id']}/edit" : '/admin/manga/new';
$mangaId = $isEdit ? (int)$manga['id'] : 0;
?>
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
.ql-toolbar.ql-snow{background:#1f2937;border-color:#374151;border-radius:8px 8px 0 0}
.ql-container.ql-snow{background:#1f2937;border-color:#374151;border-radius:0 0 8px 8px;font-size:13px}
.ql-editor{color:#e5e7eb;min-height:120px}
.ql-editor.ql-blank::before{color:#6b7280;font-style:normal}
.ql-snow .ql-stroke{stroke:#9ca3af}
.ql-snow .ql-fill{fill:#9ca3af}
.ql-snow .ql-picker{color:#9ca3af}
.ql-snow.ql-toolbar button:hover .ql-stroke,.ql-snow.ql-toolbar button.ql-active .ql-stroke{stroke:#e5e7eb}
.ql-snow.ql-toolbar button:hover .ql-fill,.ql-snow.ql-toolbar button.ql-active .ql-fill{fill:#e5e7eb}
.ql-snow .ql-picker-options{background:#1f2937;border-color:#374151}
</style>
<?php // re-open php tag

$statusMap = [];
foreach ($statuses as $s) $statusMap[(int)$s['id']] = $s['name'] ?? $s['label'] ?? $s['title'] ?? 'Status '.$s['id'];
$currentStatus = $isEdit ? (int)$manga['status_id'] : 1;
?>
<style>
/* Typeahead widget */
.th-wrap{position:relative}
.th-chips{min-height:38px;display:flex;flex-wrap:wrap;gap:4px;padding:4px 8px;background:#1f2937;border:1px solid #374151;border-radius:8px;cursor:text;align-items:center}
.th-chips:focus-within{border-color:#6366f1}
.th-chip{display:flex;align-items:center;gap:4px;background:#374151;color:#d1d5db;font-size:12px;padding:2px 8px;border-radius:20px;white-space:nowrap}
.th-chip-new{background:#312e81;color:#a5b4fc}
.th-chip-del{cursor:pointer;color:#9ca3af;line-height:1;font-size:14px;padding:0 2px}
.th-chip-del:hover{color:#ef4444}
.th-input{flex:1;min-width:120px;background:transparent;border:none;outline:none;color:#e5e7eb;font-size:13px;padding:2px 4px}
.th-dropdown{position:absolute;top:100%;left:0;right:0;z-index:50;background:#1f2937;border:1px solid #374151;border-radius:8px;margin-top:4px;max-height:200px;overflow-y:auto;box-shadow:0 4px 20px rgba(0,0,0,.4);display:none}
.th-dropdown.open{display:block}
.th-option{padding:8px 12px;font-size:13px;color:#d1d5db;cursor:pointer;display:flex;align-items:center;gap:8px}
.th-option:hover,.th-option.active{background:#374151}
.th-option-new{color:#a5b4fc}
.th-option-new::before{content:'+ Create ';font-weight:600}
.th-empty{padding:10px 12px;font-size:12px;color:#6b7280;text-align:center}
</style>

<!-- Back -->
<div class="mb-5">
  <a href="/admin/manga" class="a-link-back">← Back to Manga</a>
  <?php if ($isEdit): ?>
  <a href="/manga/<?= esc($manga['slug']) ?>" target="_blank" class="a-link ml-4">View on site ↗</a>
  <?php endif; ?>
</div>

<?php if ($flash = ($flash ?? null)): ?>
<div class="<?= $flash['type']==='success' ? 'a-flash a-flash-ok' : 'a-flash a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="a-grid-form">

    <!-- ── Left 2/3 ── -->
    <div class="a-space-y-5">

      <!-- Basic info -->
      <div class="a-panel">
        <div class="a-panel-head">Basic Info</div>
        <div class="a-panel-body">
          <div>
            <label class="a-label">Title <span class="req">*</span></label>
            <input type="text" name="name" id="mf-name" value="<?= esc($manga['name'] ?? '') ?>" required
                   class="a-input">
          </div>
          <div>
            <label class="a-label">Slug <span class="hint">(auto)</span></label>
            <input type="text" name="slug" id="mf-slug" value="<?= esc($manga['slug'] ?? '') ?>"
                   class="a-input mono">
          </div>
          <div>
            <label class="a-label">Other Names</label>
            <input type="text" name="otherNames" value="<?= esc($manga['otherNames'] ?? '') ?>"
                   placeholder="Alt titles separated by ;"
                   class="a-input">
          </div>
          <div>
            <label class="a-label">Crawl Sources</label>
            <textarea name="from_manga18fx" rows="2"
                      placeholder="site1.com,site2.com"
                      class="a-textarea mono"><?= esc($manga['from_manga18fx'] ?? '') ?></textarea>
            <p class="a-hint">Separate multiple sources with comma</p>
          </div>
          <div>
            <label class="a-label">Summary</label>
            <div id="mf-summary-editor" style="min-height:140px"></div>
            <textarea name="summary" id="mf-summary" style="display:none"><?= esc($manga['summary'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Authors -->
      <div class="a-panel">
        <div class="a-panel-head">Authors</div>
        <div class="a-panel-body">
          <div class="th-wrap" id="th-author">
            <div class="th-chips" id="th-author-chips" onclick="document.getElementById('th-author-input').focus()">
              <input type="text" id="th-author-input" class="th-input" placeholder="Type name, Tab to add or select…" autocomplete="off">
            </div>
            <div class="th-dropdown" id="th-author-dd"></div>
          </div>
          <input type="hidden" name="authors_data" id="th-author-data" value="<?= esc(json_encode($mangaAuthors ?? [])) ?>">
          <p class="a-hint">Press Tab to add typed name (creates new if not found in DB)</p>
        </div>
      </div>

      <!-- Artists -->
      <div class="a-panel">
        <div class="a-panel-head">Artists</div>
        <div class="a-panel-body">
          <div class="th-wrap" id="th-artists">
            <div class="th-chips" id="th-artists-chips" onclick="document.getElementById('th-artists-input').focus()">
              <input type="text" id="th-artists-input" class="th-input" placeholder="Type name, Tab to add or select…" autocomplete="off">
            </div>
            <div class="th-dropdown" id="th-artists-dd"></div>
          </div>
          <input type="hidden" name="artists_data" id="th-artists-data" value="<?= esc(json_encode($mangaArtists ?? [])) ?>">
          <p class="a-hint">Press Tab to add typed name (creates new if not found in DB)</p>
        </div>
      </div>

      <!-- Tags -->
      <div class="a-panel">
        <div class="a-panel-head">Tags</div>
        <div class="a-panel-body">
          <div class="th-wrap" id="th-tags">
            <div class="th-chips" id="th-tags-chips" onclick="document.getElementById('th-tags-input').focus()">
              <input type="text" id="th-tags-input" class="th-input" placeholder="Type tag, Tab to add or select…" autocomplete="off">
            </div>
            <div class="th-dropdown" id="th-tags-dd"></div>
          </div>
          <input type="hidden" name="tags_data" id="th-tags-data" value="<?= esc(json_encode($mangaTags ?? [])) ?>">
          <p class="a-hint">Press Tab to add typed tag (creates new if not found)</p>
        </div>
      </div>

    </div>

    <!-- ── Right 1/3 ── -->
    <div class="a-space-y-5">

      <!-- Cover Image -->
      <div class="a-panel">
        <div class="a-panel-head">Cover Image</div>
        <div class="a-panel-body compact">
          <?php
            $cdnBase    = rtrim(env('CDN_COVER_URL', ''), '/');
            $previewSrc = $isEdit ? manga_cover_url($manga, $cdnBase) : '';
          ?>
          <!-- Preview -->
          <div id="mf-cover-preview" class="<?= $previewSrc ? '' : 'a-hidden' ?>">
            <img id="mf-cover-img" src="<?= esc($previewSrc) ?><?= $previewSrc ? '?t=' . time() : '' ?>"
                 alt="Cover"
                 style="width:100%;border-radius:8px;border:1px solid var(--c-border);max-height:300px;object-fit:cover"
                 onerror="this.style.display='none'">
          </div>

          <!-- File input -->
          <input type="file" name="image_file" id="mf-cover-file" accept="image/*"
                 class="a-file-input">

          <!-- External URL input -->
          <div style="display:flex;gap:6px;margin-top:8px">
            <input type="text" id="mf-cover-ext-url" placeholder="Paste external image URL…"
                   class="a-input" style="flex:1;font-size:12px">
            <button type="button" id="mf-cover-ext-btn" class="a-btn a-btn-sm" style="white-space:nowrap">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              Fetch
            </button>
          </div>
          <p class="a-hint" style="margin-top:2px">Nhập URL ảnh ngoài rồi bấm Fetch để tải về server</p>

          <!-- Hidden field to carry existing value when no new file chosen -->
          <?php $currentImage = $manga['image'] ?? ''; ?>
          <input type="hidden" name="image_url" id="mf-cover-url" value="<?= esc($currentImage) ?>">
          <input type="hidden" name="tmp_file" id="mf-tmp-file" value="">

          <!-- CDN flag toggle -->
          <label class="a-checkbox">
            <input type="checkbox" name="cover_cdn" value="1" id="mf-cover-cdn"
                   <?= ($manga['cover'] ?? 0) == 1 ? 'checked' : '' ?>>
            <span>Ảnh đã trên CDN S3 (dùng URL CDN)</span>
          </label>

          <?php if ($isEdit): ?>
          <!-- Push to S3 button -->
          <button type="button" id="mf-push-s3"
                  class="a-btn-sky a-btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span id="mf-push-s3-text">Push ảnh lên S3</span>
          </button>
          <?php endif; ?>

          <!-- Remove -->
          <button type="button" id="mf-cover-remove"
                  class="a-txt-red a-text-xs a-cursor-pointer <?= $previewSrc ? '' : 'a-hidden' ?>">
            Remove image
          </button>
        </div>
      </div>

      <!-- Status -->
      <div class="a-panel">
        <div class="a-panel-head">Status</div>
        <div class="a-panel-body compact">
          <?php foreach ($statuses as $s): ?>
          <label class="a-radio">
            <input type="radio" name="status_id" value="<?= $s['id'] ?>" <?= $currentStatus===(int)$s['id']?'checked':'' ?>>
            <span><?= esc($s['name'] ?? $s['label'] ?? $s['title'] ?? 'Status '.$s['id']) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>


      <!-- Comic Type -->
      <?php if (!empty($comictypes)): ?>
      <div class="a-panel">
        <div class="a-panel-head">Comic Type</div>
        <div class="a-panel-body compact">
          <select name="type_id" class="a-input">
            <option value="">— None —</option>
            <?php foreach ($comictypes as $ct): ?>
            <option value="<?= $ct['id'] ?>" <?= (($manga['type_id'] ?? '') == $ct['id']) ? 'selected' : '' ?>>
              <?= esc($ct['label'] ?? $ct['name'] ?? 'Type '.$ct['id']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <?php endif; ?>

      <!-- Genres -->
      <div class="a-panel">
        <div class="a-panel-head">
          Genres
          <span class="hint"><?= count($categories) ?> total</span>
        </div>
        <div class="a-panel-body compact" style="max-height:256px;overflow-y:auto">
          <?php foreach ($categories as $cat): ?>
          <label class="a-checkbox">
            <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>"
                   <?= in_array((int)$cat['id'], $mangaCats ?? [], true) ? 'checked' : '' ?>>
            <span><?= esc($cat['name']) ?></span>
          </label>
          <?php endforeach; ?>
          <?php if (empty($categories)): ?>
          <p class="a-hint">No categories yet. <a href="/admin/categories/new" class="a-link">Add one</a></p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Settings -->
      <div class="a-panel">
        <div class="a-panel-head">Settings</div>
        <div class="a-panel-body">
          <div class="a-toggle-row">
            <div>
              <div class="a-label" style="margin-bottom:0">Public</div>
              <div class="a-hint" style="margin-top:0">Visible to all users</div>
            </div>
            <label class="a-toggle">
              <input type="checkbox" name="is_public" value="1"
                     <?= ($manga['is_public'] ?? 0) ? 'checked' : '' ?>>
              <div class="a-toggle-track"></div>
              <div class="a-toggle-thumb"></div>
            </label>
          </div>

          <div class="a-toggle-row">
            <div>
              <div class="a-label" style="margin-bottom:0">18+ / Caution</div>
              <div class="a-hint" style="margin-top:0">Mark as adult content</div>
            </div>
            <label class="a-toggle">
              <input type="checkbox" name="caution" value="1"
                     <?= ($manga['caution'] ?? 0) ? 'checked' : '' ?>>
              <div class="a-toggle-track"></div>
              <div class="a-toggle-thumb"></div>
            </label>
          </div>

          <?php if ($isEdit): ?>
          <div class="a-meta">
            <div>ID: <?= $mangaId ?></div>
            <?php if (!empty($manga['update_at'])): ?>
            <div>Updated: <?= date('M d, Y', strtotime($manga['update_at'])) ?></div>
            <?php endif; ?>
            <div>Views: <?= number_format((int)($manga['views'] ?? 0)) ?></div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <button type="submit" class="a-btn a-btn-block a-btn-xl">
        <?= $isEdit ? 'Save Changes' : 'Create Manga' ?>
      </button>

    </div>
  </div>
</form>

<script>
// ── Slug auto-gen ───────────────────────────────────────────
(function(){
  var n=document.getElementById('mf-name'), s=document.getElementById('mf-slug');
  var userEdited = false;
  s.addEventListener('input', function(){ userEdited=true; });
  n.addEventListener('input', function(){
    if(userEdited) return;
    s.value = n.value.toLowerCase().replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-').replace(/^-|-$/g,'');
  });
})();

// ── Typeahead widget factory ────────────────────────────────
function Typeahead(opts){
  var apiUrl   = opts.apiUrl;
  var inputEl  = document.getElementById(opts.inputId);
  var chipsEl  = document.getElementById(opts.chipsId);
  var ddEl     = document.getElementById(opts.ddId);
  var hiddenEl = document.getElementById(opts.hiddenId);

  var selected  = [];
  var ddItems   = [];
  var ddActive  = -1;
  var timer     = null;

  // Initialize from existing data
  try {
    var init = JSON.parse(hiddenEl.value || '[]');
    init.forEach(function(item){ if(item && item.name) addChip(item); });
  } catch(e){}

  function serialize(){
    hiddenEl.value = JSON.stringify(selected);
  }

  function addChip(item){
    // Avoid duplicates
    if(selected.some(function(s){ return s.id===item.id && s.name===item.name; })) return;
    selected.push(item);
    var chip = document.createElement('span');
    chip.className = 'th-chip' + (item.id ? '' : ' th-chip-new');
    chip.dataset.name = item.name;
    chip.innerHTML = escHtml(item.name) + '<span class="th-chip-del" title="Remove">×</span>';
    chip.querySelector('.th-chip-del').onclick = function(){
      selected = selected.filter(function(s){ return !(s.id===item.id && s.name===item.name); });
      chip.remove();
      serialize();
    };
    chipsEl.insertBefore(chip, inputEl);
    inputEl.value = '';
    serialize();
    closeDd();
  }

  function escHtml(s){
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function openDd(items, query){
    ddItems = items;
    ddActive = -1;
    ddEl.innerHTML = '';
    if(items.length===0 && !query){ ddEl.classList.remove('open'); return; }
    items.forEach(function(item, i){
      var div = document.createElement('div');
      div.className='th-option';
      div.textContent = item.name;
      div.addEventListener('mousedown', function(e){ e.preventDefault(); addChip(item); });
      div.addEventListener('mouseover', function(){ setActive(i); });
      ddEl.appendChild(div);
    });
    // "Create" option if query not found
    if(query && !items.some(function(i){ return i.name.toLowerCase()===query.toLowerCase(); })){
      var div=document.createElement('div');
      div.className='th-option th-option-new';
      div.textContent = '"'+query+'"';
      div.addEventListener('mousedown',function(e){ e.preventDefault(); addChip({id:null,name:query}); });
      ddEl.appendChild(div);
    }
    if(ddEl.children.length===0){
      ddEl.innerHTML='<div class="th-empty">No results</div>';
    }
    ddEl.classList.add('open');
  }

  function closeDd(){
    ddEl.classList.remove('open');
    ddItems = [];
    ddActive = -1;
  }

  function setActive(i){
    Array.from(ddEl.querySelectorAll('.th-option')).forEach(function(el,j){
      el.classList.toggle('active', j===i);
    });
    ddActive=i;
  }

  function search(q){
    fetch(apiUrl + '?q=' + encodeURIComponent(q))
      .then(function(r){ return r.json(); })
      .then(function(data){ openDd(data, q); })
      .catch(function(){ closeDd(); });
  }

  inputEl.addEventListener('input', function(){
    clearTimeout(timer);
    var q = inputEl.value.trim();
    if(!q){ closeDd(); return; }
    timer = setTimeout(function(){ search(q); }, 200);
  });

  inputEl.addEventListener('focus', function(){
    var q = inputEl.value.trim();
    if(q) search(q);
  });

  inputEl.addEventListener('keydown', function(e){
    var opts2 = Array.from(ddEl.querySelectorAll('.th-option'));
    if(e.key==='ArrowDown'){ e.preventDefault(); setActive(Math.min(ddActive+1, opts2.length-1)); return; }
    if(e.key==='ArrowUp'){   e.preventDefault(); setActive(Math.max(ddActive-1, 0)); return; }
    if(e.key==='Enter'){ e.preventDefault(); }
    if(e.key==='Tab' || e.key==='Enter'){
      e.preventDefault();
      if(ddActive>=0 && opts2[ddActive] && !opts2[ddActive].classList.contains('th-option-new')){
        opts2[ddActive].dispatchEvent(new MouseEvent('mousedown'));
      } else {
        var q2=inputEl.value.trim();
        if(q2){
          // Find exact match in ddItems
          var match=ddItems.find(function(i){ return i.name.toLowerCase()===q2.toLowerCase(); });
          addChip(match || {id:null,name:q2});
        }
      }
      return;
    }
    if(e.key==='Backspace' && inputEl.value==='' && selected.length>0){
      var last=selected[selected.length-1];
      var chips=chipsEl.querySelectorAll('.th-chip');
      if(chips.length) chips[chips.length-1].querySelector('.th-chip-del').click();
    }
    if(e.key==='Escape'){ closeDd(); }
  });

  document.addEventListener('click', function(e){
    if(!chipsEl.contains(e.target) && !ddEl.contains(e.target)) closeDd();
  });
}

// Init Author typeahead
Typeahead({
  apiUrl:   '/admin/api/authors',
  inputId:  'th-author-input',
  chipsId:  'th-author-chips',
  ddId:     'th-author-dd',
  hiddenId: 'th-author-data'
});

// Init Artists typeahead
Typeahead({
  apiUrl:   '/admin/api/artists',
  inputId:  'th-artists-input',
  chipsId:  'th-artists-chips',
  ddId:     'th-artists-dd',
  hiddenId: 'th-artists-data'
});

// Init Tags typeahead
Typeahead({
  apiUrl:   '/admin/api/tags',
  inputId:  'th-tags-input',
  chipsId:  'th-tags-chips',
  ddId:     'th-tags-dd',
  hiddenId: 'th-tags-data'
});
</script>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function(){
  var summaryEl = document.getElementById('mf-summary');
  var quill = new Quill('#mf-summary-editor', {
    theme: 'snow',
    placeholder: 'Enter manga summary…',
    modules: {
      toolbar: [
        ['bold','italic','underline'],
        [{'list':'ordered'},{'list':'bullet'}],
        ['link','clean']
      ]
    }
  });

  // Load existing content
  var existing = summaryEl.value.trim();
  if (existing) {
    quill.clipboard.dangerouslyPasteHTML(existing);
  }

  // Before submit, copy Quill HTML → hidden textarea
  summaryEl.closest('form').addEventListener('submit', function(){
    var html = quill.root.innerHTML;
    summaryEl.value = (html === '<p><br></p>') ? '' : html;
  });
})();

// Push to S3
(function(){
  var btn = document.getElementById('mf-push-s3');
  if (!btn) return;
  btn.addEventListener('click', function() {
    var txtEl = document.getElementById('mf-push-s3-text');
    btn.disabled = true;
    txtEl.textContent = 'Đang push...';
    btn.style.opacity = '0.7';

    var fd = new FormData();
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('/admin/manga/<?= $mangaId ?>/push-s3', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd,
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
      btn.style.opacity = '1';
      if (data.success) {
        txtEl.textContent = 'Push thành công ✓';
        btn.style.background = '#15803d';
        btn.style.cursor = 'default';
        document.getElementById('mf-cover-cdn').checked = true;
        document.getElementById('mf-cover-url').value = '';
        var img = document.getElementById('mf-cover-img');
        if (img && data.cdn_url) {
          img.src = data.cdn_url + '?t=' + Date.now();
          img.parentNode.classList.remove('a-hidden');
        }
      } else {
        txtEl.textContent = 'Lỗi: ' + (data.error || 'Unknown');
        btn.disabled = false;
        btn.style.background = '#991b1b';
      }
    })
    .catch(function(){
      btn.disabled = false;
      btn.style.opacity = '1';
      txtEl.textContent = 'Lỗi kết nối — thử lại';
    });
  });
})();
</script>

<script>
// ── Cover Image preview ──────────────────────────────────────
(function(){
  var fileInput  = document.getElementById('mf-cover-file');
  var urlInput   = document.getElementById('mf-cover-url');
  var preview    = document.getElementById('mf-cover-preview');
  var img        = document.getElementById('mf-cover-img');
  var removeBtn  = document.getElementById('mf-cover-remove');
  if(!fileInput) return;

  fileInput.addEventListener('change', function(){
    var file = this.files[0];
    if(!file) return;
    var reader = new FileReader();
    reader.onload = function(e){
      img.src = e.target.result;
      preview.classList.remove('a-hidden');
      removeBtn.classList.remove('a-hidden');
      urlInput.value = '';
    };
    reader.readAsDataURL(file);
  });

  removeBtn.addEventListener('click', function(){
    fileInput.value = '';
    urlInput.value = '';
    img.src = '';
    preview.classList.add('a-hidden');
    removeBtn.classList.add('a-hidden');
  });

  // Fetch external URL
  var extUrlInput = document.getElementById('mf-cover-ext-url');
  var extBtn = document.getElementById('mf-cover-ext-btn');
  var tmpFileInput = document.getElementById('mf-tmp-file');
  if(extBtn && extUrlInput){
    extBtn.addEventListener('click', function(){
      var url = extUrlInput.value.trim();
      if(!url){ extUrlInput.focus(); return; }
      extBtn.disabled = true;
      extBtn.textContent = 'Fetching…';
      var fd = new FormData();
      fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
      fd.append('url', url);
      fetch('/admin/manga/<?= $mangaId ?>/fetch-cover', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
      })
      .then(function(r){ return r.json(); })
      .then(function(data){
        extBtn.disabled = false;
        extBtn.textContent = 'Fetch';
        if(data.success){
          img.src = data.local_url + '?t=' + Date.now();
          img.style.display = '';
          preview.classList.remove('a-hidden');
          removeBtn.classList.remove('a-hidden');
          tmpFileInput.value = data.tmp_file;
          urlInput.value = '';
          extUrlInput.value = '';
        } else {
          alert('Lỗi: ' + (data.error || 'Unknown'));
        }
      })
      .catch(function(){
        extBtn.disabled = false;
        extBtn.textContent = 'Fetch';
        alert('Lỗi kết nối');
      });
    });
  }
})();
</script>
