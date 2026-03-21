<body>
  <!-- Header -->
  <header class="header">
    <div class="header-inner container">
      <a href="/" class="logo">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <rect x="2" y="3" width="20" height="18" rx="2" stroke="white" stroke-width="2"/>
          <path d="M8 3v18M16 3v18" stroke="white" stroke-width="2"/>
        </svg>
        <?php $_logo = site_setting('site_logo'); ?>
        <?php if ($_logo): ?>
        <img src="<?= esc($_logo) ?>" alt="<?= esc(site_setting('site_title', 'COMIX')) ?>" style="height:22px">
        <?php else: ?>
        <span><?= esc(site_setting('site_title', 'COMIX')) ?></span>
        <?php endif; ?>
      </a>
      <div class="search-bar">
        <i class="fas fa-search search-icon"></i>
        <input type="text" placeholder="Buscar cómic..." autocomplete="off" id="headerSearchInput">
        <a href="/search" class="filter-btn"><i class="fas fa-sliders-h"></i> FILTRO</a>
        <div class="search-dropdown" id="headerSearchDropdown"></div>
      </div>
      <div class="header-actions">
        <!-- Logged-in actions (hidden by default, shown by JS) -->
        <div id="headerLoggedIn" style="display:none;align-items:center;gap:4px">
          <div class="noti-wrap" id="notiWrap">
            <button class="icon-btn" id="notiBtn" aria-label="Notifications"><i class="far fa-bell"></i><span class="noti-badge" id="notiBadge" style="display:none">0</span></button>
            <div class="noti-panel" id="notiPanel" style="display:none">
              <div class="noti-panel-header">
                <span>Notificaciones</span>
                <button onclick="notiMarkAll(event)">Marcar todo leído</button>
              </div>
              <div class="noti-list" id="notiList">
                <div class="noti-empty">Cargando...</div>
              </div>
            </div>
          </div>
          <a href="/profile" class="icon-btn"><i class="far fa-user"></i></a>
        </div>
        <!-- Logged-out (shown by default) -->
        <a href="/login" class="login-btn" id="headerLoginBtn">LOGIN</a>
      </div>
      <button class="mobile-search-btn" id="mobileSearchBtn"><i class="fas fa-search"></i></button>
      <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
    </div>
    <!-- Mobile Search Bar (toggleable) -->
    <div class="mobile-search-bar" id="mobileSearchBar">
      <input type="text" placeholder="Buscar cómic..." autocomplete="off" id="mobileSearchInput">
      <div class="search-dropdown" id="mobileSearchDropdown"></div>
    </div>
  </header>

  <!-- Mobile Menu Overlay -->
  <div class="mobile-menu-overlay" id="mobileOverlay"></div>
  <div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-header">
      <a href="/" class="logo">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <rect x="2" y="3" width="20" height="18" rx="2" stroke="white" stroke-width="2"/>
          <path d="M8 3v18M16 3v18" stroke="white" stroke-width="2"/>
        </svg>
        <span><?= esc(site_setting('site_title', 'COMIX')) ?></span>
      </a>
      <button class="mobile-menu-close" id="mobileMenuClose"><i class="fas fa-times"></i></button>
    </div>
    <nav class="mobile-menu-nav">
      <a href="/"><i class="fas fa-play"></i> Inicio</a>
      <a href="/search"><i class="fas fa-play"></i> Explorar</a>
      <a href="/search?sort=-views"><i class="fas fa-play"></i> Populares</a>
      <?php if (!empty($categories)): ?>
      <div class="mobile-genre-wrapper">
        <a href="#" class="mobile-genre-trigger"><i class="fas fa-play"></i> Géneros <i class="fas fa-chevron-down genre-arrow"></i></a>
        <div class="mobile-genre-list">
          <?php foreach ($categories as $cat): ?>
          <a href="/search?genre=<?= esc($cat['slug'], 'url') ?>"><?= esc($cat['name']) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </nav>
    <div class="mobile-menu-actions">
      <div id="mobileLoggedIn" style="display:none">
        <a href="/profile" class="login-btn" style="width:100%;text-align:center;padding:10px;display:block;" id="mobileUsername"></a>
        <a href="/logout" class="login-btn" style="width:100%;text-align:center;padding:10px;display:block;background:transparent;border:1px solid var(--border);margin-top:8px;">SALIR</a>
      </div>
      <div id="mobileLoggedOut">
        <a href="/login" class="login-btn" style="width:100%;text-align:center;padding:10px;display:block;">LOGIN</a>
      </div>
    </div>
  </div>

  <!-- Dropdown Nav -->
  <nav class="nav-dropdown">
    <div class="nav-dropdown-inner container">
      <a href="/search?sort=-created_at"><i class="fas fa-play"></i> Más Nuevos</a>
      <a href="/search?sort=-updated_at"><i class="fas fa-play"></i> Últimas Actualizaciones</a>
      <a href="/search?sort=-views"><i class="fas fa-play"></i> Populares</a>
      <?php if (!empty($categories)): ?>
      <div class="nav-genre-wrapper">
        <a href="#" class="nav-genre-trigger"><i class="fas fa-play"></i> Géneros <i class="fas fa-chevron-down genre-arrow"></i></a>
        <div class="genre-dropdown">
          <?php foreach ($categories as $cat): ?>
          <a href="/search?genre=<?= esc($cat['slug'], 'url') ?>"><?= esc($cat['name']) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      <a href="/search"><i class="fas fa-play"></i> Ver Todo</a>
    </div>
  </nav>

