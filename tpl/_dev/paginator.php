<ul class="pagination">
    <?php if (empty($arrowLeft)): ?>
        <li class="disabled"><span>&laquo;</span></li>
    <?php else: ?>
        <li><a href="<?= $arrowLeft ?>" title="Previous page">&laquo;</a></li>
    <?php endif; ?>

    <?php foreach ($pagesLeft as $p): ?>
        <li><a href="<?= $p["href"] ?>" title="Go to page <?= $p["page"] ?>"><?= $p["page"] ?></a></li>
    <?php endforeach; ?>

    <?php if (!empty($pagesLeft)): ?>
        <li><span>..</span></li>
    <?php endif; ?>


    <?php foreach ($pagesCenter as $p): ?>
        <?php if ($p["is_active"]): ?>
            <li class="active"><span title="Current page is <?= $p["page"] ?>"><?= $p["page"] ?></span></li>
        <?php else: ?>
            <li><a href="<?= $p["href"] ?>" title="Go to page <?= $p["page"] ?>"><?= $p["page"] ?></a></li>
        <?php endif; ?>
    <?php endforeach; ?>


    <?php if (!empty($pagesRight)): ?>
        <li><span>..</span></li>
    <?php endif; ?>

    <?php foreach ($pagesRight as $p): ?>
        <li><a href="<?= $p["href"] ?>" title="Go to page <?= $p["page"] ?>"><?= $p["page"] ?></a></li>
    <?php endforeach; ?>

    <?php if (empty($arrowRight)): ?>
        <li class="disabled"><span>&raquo;</span></li>
    <?php else: ?>
        <li><a href="<?= $arrowRight ?>" title="Next page">&raquo;</a></li>
    <?php endif; ?>
</ul>
