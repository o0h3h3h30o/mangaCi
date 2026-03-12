<?php

use CodeIgniter\Pager\PagerRenderer;

/** @var PagerRenderer $pager */
$pager->setSurroundCount(2);
?>

<div class="pagination">
  <?php if ($pager->hasPreviousPage()): ?>
    <a href="<?= $pager->getPreviousPage() ?>" class="page-btn page-prev">&lsaquo;</a>
  <?php endif; ?>

  <?php foreach ($pager->links() as $link): ?>
    <a href="<?= $link['uri'] ?>" class="page-btn <?= $link['active'] ? 'active' : '' ?>"><?= $link['title'] ?></a>
  <?php endforeach; ?>

  <?php if ($pager->hasNextPage()): ?>
    <a href="<?= $pager->getNextPage() ?>" class="page-btn page-next">&rsaquo;</a>
  <?php endif; ?>
</div>
