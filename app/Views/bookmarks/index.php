<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<main>
    <div class="max-w-7xl mx-auto px-3 w-full mt-6">

        <div class="justify-between border-2 border-gray-100 dark:border-dark-blue p-3 bg-white dark:bg-fire-blue shadow-md rounded font-semibold dark:shadow-gray-900">

            <!-- Header -->
            <div class="p-4 border-b border-gray-100 dark:border-dark-blue">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl">
                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Following</h2>
                    <span class="text-sm text-gray-400 font-normal" data-bk-count data-n="<?= $total ?>">(<?= $total ?> titles)</span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-3">
                <?php if (empty($bookmarks)): ?>
                <div class="text-center py-16">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    <p class="text-gray-400">You haven't followed any manga yet.</p>
                    <a href="/search" class="mt-3 inline-block text-sm text-blue-500 hover:text-blue-700 transition-colors">Browse manga →</a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <?php foreach ($bookmarks as $manga): ?>
                    <?php
                        $diff = time() - ($manga['update_at'] ?? 0);
                        if ($diff < 60)         $ago = $diff . 's ago';
                        elseif ($diff < 3600)   $ago = floor($diff/60) . 'm ago';
                        elseif ($diff < 86400)  $ago = floor($diff/3600) . 'h ago';
                        elseif ($diff < 604800) $ago = floor($diff/86400) . 'd ago';
                        else                    $ago = floor($diff/604800) . 'w ago';
                    ?>
                    <div class="w-full bk-card">
                        <div class="border rounded-lg border-gray-300 dark:border-dark-blue bg-white dark:bg-fire-blue hover:dark:bg-light-blue-hover hover:dark:border-dark-blue-hover hover:shadow-lg transition-all duration-300 overflow-hidden">
                            <div style="position:relative;line-height:0">
                                <a href="/manga/<?= esc($manga['slug']) ?>" title="<?= esc($manga['name']) ?>">
                                    <div class="cover-frame">
                                        <img src="<?= esc(manga_cover_url($manga)) ?>"
                                             alt="<?= esc($manga['name']) ?>"
                                             class="cover" width="300" height="405" loading="lazy" decoding="async">
                                    </div>
                                </a>
                                <button type="button" title="Remove bookmark"
                                    data-manga-id="<?= (int)$manga['id'] ?>"
                                    style="position:absolute;top:6px;right:6px;z-index:10;width:22px;height:22px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:rgba(248,113,113,0.85);color:#fff;border:none;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,.3)"
                                    onmouseover="this.style.background='rgb(239,68,68)'" onmouseout="this.style.background='rgba(248,113,113,0.85)'"
                                    class="bk-remove">
                                    <svg style="width:11px;height:11px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-3">
                                <h3 class="text-base font-semibold mb-2 truncate">
                                    <a href="/manga/<?= esc($manga['slug']) ?>"
                                        class="text-blue-700 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100 transition-colors">
                                        <?= esc($manga['name']) ?>
                                    </a>
                                </h3>
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <?php if (($manga['chapter_1'] ?? 0) > 0): ?>
                                    <h4 class="flex-shrink-0">
                                        <a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($manga['chap_1_slug'] ?? '') ?>"
                                            class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors text-xs font-medium whitespace-nowrap">
                                            Chapter <?= rtrim(rtrim(number_format((float)$manga['chapter_1'], 1), '0'), '.') ?>
                                        </a>
                                    </h4>
                                    <?php endif; ?>
                                    <div class="flex items-center gap-1 text-gray-400 text-xs flex-shrink min-w-0">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="whitespace-nowrap overflow-hidden text-ellipsis"><?= $ago ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($total > 24): ?>
            <div class="p-3 border-t border-gray-100 dark:border-dark-blue">
                <?= $pager->links() ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</main>
<script>
document.querySelectorAll('.bk-remove').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id = this.dataset.mangaId;
        var card = this.closest('.bk-card');
        fetch('/api/bookmark/toggle', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'manga_id=' + id
        }).then(function(r) { return r.json(); }).then(function(d) {
            if (!d.bookmarked) {
                card.remove();
                var sp = document.querySelector('[data-bk-count]');
                if (sp) { var n = parseInt(sp.dataset.n) - 1; sp.dataset.n = n; sp.textContent = '(' + n + ' titles)'; }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
