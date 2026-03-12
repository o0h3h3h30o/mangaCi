<?= $this->extend('themes/mangahub/layouts/main') ?>

<?= $this->section('content') ?>
<style>
.hs-page { max-width: 1200px; margin: 0 auto; padding: 24px 12px; }
.hs-panel { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
.hs-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.hs-header-left { display: flex; align-items: center; gap: 10px; }
.hs-header-icon { width: 32px; height: 32px; border-radius: 8px; background: var(--accent); display: flex; align-items: center; justify-content: center; }
.hs-header h2 { font-size: 18px; font-weight: 700; color: var(--txt); margin: 0; }
.hs-header .hs-count { font-size: 13px; color: var(--txt3); font-weight: 400; }
.hs-clear { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; color: #ef4444; background: transparent; border: 1px solid #ef4444; border-radius: var(--radius-sm); padding: 4px 12px; cursor: pointer; transition: background 0.2s; }
.hs-clear:hover { background: rgba(239,68,68,0.08); }
.hs-body { padding: 16px; }
.hs-empty { text-align: center; padding: 60px 20px; }
.hs-empty svg { margin: 0 auto 12px; }
.hs-empty p { color: var(--txt3); font-size: 14px; margin: 0 0 12px; }
.hs-empty a { color: var(--accent); font-size: 13px; text-decoration: none; }
.hs-empty a:hover { text-decoration: underline; }
.hs-card-info { padding: 10px 12px; }
.hs-card-info h3 { margin: 0 0 6px; font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.hs-card-info h3 a { color: var(--accent); text-decoration: none; }
.hs-card-info h3 a:hover { text-decoration: underline; }
.hs-time { display: flex; align-items: center; gap: 4px; color: var(--txt3); font-size: 11px; margin-top: 6px; }
</style>

<main>
    <div class="hs-page">
        <div class="hs-panel">

            <div class="hs-header">
                <div class="hs-header-left">
                    <div class="hs-header-icon">
                        <svg style="width:14px;height:14px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polyline points="12 8 12 12 14 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h2>Reading History</h2>
                    <span class="hs-count">(<?= count($history) ?> titles)</span>
                </div>
                <?php if (!empty($history)): ?>
                <button onclick="clearHistory()" class="hs-clear">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Clear All
                </button>
                <?php endif; ?>
            </div>

            <div class="hs-body">
                <?php if (empty($history)): ?>
                <div class="hs-empty">
                    <svg style="width:48px;height:48px;color:var(--txt3)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline points="12 8 12 12 14 14" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p>No reading history yet.</p>
                    <a href="/search">Browse manga &rarr;</a>
                </div>
                <?php else: ?>
                <?php $cdnBase = rtrim(env('CDN_COVER_URL', ''), '/'); ?>
                <div class="manga-grid">
                    <?php foreach ($history as $h):
                        $hCover = (!empty($h['cover']) && str_starts_with($h['cover'], 'http'))
                            ? $h['cover']
                            : ($cdnBase . '/' . ($h['cover'] ?? ($h['manga_slug'] . '-thumb.jpg')));
                        $diff = time() - ($h['time'] ?? 0);
                        if ($diff < 60)         $ago = $diff . 's ago';
                        elseif ($diff < 3600)   $ago = floor($diff/60) . 'm ago';
                        elseif ($diff < 86400)  $ago = floor($diff/3600) . 'h ago';
                        elseif ($diff < 604800) $ago = floor($diff/86400) . 'd ago';
                        else                    $ago = floor($diff/604800) . 'w ago';
                    ?>
                    <div class="manga-card">
                      <a href="/manga/<?= esc($h['manga_slug']) ?>">
                        <div class="manga-cover">
                          <img src="<?= esc($hCover) ?>" alt="<?= esc($h['manga_name']) ?>" loading="lazy">
                          <?php if (!empty($h['chap_slug'])): ?>
                          <span class="ch-badge"><?= esc($h['chap_slug']) ?></span>
                          <?php endif; ?>
                        </div>
                      </a>
                      <div class="manga-name"><a href="/manga/<?= esc($h['manga_slug']) ?>" style="color:inherit;text-decoration:none"><?= esc($h['manga_name']) ?></a></div>
                      <div class="manga-ch">
                        <?php if (!empty($h['chap_slug'])): ?>
                        <a href="/manga/<?= esc($h['manga_slug']) ?>/<?= esc($h['chap_slug']) ?>" style="color:var(--txt3);text-decoration:none"><?= esc($h['chap_slug']) ?></a>
                        <?php endif; ?>
                        <span class="manga-time"><?= $ago ?></span>
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
