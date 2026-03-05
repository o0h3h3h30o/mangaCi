<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<main>
    <div class="max-w-7xl mx-auto px-3 w-full mt-6 pb-10">

        <!-- Header card -->
        <div class="border-2 border-gray-100 dark:border-dark-blue bg-white dark:bg-fire-blue shadow-md rounded-xl overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100 dark:border-dark-blue flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="/profile" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Go back">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl">
                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Notifications</h2>
                    <?php if ($unread > 0): ?>
                    <span class="text-xs font-semibold bg-red-500 text-white rounded-full px-2 py-0.5"><?= $unread ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($unread > 0): ?>
                <button id="npl-mark-all"
                    class="text-xs text-indigo-500 hover:text-indigo-400 border border-indigo-200 dark:border-indigo-800 rounded-lg px-3 py-1.5 transition-colors">
                    Mark all as read
                </button>
                <?php endif; ?>
            </div>

            <!-- List -->
            <div id="npl-list" class="divide-y divide-gray-100 dark:divide-dark-blue">
                <?php if (empty($notifications)): ?>
                <div class="text-center text-gray-400 py-16 text-sm">No notifications</div>
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
                <div class="npl-item flex items-start gap-3 px-5 py-4 cursor-pointer transition-colors <?= $isUnread ? 'bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30' : 'hover:bg-gray-50 dark:hover:bg-light-blue opacity-60 hover:opacity-100' ?>"
                    data-id="<?= (int)$n['id'] ?>"
                    data-slug="<?= esc($n['manga_slug']) ?>"
                    data-chapter="<?= esc($n['chapter_slug'] ?? '') ?>"
                    data-read="<?= $isUnread ? '0' : '1' ?>"
                    onclick="nplClick(this)">

                    <!-- Avatar -->
                    <?php if (($n['type'] ?? '') === 'report_resolved'): ?>
                    <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <?php else: ?>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold flex-shrink-0"
                         style="font-size:15px">
                        <?= esc($initial) ?>
                    </div>
                    <?php endif; ?>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm <?= $isUnread ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400' ?>">
                        <?php if (($n['type'] ?? '') === 'report_resolved'): ?>
                            Your error report for
                            <span class="<?= $isUnread ? 'font-semibold text-gray-900 dark:text-white' : '' ?>">
                                <?= esc($n['manga_name']) ?>
                            </span>
                            has been <span class="font-semibold text-green-600 dark:text-green-400">resolved</span>
                        <?php else: ?>
                            <span class="font-semibold <?= $isUnread ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-500' ?>">
                                <?= esc($actor) ?>
                            </span>
                            replied to your comment in
                            <span class="<?= $isUnread ? 'font-semibold text-gray-900 dark:text-white' : '' ?>">
                                <?= esc($n['manga_name']) ?>
                            </span>
                        <?php endif; ?>
                        </p>
                        <?php if (!empty($n['preview'])): ?>
                        <p class="text-xs mt-0.5 truncate <?= $isUnread ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400 dark:text-gray-600' ?>">
                            <?= esc($n['preview']) ?>
                        </p>
                        <?php endif; ?>
                        <p class="text-xs mt-1 <?= $isUnread ? 'text-gray-400' : 'text-gray-300 dark:text-gray-600' ?>">
                            <?= $timeAgo ?>
                        </p>
                    </div>

                    <!-- Unread dot -->
                    <?php if ($isUnread): ?>
                    <div class="npl-dot w-2.5 h-2.5 rounded-full bg-indigo-500 flex-shrink-0 mt-2"></div>
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
      // Đánh dấu đã đọc → đổi style ngay
      fetch('/api/notifications/'+id+'/read', {method:'POST', credentials:'same-origin'});
      el.dataset.read = '1';
      // Xóa highlight
      el.classList.remove('bg-indigo-50','dark:bg-indigo-900/20','hover:bg-indigo-100','dark:hover:bg-indigo-900/30');
      el.classList.add('opacity-60','hover:opacity-100','hover:bg-gray-50','dark:hover:bg-light-blue');
      // Xóa chấm
      var dot = el.querySelector('.npl-dot');
      if(dot) dot.remove();
      // Đổi text về mờ
      el.querySelectorAll('p').forEach(function(p){
        p.classList.remove('text-gray-900','dark:text-white','font-medium');
        p.classList.add('text-gray-500','dark:text-gray-400');
      });
      // Cập nhật badge header nếu có
      if(typeof updateBadge === 'function'){
        var remaining = document.querySelectorAll('.npl-dot').length;
        updateBadge(remaining);
      }
      // Ẩn nút "Đọc tất cả" nếu hết unread
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
          // Xóa tất cả highlight + dot
          document.querySelectorAll('.npl-item').forEach(function(el){
            if(el.dataset.read === '0'){
              el.dataset.read = '1';
              el.classList.remove('bg-indigo-50','dark:bg-indigo-900/20','hover:bg-indigo-100','dark:hover:bg-indigo-900/30');
              el.classList.add('opacity-60','hover:opacity-100');
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
