<?= $this->extend('themes/mangahub/layouts/main') ?>

<?= $this->section('content') ?>
<style>
.nt-page { max-width: 1200px; margin: 0 auto; padding: 24px 12px 40px; }
.nt-panel { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
.nt-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.nt-header-left { display: flex; align-items: center; gap: 10px; }
.nt-header-icon { width: 32px; height: 32px; border-radius: 8px; background: var(--accent); display: flex; align-items: center; justify-content: center; }
.nt-header h2 { font-size: 18px; font-weight: 700; color: var(--txt); margin: 0; }
.nt-badge { font-size: 11px; font-weight: 600; background: #ef4444; color: #fff; border-radius: 10px; padding: 2px 8px; }
.nt-back { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: var(--radius-sm); color: var(--txt3); text-decoration: none; transition: background 0.2s, color 0.2s; }
.nt-back:hover { background: var(--border); color: var(--txt); }
.nt-mark-all { font-size: 12px; color: var(--accent); border: 1px solid var(--accent); border-radius: var(--radius-sm); padding: 4px 12px; background: transparent; cursor: pointer; transition: background 0.2s; }
.nt-mark-all:hover { background: rgba(232,25,44,0.08); }
.nt-list { }
.nt-empty { text-align: center; color: var(--txt3); padding: 60px 20px; font-size: 13px; }
.nt-item { display: flex; align-items: flex-start; gap: 12px; padding: 14px 20px; cursor: pointer; transition: background 0.2s; }
.nt-item:hover { background: rgba(232,25,44,0.04); }
.nt-item + .nt-item { border-top: 1px solid var(--border); }
.nt-item-unread { background: rgba(232,25,44,0.04); }
.nt-item-read { opacity: 0.6; }
.nt-item-read:hover { opacity: 1; }
.nt-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 15px; flex-shrink: 0; }
.nt-avatar-reply { background: linear-gradient(135deg, var(--accent), var(--accent2)); }
.nt-avatar-resolved { background: #22c55e; }
.nt-content { flex: 1; min-width: 0; }
.nt-content p { margin: 0; }
.nt-text { font-size: 13px; color: var(--txt2); }
.nt-text-unread { color: var(--txt); font-weight: 500; }
.nt-actor { font-weight: 600; color: var(--accent); }
.nt-actor-read { color: var(--txt3); }
.nt-manga { font-weight: 500; color: var(--txt); }
.nt-resolved { font-weight: 600; color: #22c55e; }
.nt-preview { font-size: 12px; color: var(--txt3); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.nt-time { font-size: 11px; color: var(--txt3); margin-top: 4px; }
.nt-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--accent); flex-shrink: 0; margin-top: 8px; }
</style>

<main>
    <div class="nt-page">
        <div class="nt-panel">

            <div class="nt-header">
                <div class="nt-header-left">
                    <a href="/profile" class="nt-back" title="Go back">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="nt-header-icon">
                        <svg style="width:14px;height:14px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h2>Notifications</h2>
                    <?php if ($unread > 0): ?>
                    <span class="nt-badge"><?= $unread ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($unread > 0): ?>
                <button id="npl-mark-all" class="nt-mark-all">
                    Mark all as read
                </button>
                <?php endif; ?>
            </div>

            <div id="npl-list" class="nt-list">
                <?php if (empty($notifications)): ?>
                <div class="nt-empty">No notifications</div>
                <?php else: ?>
                <?php foreach ($notifications as $n):
                    $isUnread  = (int)$n['is_read'] === 0;
                    $actor     = $n['actor_name'] ?: $n['actor_username'] ?: '?';
                    $initial   = mb_strtoupper(mb_substr($actor, 0, 1));
                    $timeAgo   = '';
                    if (!empty($n['created_at'])) {
                        $sec = time() - strtotime($n['created_at']);
                        if ($sec < 60)       $timeAgo = 'Just now';
                        elseif ($sec < 3600) $timeAgo = floor($sec/60) . ' minutes ago';
                        elseif ($sec < 86400)$timeAgo = floor($sec/3600) . ' hours ago';
                        else                 $timeAgo = floor($sec/86400) . ' days ago';
                    }
                ?>
                <div class="npl-item nt-item <?= $isUnread ? 'nt-item-unread' : 'nt-item-read' ?>"
                    data-id="<?= (int)$n['id'] ?>"
                    data-slug="<?= esc($n['manga_slug']) ?>"
                    data-chapter="<?= esc($n['chapter_slug'] ?? '') ?>"
                    data-read="<?= $isUnread ? '0' : '1' ?>"
                    onclick="nplClick(this)">

                    <?php if (($n['type'] ?? '') === 'report_resolved'): ?>
                    <div class="nt-avatar nt-avatar-resolved">
                        <svg style="width:20px;height:20px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <?php else: ?>
                    <div class="nt-avatar nt-avatar-reply">
                        <?= esc($initial) ?>
                    </div>
                    <?php endif; ?>

                    <div class="nt-content">
                        <p class="nt-text <?= $isUnread ? 'nt-text-unread' : '' ?>">
                        <?php if (($n['type'] ?? '') === 'report_resolved'): ?>
                            Your error report for
                            <span class="nt-manga"><?= esc($n['manga_name']) ?></span>
                            has been <span class="nt-resolved">resolved</span>
                        <?php else: ?>
                            <span class="nt-actor <?= $isUnread ? '' : 'nt-actor-read' ?>">
                                <?= esc($actor) ?>
                            </span>
                            replied to your comment in
                            <span class="nt-manga"><?= esc($n['manga_name']) ?></span>
                        <?php endif; ?>
                        </p>
                        <?php if (!empty($n['preview'])): ?>
                        <p class="nt-preview"><?= esc($n['preview']) ?></p>
                        <?php endif; ?>
                        <p class="nt-time"><?= $timeAgo ?></p>
                    </div>

                    <?php if ($isUnread): ?>
                    <div class="npl-dot nt-dot"></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
(function(){
  window.nplClick = function(el){
    var id      = el.dataset.id;
    var slug    = el.dataset.slug;
    var chapter = el.dataset.chapter;
    var read    = el.dataset.read;

    if(read === '0'){
      fetch('/api/notifications/'+id+'/read', {method:'POST', credentials:'same-origin'});
      el.dataset.read = '1';
      el.className = el.className.replace('nt-item-unread', 'nt-item-read');
      var dot = el.querySelector('.npl-dot');
      if(dot) dot.remove();
      if(typeof updateBadge === 'function'){
        var remaining = document.querySelectorAll('.npl-dot').length;
        updateBadge(remaining);
      }
      var dots = document.querySelectorAll('.npl-dot');
      if(dots.length === 0){
        var btn = document.getElementById('npl-mark-all');
        if(btn) btn.style.display = 'none';
      }
    }

    if(slug){
      var url = chapter ? '/manga/'+slug+'/'+chapter+'#cc-section' : '/manga/'+slug+'#comment-section';
      setTimeout(function(){ window.location.href = url; }, 150);
    }
  };

  var markAllBtn = document.getElementById('npl-mark-all');
  if(markAllBtn){
    markAllBtn.addEventListener('click', function(){
      fetch('/api/notifications/read-all', {method:'POST', credentials:'same-origin'})
        .then(function(){
          document.querySelectorAll('.npl-item').forEach(function(el){
            if(el.dataset.read === '0'){
              el.dataset.read = '1';
              el.className = el.className.replace('nt-item-unread', 'nt-item-read');
              var dot = el.querySelector('.npl-dot');
              if(dot) dot.remove();
            }
          });
          markAllBtn.style.display = 'none';
          if(typeof updateBadge === 'function') updateBadge(0);
        });
    });
  }
})();
</script>
<?= $this->endSection() ?>
