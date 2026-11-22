<div class="examples-page">
    <h1 class="page-title">Компоненты</h1>
    <p class="lead text-secondary">Готовые компоненты и примеры работы с движком. Код каждого примера - в <code>src/examples</code> и <code>tpl/examples</code>, а как устроены компоненты - в <a href="<?= SiteRoot('manual&p=components') ?>">документации</a>.</p>

    <?php if (!$isAvailable): ?>
        <div class="examples-note">Живые примеры открываются только в режиме разработки (<code>DEBUG_MODE=1</code> в <code>.env</code>). Здесь - их список.</div>
    <?php endif ?>

    <?php foreach ($groups as $groupName => $items): ?>
        <section class="examples-group">
            <h2><?= $groupName ?></h2>
            <div class="examples-list">
                <?php foreach ($items as $uri => $name): ?>
                    <?php if (str_starts_with($uri, '/')): ?>
                        <a href="<?= SiteRoot(ltrim($uri, '/')) ?>" class="examples-item"><span><?= $name ?></span><i class="bi bi-arrow-right"></i></a>
                    <?php elseif ($isAvailable): ?>
                        <a href="<?= SiteRoot("examples/{$uri}") ?>" class="examples-item"><span><?= $name ?></span><i class="bi bi-arrow-right"></i></a>
                    <?php else: ?>
                        <div class="examples-item"><span><?= $name ?></span></div>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
        </section>
    <?php endforeach ?>
</div>
