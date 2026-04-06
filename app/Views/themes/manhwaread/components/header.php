  <!-- Top Navbar -->
  <header class="top-navbar">
    <div class="top-navbar-left">
      <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>
      <!-- Mobile: logo next to hamburger -->
      <a href="/" class="mobile-logo">
        <?php $_logo = site_setting('site_logo'); ?>
        <?php if ($_logo): ?>
        <img src="<?= esc($_logo) ?>" alt="<?= esc(site_setting('site_title', 'ManhwaRead')) ?>" style="height:32px">
        <?php else: ?>
        <div class="logo-icon"><i class="fas fa-circle-notch"></i></div>
        <?php endif; ?>
      </a>
      <div class="top-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="<?= lang('Comixx.search_placeholder') ?>" autocomplete="off" id="headerSearchInput">
        <div class="search-dropdown" id="headerSearchDropdown"></div>
      </div>
    </div>
    <div class="top-navbar-right">
      <!-- Mobile search toggle -->
      <button class="icon-btn mobile-search-btn" id="mobileSearchBtn"><i class="fas fa-search"></i></button>
      <button class="icon-btn mobile-search-close-btn" id="mobileSearchCloseBtn" style="display:none"><i class="fas fa-times"></i></button>

      <!-- Theme toggle -->
      <button class="icon-btn" data-theme-toggle><i class="fas fa-moon"></i></button>

      <!-- Auth area -->
      <div id="headerAuthArea" style="display:flex;align-items:center;gap:12px;">
        <?php if (!empty($currentUser)): ?>
        <!-- Notifications -->
        <div class="noti-wrap" id="notiWrap">
          <button class="icon-btn" id="notiBtn" aria-label="<?= lang('Comixx.notifications') ?>">
            <i class="far fa-bell"></i>
            <span class="noti-badge" id="notiBadge" style="display:none">0</span>
          </button>
          <div class="noti-panel" id="notiPanel" style="display:none">
            <div class="noti-panel-header">
              <span><?= lang('Comixx.notifications') ?></span>
              <button onclick="notiMarkAll(event)"><?= lang('Comixx.mark_all_read') ?></button>
            </div>
            <div class="noti-list" id="notiList">
              <div class="noti-empty"><?= lang('Comixx.loading') ?></div>
            </div>
            <a href="/notifications" class="noti-panel-footer"><?= lang('Comixx.view_all') ?></a>
          </div>
        </div>

        <!-- User profile -->
        <a href="/profile" class="icon-btn"><i class="far fa-user"></i></a>
        <a href="/logout" class="icon-btn" title="<?= lang('Comixx.logout') ?>"><i class="fas fa-sign-out-alt"></i></a>
        <?php else: ?>
        <!-- Login/Register -->
        <a href="/login" class="btn-login"><i class="fas fa-user"></i> <span><?= lang('Comixx.login') ?></span></a>
        <a href="/register" class="btn-register"><?= lang('Comixx.register') ?></a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Mobile Search Dropdown -->
  <div class="mobile-search-dropdown" id="mobileSearchDropdown">
    <div class="mobile-search-input-wrap">
      <div class="mobile-search-field">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="<?= lang('Comixx.search_placeholder') ?>" id="mobileSearchInput" autocomplete="off">
      </div>
      <button class="mobile-search-submit" id="mobileSearchSubmit">
        <i class="fas fa-search"></i>
      </button>
    </div>
    <div class="search-dropdown" id="mobileSearchResults"></div>
  </div>

<script>
// Lang strings for JS
var __lang = {
  no_notifications: <?= json_encode(lang('Comixx.no_notifications')) ?>,
  loading: <?= json_encode(lang('Comixx.loading')) ?>,
  now: <?= json_encode(lang('ComixxTime.now')) ?>,
  js_min: <?= json_encode(lang('ComixxTime.js_min')) ?>,
  js_hour: <?= json_encode(lang('ComixxTime.js_hour')) ?>,
  js_day: <?= json_encode(lang('ComixxTime.js_day')) ?>,
  js_format: <?= json_encode(lang('ComixxTime.js_format')) ?>
};

// Time diff helper
function _timeDiff(s){
  if(!s)return '';var d=new Date(s.replace(' ','T')),now=new Date(),sec=Math.floor((now-d)/1000);
  function f(n,unit){if(unit.indexOf('{n}')!==-1)return unit.replace('{n}',n);return n+unit;}
  if(sec<60)return __lang.now;
  if(sec<3600){var m=Math.floor(sec/60);return f(m,__lang.js_min);}
  if(sec<86400){var h=Math.floor(sec/3600);return f(h,__lang.js_hour);}
  var dd=Math.floor(sec/86400);return f(dd,__lang.js_day);
}

