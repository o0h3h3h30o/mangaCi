<?= $this->extend('themes/mangahub/layouts/main') ?>

<?= $this->section('content') ?>
<style>
.pf-page { max-width: 1200px; margin: 0 auto; padding: 24px 12px; }
.pf-panel { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
.pf-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; }
.pf-header-icon { width: 32px; height: 32px; border-radius: 8px; background: var(--accent); display: flex; align-items: center; justify-content: center; }
.pf-header h2 { font-size: 18px; font-weight: 700; color: var(--txt); margin: 0; }
.pf-body { padding: 24px; }
.pf-top { display: flex; gap: 24px; align-items: flex-start; }
.pf-avatar-wrap { display: flex; flex-direction: column; align-items: center; gap: 8px; flex-shrink: 0; }
.pf-avatar-ring { padding: 3px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), var(--accent2)); box-shadow: 0 4px 12px rgba(232,25,44,0.3); }
.pf-avatar-inner { padding: 3px; border-radius: 50%; background: var(--card); }
.pf-avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), var(--accent2)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 30px; }
.pf-username { font-size: 12px; color: var(--txt3); }
.pf-info { flex: 1; min-width: 0; }
.pf-info h3 { margin: 0 0 12px; font-size: 18px; font-weight: 700; color: var(--txt); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pf-detail { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--txt2); margin-bottom: 8px; }
.pf-detail svg { flex-shrink: 0; }
.pf-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 24px; }
.pf-stat { display: flex; flex-direction: column; align-items: center; padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border); text-decoration: none; transition: border-color 0.2s, background 0.2s; }
.pf-stat:hover { border-color: var(--accent); background: rgba(232,25,44,0.05); }
.pf-stat-num { font-size: 22px; font-weight: 700; color: var(--accent); }
.pf-stat-label { font-size: 12px; color: var(--txt3); margin-top: 4px; }
.pf-actions { margin-top: 20px; display: flex; justify-content: flex-end; gap: 12px; }
.pf-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 500; text-decoration: none; border: 1px solid var(--border); transition: background 0.2s, border-color 0.2s; cursor: pointer; }
.pf-btn-outline { color: var(--accent); border-color: var(--accent); background: transparent; }
.pf-btn-outline:hover { background: rgba(232,25,44,0.08); }
.pf-btn-danger { color: #ef4444; border-color: #ef4444; background: transparent; }
.pf-btn-danger:hover { background: rgba(239,68,68,0.08); }
.pf-noti-panel { margin-top: 16px; }
.pf-noti-header { padding: 12px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.pf-noti-header-left { display: flex; align-items: center; gap: 10px; }
.pf-noti-header h2 { font-size: 18px; font-weight: 700; color: var(--txt); margin: 0; }
.pf-noti-badge { font-size: 11px; font-weight: 600; background: #ef4444; color: #fff; border-radius: 10px; padding: 2px 8px; display: none; }
.pf-noti-mark { font-size: 12px; color: var(--accent); border: 1px solid var(--accent); border-radius: var(--radius-sm); padding: 4px 12px; background: transparent; cursor: pointer; transition: background 0.2s; }
.pf-noti-mark:hover { background: rgba(232,25,44,0.08); }
#pn-list { }
.pn-row { display: flex; align-items: flex-start; gap: 12px; padding: 12px 20px; cursor: pointer; transition: background 0.2s; }
.pn-row:hover { background: rgba(232,25,44,0.04); }
.pn-row + .pn-row { border-top: 1px solid var(--border); }
.pn-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), var(--accent2)); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 14px; font-weight: 700; flex-shrink: 0; }
.pn-content { flex: 1; min-width: 0; }
.pn-content p { margin: 0; font-size: 13px; color: var(--txt2); }
.pn-content .pn-actor { font-weight: 600; color: var(--accent); }
.pn-content .pn-manga { font-weight: 500; color: var(--txt); }
.pn-content .pn-preview { font-size: 12px; color: var(--txt3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
.pn-content .pn-time { font-size: 11px; color: var(--txt3); margin-top: 4px; }
.pn-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent); flex-shrink: 0; margin-top: 8px; }
.pn-empty { text-align: center; color: var(--txt3); padding: 40px 20px; font-size: 13px; }
@media (max-width: 640px) {
    .pf-top { flex-direction: column; align-items: center; text-align: center; }
    .pf-info h3 { text-align: center; }
    .pf-detail { justify-content: center; }
    .pf-actions { justify-content: center; }
}
</style>

