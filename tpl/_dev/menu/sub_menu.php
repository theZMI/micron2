<?php $count = count($menu);
if ($count): ?>
    <ul class="navbar-nav <?= $css ?>">
        <?php foreach ($menu as $v): ?>
            <?php --$count; ?>
            <li class="nav-item <?= $v['css'] ?>">
                <?php $linkCss = ($count === 0 ? ' last' : '') . " {$v['linkCss']}"; ?>
                <a href="<?= $v['link'] ?>" title="<?= $v['label'] ?>" <?= $v['html'] ?> <?= count($v['list']) ? 'class="nav-link dropdown' . $linkCss . '" role="button" data-bs-toggle="dropdown" aria-expanded="false"' : 'class="nav-link' . $linkCss . '"' ?>>
                    <span>
                        <?= $v['name'] ?>
                        <?= count($v['list']) ? '<i class="bi bi-caret-down-fill"></i>' : '' ?>
                    </span>
                </a>
                <?php if (count($v['list'])): ?>
                    <ul class="dropdown-menu <?= stripos($css, 'navbar-nav-right') !== false ? 'dropdown-menu-end' : '' ?>">
                        <?php foreach ($v['list'] as $v2): ?>
                            <?php if ($v2 === 'divider'): ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            <?php else: ?>
                                <li><a href="<?= $v2['link'] ?>" title="<?= $v2['label'] ?>" class="dropdown-item"><span><?= $v2['name'] ?></span></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>