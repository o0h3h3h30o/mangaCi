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
  <a href="/admin/manga" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">← Back to Manga</a>
  <?php if ($isEdit): ?>
  <a href="/manga/<?= esc($manga['slug']) ?>" target="_blank" class="ml-4 text-sm text-indigo-400 hover:underline">View on site ↗</a>
  <?php endif; ?>
</div>

<?php if ($flash = ($flash ?? null)): ?>
<div class="mb-5 px-4 py-3 rounded-lg text-sm <?= $flash['type']==='success' ? 'bg-green-900/40 border border-green-700 text-green-300' : 'bg-red-900/40 border border-red-700 text-red-300' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- ── Left 2/3 ── -->
    <div class="lg:col-span-2 space-y-5">

      <!-- Basic info -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Basic Info</div>
        <div class="p-5 space-y-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Title <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="mf-name" value="<?= esc($manga['name'] ?? '') ?>" required
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Slug <span class="text-gray-600">(auto)</span></label>
            <input type="text" name="slug" id="mf-slug" value="<?= esc($manga['slug'] ?? '') ?>"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500 transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Other Names</label>
            <input type="text" name="otherNames" value="<?= esc($manga['otherNames'] ?? '') ?>"
                   placeholder="Alt titles separated by ;"
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-indigo-500 transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Crawl Sources</label>
            <textarea name="from_manga18fx" rows="2"
                      placeholder="site1.com,site2.com"
                      class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-200 font-mono focus:outline-none focus:border-indigo-500 transition-colors resize-none"><?= esc($manga['from_manga18fx'] ?? '') ?></textarea>
            <p class="text-xs text-gray-600 mt-1">Separate multiple sources with comma</p>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Summary</label>
            <div id="mf-summary-editor" style="min-height:140px"></div>
            <textarea name="summary" id="mf-summary" class="hidden"><?= esc($manga['summary'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Authors -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Authors</div>
        <div class="p-5">
          <div class="th-wrap" id="th-author">
            <div class="th-chips" id="th-author-chips" onclick="document.getElementById('th-author-input').focus()">
              <input type="text" id="th-author-input" class="th-input" placeholder="Type name, Tab to add or select…" autocomplete="off">
            </div>
            <div class="th-dropdown" id="th-author-dd"></div>
          </div>
          <input type="hidden" name="authors_data" id="th-author-data" value="<?= esc(json_encode($mangaAuthors ?? [])) ?>">
          <p class="text-xs text-gray-600 mt-1.5">Press Tab to add typed name (creates new if not found in DB)</p>
        </div>
      </div>

      <!-- Artists -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Artists</div>
        <div class="p-5">
          <div class="th-wrap" id="th-artists">
            <div class="th-chips" id="th-artists-chips" onclick="document.getElementById('th-artists-input').focus()">
              <input type="text" id="th-artists-input" class="th-input" placeholder="Type name, Tab to add or select…" autocomplete="off">
            </div>
            <div class="th-dropdown" id="th-artists-dd"></div>
          </div>
          <input type="hidden" name="artists_data" id="th-artists-data" value="<?= esc(json_encode($mangaArtists ?? [])) ?>">
          <p class="text-xs text-gray-600 mt-1.5">Press Tab to add typed name (creates new if not found in DB)</p>
        </div>
      </div>

      <!-- Tags -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Tags</div>
        <div class="p-5">
          <div class="th-wrap" id="th-tags">
            <div class="th-chips" id="th-tags-chips" onclick="document.getElementById('th-tags-input').focus()">
              <input type="text" id="th-tags-input" class="th-input" placeholder="Type tag, Tab to add or select…" autocomplete="off">
            </div>
            <div class="th-dropdown" id="th-tags-dd"></div>
          </div>
          <input type="hidden" name="tags_data" id="th-tags-data" value="<?= esc(json_encode($mangaTags ?? [])) ?>">
          <p class="text-xs text-gray-600 mt-1.5">Press Tab to add typed tag (creates new if not found)</p>
        </div>
      </div>

    </div>

    <!-- ── Right 1/3 ── -->
    <div class="space-y-5">

      <!-- Cover Image -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Cover Image</div>
        <div class="p-4 space-y-3">
          <?php
            $cdnBase    = rtrim(env('CDN_COVER_URL', ''), '/');
            $previewSrc = $isEdit ? manga_cover_url($manga, $cdnBase) : '';
          ?>
          <!-- Preview -->
          <div id="mf-cover-preview" class="<?= $previewSrc ? '' : 'hidden' ?>">
            <img id="mf-cover-img" src="<?= esc($previewSrc) ?>"
                 alt="Cover"
                 class="w-full rounded-lg border border-gray-700"
                 style="max-height:300px;object-fit:cover"
                 onerror="this.parentNode.classList.add('hidden')">
          </div>

          <!-- File input -->
          <input type="file" name="image_file" id="mf-cover-file" accept="image/*"
                 class="w-full text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600 file:cursor-pointer cursor-pointer">

          <!-- Hidden field to carry existing value when no new file chosen -->
          <?php $currentImage = $manga['image'] ?? ''; ?>
          <input type="hidden" name="image_url" id="mf-cover-url" value="<?= esc($currentImage) ?>">

          <!-- CDN flag toggle -->
          <label class="flex items-center gap-2 cursor-pointer mt-1">
            <input type="checkbox" name="cover_cdn" value="1" id="mf-cover-cdn"
                   class="w-4 h-4 accent-indigo-500"
                   <?= ($manga['cover'] ?? 0) == 1 ? 'checked' : '' ?>>
            <span class="text-xs text-gray-400">Ảnh đã trên CDN S3 (dùng URL CDN)</span>
          </label>

          <?php if ($isEdit): ?>
          <!-- Push to S3 button -->
          <button type="button" id="mf-push-s3"
                  class="w-full flex items-center justify-center gap-1.5 bg-sky-700/80 hover:bg-sky-600 text-white text-xs font-medium py-1.5 px-3 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span id="mf-push-s3-text">Push ảnh lên S3</span>
          </button>
          <?php endif; ?>

          <!-- Remove -->
          <button type="button" id="mf-cover-remove"
                  class="text-xs text-red-400 hover:text-red-300 transition-colors <?= $previewSrc ? '' : 'hidden' ?>">
            Remove image
          </button>
        </div>
      </div>

      <!-- Status -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Status</div>
        <div class="p-4 space-y-2">
          <?php foreach ($statuses as $s): ?>
          <label class="flex items-center gap-3 cursor-pointer px-1 py-1 rounded hover:bg-gray-800/40">
            <input type="radio" name="status_id" value="<?= $s['id'] ?>" <?= $currentStatus===(int)$s['id']?'checked':'' ?> class="w-4 h-4 accent-indigo-500">
            <span class="text-sm text-gray-300"><?= esc($s['name'] ?? $s['label'] ?? $s['title'] ?? 'Status '.$s['id']) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>


      <!-- Genres -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">
          Genres
          <span class="ml-1 text-xs font-normal text-gray-600"><?= count($categories) ?> total</span>
        </div>
        <div class="p-4 max-h-64 overflow-y-auto space-y-1">
          <?php foreach ($categories as $cat): ?>
          <label class="flex items-center gap-3 cursor-pointer px-1 py-1 rounded hover:bg-gray-800/40">
            <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>"
                   <?= in_array((int)$cat['id'], $mangaCats ?? [], true) ? 'checked' : '' ?>
                   class="w-4 h-4 accent-indigo-500 rounded">
            <span class="text-sm text-gray-300"><?= esc($cat['name']) ?></span>
          </label>
          <?php endforeach; ?>
          <?php if (empty($categories)): ?>
          <p class="text-xs text-gray-600">No categories yet. <a href="/admin/categories/new" class="text-indigo-400 hover:underline">Add one</a></p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Settings -->
      <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800 text-sm font-semibold text-gray-300">Settings</div>
        <div class="p-5 space-y-4">
          <label class="flex items-center justify-between cursor-pointer">
            <div>
              <div class="text-sm text-gray-200">Public</div>
              <div class="text-xs text-gray-600">Visible to all users</div>
            </div>
            <div class="relative">
              <input type="checkbox" name="is_public" value="1" id="mf-public"
                     <?= ($manga['is_public'] ?? 0) ? 'checked' : '' ?>
                     class="sr-only peer">
              <div class="w-10 h-6 bg-gray-700 peer-checked:bg-indigo-600 rounded-full transition-colors cursor-pointer" onclick="document.getElementById('mf-public').click()"></div>
              <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4 pointer-events-none"></div>
            </div>
          </label>

          <label class="flex items-center justify-between cursor-pointer">
            <div>
              <div class="text-sm text-gray-200">18+ / Caution</div>
              <div class="text-xs text-gray-600">Mark as adult content</div>
            </div>
            <div class="relative">
              <input type="checkbox" name="caution" value="1" id="mf-caution"
                     <?= ($manga['caution'] ?? 0) ? 'checked' : '' ?>
                     class="sr-only peer">
              <div class="w-10 h-6 bg-gray-700 peer-checked:bg-red-600 rounded-full transition-colors cursor-pointer" onclick="document.getElementById('mf-caution').click()"></div>
              <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4 pointer-events-none"></div>
            </div>
          </label>

          <?php if ($isEdit): ?>
          <div class="text-xs text-gray-600 space-y-0.5 pt-2 border-t border-gray-800">
            <div>ID: <?= $mangaId ?></div>
            <?php if (!empty($manga['update_at'])): ?>
            <div>Updated: <?= date('M d, Y', strtotime($manga['update_at'])) ?></div>
            <?php endif; ?>
            <div>Views: <?= number_format((int)($manga['views'] ?? 0)) ?></div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 rounded-xl transition-colors text-sm">
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
    btn.classList.add('opacity-70');

    var fd = new FormData();
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('/admin/manga/<?= $mangaId ?>/push-s3', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd,
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
      btn.classList.remove('opacity-70');
      if (data.success) {
        txtEl.textContent = 'Push thành công ✓';
        btn.classList.remove('bg-sky-700/80','hover:bg-sky-600');
        btn.classList.add('bg-green-700','cursor-default');
        document.getElementById('mf-cover-cdn').checked = true;
        document.getElementById('mf-cover-url').value = '';
        // Cập nhật preview sang CDN URL
        var img = document.getElementById('mf-cover-img');
        if (img && data.cdn_url) {
          img.src = data.cdn_url + '?t=' + Date.now();
          img.parentNode.classList.remove('hidden');
        }
      } else {
        txtEl.textContent = 'Lỗi: ' + (data.error || 'Unknown');
        btn.disabled = false;
        btn.classList.add('bg-red-800');
        btn.classList.remove('bg-sky-700/80');
      }
    })
    .catch(function(){
      btn.disabled = false;
      btn.classList.remove('opacity-70');
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
      preview.classList.remove('hidden');
      removeBtn.classList.remove('hidden');
      urlInput.value = '';
    };
    reader.readAsDataURL(file);
  });

  removeBtn.addEventListener('click', function(){
    fileInput.value = '';
    urlInput.value = '';
    img.src = '';
    preview.classList.add('hidden');
    removeBtn.classList.add('hidden');
  });
})();
</script>