<script>
// Search autocomplete
(function(){
  var input=document.getElementById('headerSearchInput');
  var dropdown=document.getElementById('headerSearchDropdown');
  if(!input||!dropdown)return;
  var timer=null;
  input.addEventListener('input',function(){
    clearTimeout(timer);
    var q=input.value.trim();
    if(q.length<2){dropdown.classList.remove('open');dropdown.innerHTML='';return;}
    timer=setTimeout(function(){
      fetch('/api/search?q='+encodeURIComponent(q)).then(function(r){return r.json()}).then(function(d){
        if(!d.results||!d.results.length){dropdown.classList.remove('open');return;}
        dropdown.innerHTML=d.results.slice(0,5).map(function(m){
          var chLabel=m.latest_chapter?m.latest_chapter.name:'';
          return '<a href="/manga/'+m.slug+'" class="search-result-item">'
            +'<div class="search-result-thumb"><img src="'+(m.cover_full_url||'')+'" width="40" height="56" alt=""></div>'
            +'<div class="search-result-info"><h4>'+m.name+'</h4>'+(chLabel?'<span>'+chLabel+'</span>':'')+'</div>'
            +'</a>';
        }).join('');
        dropdown.classList.add('open');
      }).catch(function(){});
    },300);
  });
  input.addEventListener('keydown',function(e){
    if(e.key==='Enter'){
      e.preventDefault();
      window.location.href='/search?filter[name]='+encodeURIComponent(input.value.trim());
    }
  });
  document.addEventListener('click',function(e){
    if(!e.target.closest('.search-bar'))dropdown.classList.remove('open');
  });
})();

// Mobile search toggle
(function(){
  var btn=document.getElementById('mobileSearchBtn');
  var bar=document.getElementById('mobileSearchBar');
  var input=document.getElementById('mobileSearchInput');
  var dropdown=document.getElementById('mobileSearchDropdown');
  if(!btn||!bar)return;
  btn.addEventListener('click',function(){
    var open=bar.classList.toggle('open');
    if(open&&input){input.focus();}
  });
  // Search autocomplete for mobile
  if(input&&dropdown){
    var timer=null;
    input.addEventListener('input',function(){
      clearTimeout(timer);
      var q=input.value.trim();
      if(q.length<2){dropdown.classList.remove('open');dropdown.innerHTML='';return;}
      timer=setTimeout(function(){
        fetch('/api/search?q='+encodeURIComponent(q)).then(function(r){return r.json()}).then(function(d){
          if(!d.results||!d.results.length){dropdown.classList.remove('open');return;}
          dropdown.innerHTML=d.results.slice(0,5).map(function(m){
            var chLabel=m.latest_chapter?m.latest_chapter.name:'';
            return '<a href="/manga/'+m.slug+'" class="search-result-item">'
              +'<div class="search-result-thumb"><img src="'+(m.cover_full_url||'')+'" width="40" height="56" alt=""></div>'
              +'<div class="search-result-info"><h4>'+m.name+'</h4>'+(chLabel?'<span>'+chLabel+'</span>':'')+'</div>'
              +'</a>';
          }).join('');
          dropdown.classList.add('open');
        }).catch(function(){});
      },300);
    });
    input.addEventListener('keydown',function(e){
      if(e.key==='Enter'){e.preventDefault();window.location.href='/search?filter[name]='+encodeURIComponent(input.value.trim());}
    });
    document.addEventListener('click',function(e){
      if(!e.target.closest('.mobile-search-bar'))dropdown.classList.remove('open');
    });
  }
})();

