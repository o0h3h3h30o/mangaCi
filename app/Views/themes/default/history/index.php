<?= $this->extend('themes/default/layouts/main') ?>

<?= $this->section('content') ?>
<main>
    <div class="max-w-7xl mx-auto px-3 w-full mt-6">

        <div class="justify-between border-2 border-gray-100 dark:border-dark-blue p-3 bg-white dark:bg-fire-blue shadow-md rounded font-semibold dark:shadow-gray-900">

            <!-- Header -->
            <div class="p-4 border-b border-gray-100 dark:border-dark-blue">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl">
                            <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="12 8 12 12 14 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Reading History</h2>
                        <span class="text-sm text-gray-400 font-normal">(<?= count($history) ?> titles)</span>
                    </div>
                    <?php if (!empty($history)): ?>
                    <button onclick="clearHistory()"
                        class="text-xs text-red-400 hover:text-red-600 transition-colors font-normal flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Clear All
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content -->
            <div class="p-3">
                <?php if (empty($history)): ?>
                <div class="text-center py-16">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline points="12 8 12 12 14 14" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="text-gray-400">No reading history yet.</p>
                    <a href="/search" class="mt-3 inline-block text-sm text-blue-500 hover:text-blue-700 transition-colors">Browse manga →</a>
                </div>
                <?php else: ?>
                <?php $cdnBase = rtrim(env('CDN_COVER_URL', ''), '/'); ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <?php foreach ($history as $h):
                        // Nếu cover là URL đầy đủ (mới) dùng trực tiếp, còn lại fallback CDN (cookie cũ)
                        $hCover = (!empty($h['cover']) && str_starts_with($h['cover'], 'http'))
                            ? $h['cover']
                            : ($cdnBase . '/' . ($h['cover'] ?? ($h['manga_slug'] . '-thumb.jpg')));
                    ?>
                    <?php
                        $diff = time() - ($h['time'] ?? 0);
                        if ($diff < 60)         $ago = $diff . 's ago';
                        elseif ($diff < 3600)   $ago = floor($diff/60) . 'm ago';
                        elseif ($diff < 86400)  $ago = floor($diff/3600) . 'h ago';
                        elseif ($diff < 604800) $ago = floor($diff/86400) . 'd ago';
                        else                    $ago = floor($diff/604800) . 'w ago';
                    ?>
                    <div class="w-full">
                        <div class="border rounded-lg border-gray-300 dark:border-dark-blue bg-white dark:bg-fire-blue
                            hover:dark:bg-light-blue-hover hover:dark:border-dark-blue-hover
                            hover:shadow-lg transition-all duration-300 overflow-hidden">
                            <div class="relative">
                                <a href="/manga/<?= esc($h['manga_slug']) ?>" title="<?= esc($h['manga_name']) ?>">
                                    <div class="cover-frame">
                                        <img src="<?= esc($hCover) ?>"
                                             alt="<?= esc($h['manga_name']) ?>"
                                             class="cover" width="300" height="405" loading="lazy" decoding="async">
                                    </div>
                                </a>
                            </div>
                            <div class="p-3">
                                <h3 class="text-base font-semibold mb-1 truncate">
                                    <a href="/manga/<?= esc($h['manga_slug']) ?>"
                                        class="text-blue-700 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100 transition-colors">
                                        <?= esc($h['manga_name']) ?>
                                    </a>
                                </h3>
                                <a href="/manga/<?= esc($h['manga_slug']) ?>/<?= esc($h['chap_slug']) ?>"
                                   class="inline-block bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded-full text-xs font-medium hover:bg-indigo-200 dark:hover:bg-indigo-800 transition-colors">
                                    <?= esc($h['chap_slug']) ?>
                                </a>
                                <div class="flex items-center gap-1 text-gray-400 text-xs mt-1.5">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span><?= $ago ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

<script>
function clearHistory() {
    if (!confirm('Clear all reading history?')) return;
    document.cookie = '_history=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    window.location.reload();
}
</script>
<?= $this->endSection() ?>
