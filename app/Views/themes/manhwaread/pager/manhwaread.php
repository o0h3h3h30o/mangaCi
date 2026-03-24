<?php $pager->setSurroundCount(2); ?>

<div class="pagination">
  <?php if ($pager->hasPreviousPage()): ?>
  <a href="<?= $pager->getPreviousPage() ?>" class="page-btn"><i class="fas fa-chevron-left"></i></a>
  <?php endif; ?>

  <?php foreach ($pager->links() as $link): ?>
    <?php if ($link['title'] === '...'): ?>
    <span class="page-btn" style="cursor:default;opacity:.5">...</span>
    <?php else: ?>
    <a href="<?= $link['uri'] ?>" class="page-btn page-num <?= $link['active'] ? 'active' : '' ?>"><?= $link['title'] ?></a>
    <?php endif; ?>
  <?php endforeach; ?>

  <?php if ($pager->hasNextPage()): ?>
  <a href="<?= $pager->getNextPage() ?>" class="page-btn"><i class="fas fa-chevron-right"></i></a>
  <?php endif; ?>
</div>