// Mobile menu
(function(){
  var btn=document.getElementById('mobileMenuBtn');
  var menu=document.getElementById('mobileMenu');
  var overlay=document.getElementById('mobileOverlay');
  var close=document.getElementById('mobileMenuClose');
  if(!btn||!menu)return;
  function open(){menu.classList.add('open');overlay.classList.add('open');document.body.style.overflow='hidden';}
  function shut(){menu.classList.remove('open');overlay.classList.remove('open');document.body.style.overflow='';}
  btn.addEventListener('click',open);
  if(close)close.addEventListener('click',shut);
  if(overlay)overlay.addEventListener('click',shut);
  // Genre dropdown toggle
  document.querySelectorAll('.mobile-genre-trigger').forEach(function(t){
    t.addEventListener('click',function(e){e.preventDefault();t.closest('.mobile-genre-wrapper').classList.toggle('open');});
  });
})();

// Hydrate header from /api/me
(function(){
  function updateBadge(n){
    var b=document.getElementById('notiBadge');if(!b)return;
    if(n>0){b.textContent=n>99?'99+':n;b.style.display='flex';}else{b.style.display='none';}
  }
  window.updateBadge=updateBadge;

  function _esc(s){return s?String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'):'';}
  function _timeDiff(s){
    if(!s)return '';var d=new Date(s.replace(' ','T')),now=new Date(),sec=Math.floor((now-d)/1000);
    if(sec<60)return 'Ahora';if(sec<3600)return 'hace '+Math.floor(sec/60)+'m';
    if(sec<86400)return 'hace '+Math.floor(sec/3600)+'h';return 'hace '+Math.floor(sec/86400)+'d';
  }

  // Hydrate header on page load
  fetch('/api/me',{credentials:'same-origin'}).then(function(r){return r.json()}).then(function(u){
    if(!u.logged_in) return;

    // Desktop: show logged-in actions, hide login btn
    var loggedIn=document.getElementById('headerLoggedIn');
    var loginBtn=document.getElementById('headerLoginBtn');
    if(loggedIn) loggedIn.style.display='flex';
    if(loginBtn) loginBtn.style.display='none';

    // Mobile menu
    var mLoggedIn=document.getElementById('mobileLoggedIn');
    var mLoggedOut=document.getElementById('mobileLoggedOut');
    var mUsername=document.getElementById('mobileUsername');
    if(mLoggedIn) mLoggedIn.style.display='block';
    if(mLoggedOut) mLoggedOut.style.display='none';
    if(mUsername) mUsername.textContent=u.username||u.name;

    // Notification badge
    if(u.unread_count) updateBadge(u.unread_count);

    // Expose user for other scripts
    window.__user=u;
  }).catch(function(){});

  // Notification panel toggle
  var notiOpen=false;
  document.addEventListener('click',function(e){
    var wrap=document.getElementById('notiWrap');
    if(notiOpen&&wrap&&!wrap.contains(e.target)){
      notiOpen=false;document.getElementById('notiPanel').style.display='none';
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
      if(!d.notifications||!d.notifications.length){list.innerHTML='<div class="noti-empty">Sin notificaciones</div>';return;}
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
    if(!rem)document.getElementById('notiList').innerHTML='<div class="noti-empty">Sin notificaciones</div>';
    if(slug){window.location.href=chapter?'/manga/'+slug+'/'+chapter+'#cc-section':'/manga/'+slug+'#comment-section';}
  };
  window.notiMarkAll=function(e){
    e.stopPropagation();
    fetch('/api/notifications/read-all',{method:'POST',credentials:'same-origin'}).then(function(){
      document.getElementById('notiList').innerHTML='<div class="noti-empty">Sin notificaciones</div>';updateBadge(0);
    });
  };
})();
</script>
