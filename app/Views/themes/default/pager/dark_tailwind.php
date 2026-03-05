<?php

/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<?php if ($pager->hasPreviousPage() || $pager->hasNextPage()): ?>
<nav aria-label="Pagination" class="flex justify-center mt-6 mb-4">
  <ul class="inline-flex items-center gap-1 text-sm">

    <?php if ($pager->hasPreviousPage()): ?>
      <li>
        <a href="<?= $pager->getFirst() ?>" class="px-3 py-2 rounded-lg bg-gray-700/50 text-gray-300 hover:bg-gray-600 transition-colors">
          &laquo;
        </a>
      </li>
      <li>
        <a href="<?= $pager->getPreviousPage() ?>" class="px-3 py-2 rounded-lg bg-gray-700/50 text-gray-300 hover:bg-gray-600 transition-colors">
          &lsaquo;
        </a>
      </li>
    <?php endif; ?>

    <?php foreach ($pager->links() as $link): ?>
      <li>
        <a href="<?= $link['uri'] ?>"
           class="px-3 py-2 rounded-lg transition-colors <?= $link['active'] ? 'bg-blue-600 text-white font-semibold' : 'bg-gray-700/50 text-gray-300 hover:bg-gray-600' ?>">
          <?= $link['title'] ?>
        </a>
      </li>
    <?php endforeach; ?>

    <?php if ($pager->hasNextPage()): ?>
      <li>
        <a href="<?= $pager->getNextPage() ?>" class="px-3 py-2 rounded-lg bg-gray-700/50 text-gray-300 hover:bg-gray-600 transition-colors">
          &rsaquo;
        </a>
      </li>
      <li>
        <a href="<?= $pager->getLast() ?>" class="px-3 py-2 rounded-lg bg-gray-700/50 text-gray-300 hover:bg-gray-600 transition-colors">
          &raquo;
        </a>
      </li>
    <?php endif; ?>

  </ul>
</nav>
<?php endif; ?>
