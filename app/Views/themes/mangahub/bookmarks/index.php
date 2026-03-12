<?= $this->extend('themes/mangahub/layouts/main') ?>

<?= $this->section('content') ?>
<style>
.bk-page { max-width: 1200px; margin: 0 auto; padding: 24px 12px; }
.bk-panel { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
.bk-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; }
.bk-header-icon { width: 32px; height: 32px; border-radius: 8px; background: var(--accent); display: flex; align-items: center; justify-content: center; }
.bk-header h2 { font-size: 18px; font-weight: 700; color: var(--txt); margin: 0; }
.bk-header .bk-count { font-size: 13px; color: var(--txt3); font-weight: 400; }
.bk-body { padding: 16px; }
.bk-empty { text-align: center; padding: 60px 20px; }
.bk-empty svg { margin: 0 auto 12px; }
.bk-empty p { color: var(--txt3); font-size: 14px; margin: 0 0 12px; }
.bk-empty a { color: var(--accent); font-size: 13px; text-decoration: none; }
.bk-empty a:hover { text-decoration: underline; }
.bk-footer { padding: 12px 20px; border-top: 1px solid var(--border); }
.bk-remove-btn { position: absolute; top: 6px; right: 6px; z-index: 10; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(232,25,44,0.85); color: #fff; border: none; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,.3); }
.bk-remove-btn:hover { background: var(--accent); }
</style>

<main>
    <div class="bk-page">
        <div class="bk-panel">

            <div class="bk-header">
                <div class="bk-header-icon">
                    <svg style="width:14px;height:14px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </div>
                <h2>Following</h2>
                <span class="bk-count" data-bk-count data-n="<?= $total ?>">(<?= $total ?> titles)</span>
            </div>

            <div class="bk-body">
                <?php if (empty($bookmarks)): ?>
                <div class="bk-empty">
                    <svg style="width:48px;height:48px;color:var(--txt3)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    <p>You haven't followed any manga yet.</p>
                    <a href="/search">Browse manga &rarr;</a>
                </div>
                <?php else: ?>
                <div class="manga-grid">
                    <?php foreach ($bookmarks as $manga): ?>
                    <?php
                        $diff = time() - ($manga['update_at'] ?? 0);
                        if ($diff < 60)         $ago = $diff . 's ago';
                        elseif ($diff < 3600)   $ago = floor($diff/60) . 'm ago';
                        elseif ($diff < 86400)  $ago = floor($diff/3600) . 'h ago';
                        elseif ($diff < 604800) $ago = floor($diff/86400) . 'd ago';
                        else                    $ago = floor($diff/604800) . 'w ago';
                    ?>
                    <div class="manga-card bk-card">
                      <a href="/manga/<?= esc($manga['slug']) ?>">
                        <div class="manga-cover" style="position:relative">
                          <img src="<?= esc(manga_cover_url($manga)) ?>" alt="<?= esc($manga['name']) ?>" loading="lazy">
                          <?php if (!empty($manga['chapter_1'])): ?>
                          <span class="ch-badge">Ch.<?= esc($manga['chapter_1']) ?></span>
                          <?php endif; ?>
                          <button type="button" title="Remove bookmark"
                              data-manga-id="<?= (int)$manga['id'] ?>"
                              class="bk-remove-btn bk-remove"
                              onclick="event.preventDefault();event.stopPropagation();">
                              <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                              </svg>
                          </button>
                        </div>
                      </a>
                      <div class="manga-name"><a href="/manga/<?= esc($manga['slug']) ?>" style="color:inherit;text-decoration:none"><?= esc($manga['name']) ?></a></div>
                      <div class="manga-ch">
                        <?php if (!empty($manga['chap_1_slug'])): ?>
                        <a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($manga['chap_1_slug']) ?>" style="color:var(--txt3);text-decoration:none">Chapter <?= esc($manga['chapter_1'] ?? '') ?></a>
                        <?php else: ?>
                        <span>Chapter <?= esc($manga['chapter_1'] ?? '') ?></span>
                        <?php endif; ?>
                        <span class="manga-time"><?= $ago ?></span>
                      </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($total > 24): ?>
            <div class="bk-footer">
                <?= $pager->links('default', 'mangahub_full') ?>
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
