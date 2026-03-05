<?php

use CodeIgniter\Pager\PagerRenderer;

/** @var PagerRenderer $pager */
$pager->setSurroundCount(2);
?>

<nav aria-label="Page navigation" class="flex items-center gap-1 flex-wrap">
    <?php if ($pager->hasPrevious()): ?>
    <a href="<?= $pager->getPrevious() ?>"
        class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-fire-blue text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-light-blue transition-colors">
        &laquo; Prev
    </a>
    <?php endif ?>

    <?php foreach ($pager->links() as $link): ?>
    <a href="<?= $link['uri'] ?>"
        class="px-3 py-1.5 text-sm rounded-lg border transition-colors
            <?= $link['active']
                ? 'border-indigo-500 bg-indigo-500 text-white font-semibold'
                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-fire-blue text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-light-blue' ?>">
        <?= $link['title'] ?>
    </a>
    <?php endforeach ?>

    <?php if ($pager->hasNext()): ?>
    <a href="<?= $pager->getNext() ?>"
        class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-fire-blue text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-light-blue transition-colors">
        Next &raquo;
    </a>
    <?php endif ?>
</nav>
