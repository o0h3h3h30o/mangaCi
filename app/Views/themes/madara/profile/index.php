<?= $this->extend('themes/madara/layouts/main') ?>

<?= $this->section('content') ?>
<main>
    <div class="max-w-7xl mx-auto px-3 w-full mt-6">
        <div class="border-2 border-gray-100 dark:border-dark-blue bg-white dark:bg-fire-blue shadow-md rounded dark:shadow-gray-900 overflow-hidden">

            <!-- Header bar -->
            <div class="p-4 border-b border-gray-100 dark:border-dark-blue">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl">
                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Profile</h2>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6">
                <div class="flex flex-col sm:flex-row gap-6 items-start">

                    <!-- Avatar -->
                    <div class="flex-shrink-0 flex flex-col items-center gap-2">
                        <div class="p-[3px] rounded-full bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 shadow-lg shadow-indigo-500/30">
                            <div class="p-[3px] rounded-full bg-white dark:bg-fire-blue">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-3xl">
                                    <?= mb_strtoupper(mb_substr($currentUser['name'], 0, 1)) ?>
                                </div>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">@<?= esc($currentUser['username']) ?></span>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0 space-y-3">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate"><?= esc($currentUser['name']) ?></h3>

                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate"><?= esc($user['email']) ?></span>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Joined <?= date('M d, Y', (int)($user['created_on'] ?? strtotime($user['created_at'] ?? 'now'))) ?></span>
                        </div>
                    </div>

                </div>

                <!-- Stats row -->
                <div class="mt-6 grid grid-cols-3 gap-3">
                    <a href="/bookmarks" class="flex flex-col items-center py-4 rounded-lg border border-gray-100 dark:border-dark-blue hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:border-indigo-200 dark:hover:border-indigo-500/30 transition-colors">
                        <span class="text-2xl font-bold text-indigo-500"><?= $bookmarkCount ?></span>
                        <span class="text-xs text-gray-400 mt-1">Following</span>
                    </a>
                    <a href="/history" class="flex flex-col items-center py-4 rounded-lg border border-gray-100 dark:border-dark-blue hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:border-indigo-200 dark:hover:border-indigo-500/30 transition-colors">
                        <span class="text-2xl font-bold text-indigo-500"><?= $historyCount ?></span>
                        <span class="text-xs text-gray-400 mt-1">History</span>
                    </a>
                    <a href="/notifications" class="flex flex-col items-center py-4 rounded-lg border border-gray-100 dark:border-dark-blue hover:bg-red-50 dark:hover:bg-red-500/10 hover:border-red-200 dark:hover:border-red-500/30 transition-colors">
                        <span class="text-2xl font-bold text-red-500"><?= $unreadNotiCount ?></span>
                        <span class="text-xs text-gray-400 mt-1">Notifications</span>
                    </a>
                </div>

                <!-- Actions -->
                <div class="mt-5 flex justify-end gap-3">
                    <a href="/profile/change-password" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-indigo-500 border border-indigo-200 dark:border-indigo-900/40 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Change Password
                    </a>
                    <a href="/logout" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-red-500 border border-red-200 dark:border-red-900/40 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </a>
                </div>

            </div>

        </div>

    <!-- Notifications card -->
    <div class="mt-4 border-2 border-gray-100 dark:border-dark-blue bg-white dark:bg-fire-blue shadow-md rounded dark:shadow-gray-900 overflow-hidden">

        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-100 dark:border-dark-blue flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl">
                    <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Notifications</h2>
                <span id="pn-count" class="hidden text-xs font-semibold bg-red-500 text-white rounded-full px-2 py-0.5">0</span>
            </div>
            <button id="pn-mark-all" onclick="pnMarkAll()"
                class="text-xs text-indigo-500 hover:text-indigo-400 border border-indigo-200 dark:border-indigo-800 rounded-lg px-3 py-1.5 transition-colors">
                Mark all as read
            </button>
        </div>

        <!-- List -->
        <div id="pn-list" class="divide-y divide-gray-100 dark:divide-dark-blue">
            <div class="text-center text-gray-400 py-10 text-sm">Loading...</div>
        </div>

    </div>
</div><!-- end max-w-7xl -->

<script>
(function(){
  function _esc(s){
    if(!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
  function _time(s){
    if(!s) return '';
    var d=new Date(s.replace(' ','T')), now=new Date();
    var sec=Math.floor((now-d)/1000);
    if(sec<60) return 'Just now';
    if(sec<3600) return Math.floor(sec/60)+' minutes ago';
    if(sec<86400) return Math.floor(sec/3600)+' hours ago';
    return Math.floor(sec/86400)+' days ago';
  }
  function setCount(n){
    var el=document.getElementById('pn-count');
    if(n>0){ el.textContent=n>99?'99+':n; el.classList.remove('hidden'); }
    else { el.classList.add('hidden'); }
    // Đồng bộ badge trên header nếu có
    if(typeof updateBadge==='function') updateBadge(n);
  }

  function loadList(){
    fetch('/api/notifications',{credentials:'same-origin'})
      .then(function(r){return r.json();})
      .then(function(d){
        setCount(d.unread||0);
        var list=document.getElementById('pn-list');
        if(!d.notifications||d.notifications.length===0){
          list.innerHTML='<div class="text-center text-gray-400 py-10 text-sm">No notifications</div>';
          return;
        }
        list.innerHTML=d.notifications.map(function(n){
          var actor=n.actor_name||n.actor_username||'?';
          var initial=actor.charAt(0).toUpperCase();
          return '<div class="pn-item flex items-start gap-3 px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-light-blue transition-colors" '
            +'data-id="'+n.id+'" data-slug="'+_esc(n.manga_slug)+'" onclick="pnClick(this)">'+
            '<div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">'+_esc(initial)+'</div>'+
            '<div class="flex-1 min-w-0">'+
            '<p class="text-sm text-gray-800 dark:text-gray-200">'+
            '<span class="font-semibold text-indigo-500">'+_esc(actor)+'</span>'+
            ' replied to your comment in '+
            '<span class="font-medium text-gray-900 dark:text-white">'+_esc(n.manga_name)+'</span>'+
            '</p>'+
            '<p class="text-xs text-gray-400 mt-0.5 truncate">'+_esc(n.preview)+'</p>'+
            '<p class="text-xs text-gray-400 mt-1">'+_time(n.created_at)+'</p>'+
            '</div>'+
            '<div class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0 mt-2"></div>'+
            '</div>';
        }).join('');
      })
      .catch(function(){
        document.getElementById('pn-list').innerHTML='<div class="text-center text-gray-400 py-10 text-sm">Failed to load notifications</div>';
      });
  }

  window.pnClick=function(el){
    var id=el.dataset.id, slug=el.dataset.slug;
    fetch('/api/notifications/'+id+'/read',{method:'POST',credentials:'same-origin'});
    el.remove();
    var rem=document.querySelectorAll('.pn-item').length;
    setCount(rem);
    if(rem===0) document.getElementById('pn-list').innerHTML='<div class="text-center text-gray-400 py-10 text-sm">No notifications</div>';
    if(slug) window.location.href='/manga/'+slug+'#comment-section';
  };

  window.pnMarkAll=function(){
    fetch('/api/notifications/read-all',{method:'POST',credentials:'same-origin'})
      .then(function(){
        document.getElementById('pn-list').innerHTML='<div class="text-center text-gray-400 py-10 text-sm">No notifications</div>';
        setCount(0);
      });
  };

  document.addEventListener('DOMContentLoaded', loadList);
})();
</script>
</main>
<?= $this->endSection() ?>