function _esc(s){return s?String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'):'';}

// Notification panel
(function(){
  function updateBadge(n){
    var b=document.getElementById('notiBadge');if(!b)return;
    if(n>0){b.textContent=n>99?'99+':n;b.style.display='flex';}else{b.style.display='none';}
  }
  window.updateBadge=updateBadge;

  // Load unread count on page load
  <?php if (!empty($currentUser)): ?>
  fetch('/api/notifications',{credentials:'same-origin'}).then(function(r){return r.json()}).then(function(d){
    updateBadge(d.unread||0);
  }).catch(function(){});
  <?php endif; ?>

  // Notification panel toggle
  var notiOpen=false;
  document.addEventListener('click',function(e){
    var wrap=document.getElementById('notiWrap');
    var panel=document.getElementById('notiPanel');
    if(notiOpen&&wrap&&panel&&!wrap.contains(e.target)){
      notiOpen=false;panel.style.display='none';
    }
  });

  var notiBtn=document.getElementById('notiBtn');
  if(notiBtn) notiBtn.addEventListener('click',function(e){
    e.stopPropagation();notiOpen=!notiOpen;
    var panel=document.getElementById('notiPanel');
    if(notiOpen){panel.style.display='block';loadNotiList();}
    else{panel.style.display='none';}
  });

  function loadNotiList(){
    fetch('/api/notifications',{credentials:'same-origin'}).then(function(r){return r.json()}).then(function(d){
      updateBadge(d.unread||0);
      var list=document.getElementById('notiList');
      if(!d.notifications||!d.notifications.length){list.innerHTML='<div class="noti-empty">'+__lang.no_notifications+'</div>';return;}
      list.innerHTML=d.notifications.map(function(n){
        var isResolved=n.type==='report_resolved';
        var avatar,msg;
        if(isResolved){
          avatar='<div class="noti-avatar" style="background:#16a34a"><i class="fas fa-check" style="color:#fff;font-size:12px"></i></div>';
          msg='Your report for <strong>'+_esc(n.manga_name)+'</strong> has been <span style="color:#4ade80">resolved</span>';
        }else{
          var actor=n.actor_name||n.actor_username||'?';
          avatar='<div class="noti-avatar">'+_esc(actor.charAt(0).toUpperCase())+'</div>';
          msg='<strong style="color:var(--accent-blue)">'+_esc(actor)+'</strong> replied in <strong>'+_esc(n.manga_name)+'</strong>';
        }
        return '<div class="noti-item" data-id="'+n.id+'" data-slug="'+_esc(n.manga_slug)+'" data-chapter="'+_esc(n.chapter_slug||'')+'" onclick="notiClick(this)">'
          +avatar+'<div class="noti-content"><p class="noti-msg">'+msg+'</p>'
          +(n.preview?'<p class="noti-preview">'+_esc(n.preview)+'</p>':'')
          +'<p class="noti-time">'+_timeDiff(n.created_at)+'</p></div>'
          +'<div class="noti-dot"></div></div>';
      }).join('');
    }).catch(function(){});
  }

  window.notiClick=function(el){
    var id=el.dataset.id,slug=el.dataset.slug,chapter=el.dataset.chapter;
    fetch('/api/notifications/'+id+'/read',{method:'POST',credentials:'same-origin'});
    el.remove();var rem=document.querySelectorAll('.noti-item').length;updateBadge(rem);
    if(!rem)document.getElementById('notiList').innerHTML='<div class="noti-empty">'+__lang.no_notifications+'</div>';
    if(slug){window.location.href=chapter?'/manga/'+slug+'/'+chapter+'#cc-section':'/manga/'+slug+'#comment-section';}
  };
  window.notiMarkAll=function(e){
    e.stopPropagation();
    fetch('/api/notifications/read-all',{method:'POST',credentials:'same-origin'}).then(function(){
      document.getElementById('notiList').innerHTML='<div class="noti-empty">'+__lang.no_notifications+'</div>';updateBadge(0);
    });
  };
})();

// Mobile search toggle
(function(){
  var searchBtn=document.getElementById('mobileSearchBtn');
  var closeBtn=document.getElementById('mobileSearchCloseBtn');
  var dropdown=document.getElementById('mobileSearchDropdown');
  var input=document.getElementById('mobileSearchInput');

  function openSearch(){
    dropdown.classList.add('active');
    document.body.classList.add('mobile-search-open');
    setTimeout(function(){if(input)input.focus();},300);
  }
  function closeSearch(){
    dropdown.classList.remove('active');
    document.body.classList.remove('mobile-search-open');
  }

  if(searchBtn) searchBtn.addEventListener('click',openSearch);
  if(closeBtn) closeBtn.addEventListener('click',closeSearch);

  // Submit on enter
  if(input) input.addEventListener('keydown',function(e){
    if(e.key==='Enter'){
      var q=input.value.trim();
      if(q) window.location.href='/search?filter[name]='+encodeURIComponent(q);
    }
  });

  // Submit button
  var submitBtn=document.getElementById('mobileSearchSubmit');
  if(submitBtn&&input){
    submitBtn.addEventListener('click',function(){
      var q=input.value.trim();
      if(q) window.location.href='/search?filter[name]='+encodeURIComponent(q);
    });
  }

  // Mobile search autocomplete
  if(input){
    var mResults=document.getElementById('mobileSearchResults');
    var mTimer;
    input.addEventListener('input',function(){
      clearTimeout(mTimer);
      var q=input.value.trim();
      if(q.length<2){if(mResults)mResults.innerHTML='';return;}
      mTimer=setTimeout(function(){
        fetch('/api/search?q='+encodeURIComponent(q)).then(function(r){return r.json()}).then(function(d){
          if(!mResults)return;
          if(!d.length){mResults.innerHTML='<div style="padding:12px;color:var(--text-muted)">'+__lang.no_notifications+'</div>';return;}
          mResults.innerHTML=d.map(function(m){
            return '<a href="/manga/'+_esc(m.slug)+'" class="search-result-item">'
              +'<img src="'+(m.cover_url||'')+'" style="width:36px;height:50px;object-fit:cover;border-radius:4px">'
              +'<span>'+_esc(m.name)+'</span></a>';
          }).join('');
        });
      },300);
    });
  }
})();

// Sidebar toggle handled in main.php layout
</script>