<main>
    <div class="pf-page">
        <div class="pf-panel">

            <div class="pf-header">
                <div class="pf-header-icon">
                    <svg style="width:14px;height:14px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2>Profile</h2>
            </div>

            <div class="pf-body">
                <div class="pf-top">
                    <div class="pf-avatar-wrap">
                        <div class="pf-avatar-ring">
                            <div class="pf-avatar-inner">
                                <div class="pf-avatar">
                                    <?= mb_strtoupper(mb_substr($currentUser['name'], 0, 1)) ?>
                                </div>
                            </div>
                        </div>
                        <span class="pf-username">@<?= esc($currentUser['username']) ?></span>
                    </div>

                    <div class="pf-info">
                        <h3><?= esc($currentUser['name']) ?></h3>
                        <div class="pf-detail">
                            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span><?= esc($user['email']) ?></span>
                        </div>
                        <div class="pf-detail">
                            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Joined <?= date('M d, Y', (int)($user['created_on'] ?? strtotime($user['created_at'] ?? 'now'))) ?></span>
                        </div>
                    </div>
                </div>

                <div class="pf-stats">
                    <a href="/bookmarks" class="pf-stat">
                        <span class="pf-stat-num"><?= $bookmarkCount ?></span>
                        <span class="pf-stat-label">Following</span>
                    </a>
                    <a href="/history" class="pf-stat">
                        <span class="pf-stat-num"><?= $historyCount ?></span>
                        <span class="pf-stat-label">History</span>
                    </a>
                    <a href="/notifications" class="pf-stat">
                        <span class="pf-stat-num"><?= $unreadNotiCount ?></span>
                        <span class="pf-stat-label">Notifications</span>
                    </a>
                </div>

                <div class="pf-actions">
                    <a href="/profile/change-password" class="pf-btn pf-btn-outline">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Change Password
                    </a>
                    <a href="/logout" class="pf-btn pf-btn-danger">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </a>
                </div>
            </div>

        </div>

        <!-- Notifications card -->
        <div class="pf-panel pf-noti-panel">
            <div class="pf-noti-header">
                <div class="pf-noti-header-left">
                    <div class="pf-header-icon">
                        <svg style="width:14px;height:14px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h2>Notifications</h2>
                    <span id="pn-count" class="pf-noti-badge">0</span>
                </div>
                <button id="pn-mark-all" onclick="pnMarkAll()" class="pf-noti-mark">
                    Mark all as read
                </button>
            </div>

            <div id="pn-list">
                <div class="pn-empty">Loading...</div>
            </div>
        </div>

    </div>
</main>

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
    if(n>0){ el.textContent=n>99?'99+':n; el.style.display='inline'; }
    else { el.style.display='none'; }
    if(typeof updateBadge==='function') updateBadge(n);
  }

  function loadList(){
    fetch('/api/notifications',{credentials:'same-origin'})
      .then(function(r){return r.json();})
      .then(function(d){
        setCount(d.unread||0);
        var list=document.getElementById('pn-list');
        if(!d.notifications||d.notifications.length===0){
          list.innerHTML='<div class="pn-empty">No notifications</div>';
          return;
        }
        list.innerHTML=d.notifications.map(function(n){
          var actor=n.actor_name||n.actor_username||'?';
          var initial=actor.charAt(0).toUpperCase();
          return '<div class="pn-row pn-item" data-id="'+n.id+'" data-slug="'+_esc(n.manga_slug)+'" onclick="pnClick(this)">'+
            '<div class="pn-avatar">'+_esc(initial)+'</div>'+
            '<div class="pn-content">'+
            '<p><span class="pn-actor">'+_esc(actor)+'</span> replied to your comment in <span class="pn-manga">'+_esc(n.manga_name)+'</span></p>'+
            (n.preview ? '<p class="pn-preview">'+_esc(n.preview)+'</p>' : '')+
            '<p class="pn-time">'+_time(n.created_at)+'</p>'+
            '</div>'+
            '<div class="pn-dot"></div>'+
            '</div>';
        }).join('');
      })
      .catch(function(){
        document.getElementById('pn-list').innerHTML='<div class="pn-empty">Failed to load notifications</div>';
      });
  }

  window.pnClick=function(el){
    var id=el.dataset.id, slug=el.dataset.slug;
    fetch('/api/notifications/'+id+'/read',{method:'POST',credentials:'same-origin'});
    el.remove();
    var rem=document.querySelectorAll('.pn-item').length;
    setCount(rem);
    if(rem===0) document.getElementById('pn-list').innerHTML='<div class="pn-empty">No notifications</div>';
    if(slug) window.location.href='/manga/'+slug+'#comment-section';
  };

  window.pnMarkAll=function(){
    fetch('/api/notifications/read-all',{method:'POST',credentials:'same-origin'})
      .then(function(){
        document.getElementById('pn-list').innerHTML='<div class="pn-empty">No notifications</div>';
        setCount(0);
      });
  };

  document.addEventListener('DOMContentLoaded', loadList);
})();
</script>
<?= $this->endSection() ?>
