<div class="manual-page">
    <aside class="manual-nav">
        <details class="manual-nav-box" open data-manual-nav>
            <summary><i class="bi bi-list"></i> Разделы: <span><?= $pages[$page] ?></span></summary>
            <nav>
                <?php foreach ($groups as $groupName => $groupPages): ?>
                    <div class="manual-nav-group"><?= $groupName ?></div>
                    <?php foreach ($groupPages as $p => $name): ?>
                        <a href="<?= $url($p) ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $name ?></a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </nav>
        </details>
    </aside>

    <article class="manual-content">
        <?php IncludeCom("manual/_pages/{$page}", ['url' => $url]) ?>

        <nav class="manual-pager">
            <?php if ($prev): ?>
                <a href="<?= $url($prev) ?>" class="manual-pager-prev">
                    <small>Назад</small>
                    <span><i class="bi bi-arrow-left"></i> <?= $pages[$prev] ?></span>
                </a>
            <?php endif; ?>
            <?php if ($next): ?>
                <a href="<?= $url($next) ?>" class="manual-pager-next">
                    <small>Далее</small>
                    <span><?= $pages[$next] ?> <i class="bi bi-arrow-right"></i></span>
                </a>
            <?php endif; ?>
        </nav>
    </article>
</div>

<bottom>
    <script>
        // На телефоне список разделов свёрнут, на компьютере - всегда открыт
        (function () {
            var box = document.querySelector('[data-manual-nav]');
            if (box && window.matchMedia('(max-width: 991.98px)').matches) {
                box.open = false;
            }
        })();
    </script>
</bottom>
