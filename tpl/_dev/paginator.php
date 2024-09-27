<ul class="pagination <?= $css ?? '' ?>">
    <?php if (empty($arrowLeft)): ?>
        <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-double-left"></i></span></li>
    <?php else: ?>
        <li class="page-item"><a class="page-link" href="<?= $arrowLeft ?>" title="Previous page"><i class="bi bi-chevron-double-left"></i></a></li>
    <?php endif; ?>

    <?php foreach ($pagesLeft as $p): ?>
        <li class="page-item"><a class="page-link" href="<?= $p["href"] ?>" title="Go to page <?= $p["page"] ?>"><?= $p["page"] ?></a></li>
    <?php endforeach; ?>

    <?php if (!empty($pagesLeft)): ?>
        <li class="page-item"><span>..</span></li>
    <?php endif; ?>


    <?php foreach ($pagesCenter as $p): ?>
        <?php if ($p["is_active"]): ?>
            <li class="page-item active"><span class="page-link" title="Current page is <?= $p["page"] ?>"><?= $p["page"] ?></span></li>
        <?php else: ?>
            <li class="page-item"><a class="page-link" href="<?= $p["href"] ?>" title="Go to page <?= $p["page"] ?>"><?= $p["page"] ?></a></li>
        <?php endif; ?>
    <?php endforeach; ?>


    <?php if (!empty($pagesRight)): ?>
        <li class="page-item"><span class="page-link">..</span></li>
    <?php endif; ?>

    <?php foreach ($pagesRight as $p): ?>
        <li class="page-item"><a class="page-link" href="<?= $p["href"] ?>" title="Go to page <?= $p["page"] ?>"><?= $p["page"] ?></a></li>
    <?php endforeach; ?>

    <?php if (empty($arrowRight)): ?>
        <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-double-right"></i></span></li>
    <?php else: ?>
        <li class="page-item"><a class="page-link" href="<?= $arrowRight ?>" title="Next page"><i class="bi bi-chevron-double-right"></i></a></li>
    <?php endif; ?>
</ul>