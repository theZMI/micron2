<div id="breadcrumb">
    <div class="container">
        <div class="row">
            <div class="col">
                <?php if (count($steps) === 1): ?>
                <?php else: ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <?php foreach ($steps as $step): ?>
                                <li class="breadcrumb-item <?= $step['css'] ?>">
                                    <?php if (empty($step['link'])): ?>
                                        <?= $step['text'] ?>
                                    <?php else: ?>
                                        <a href="<?= $step['link'] ?>"><?= $step['text'] ?></a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>