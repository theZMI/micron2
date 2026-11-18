<nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbarMainMenu">
    <div class="<?= $w100 ? 'container-fluid' : 'container' ?>">
        <?php if (!empty($logo)): ?>
            <a class="navbar-brand <?= $logo['css'] ?>" href="<?= $logo['link'] ?>" title="<?= $logo['title'] ?>">
                <?= $logo['logo'] ?>
            </a>
        <?php endif ?>
        <?php if (!empty($mobileIcons)): ?>
            <div class="navbar-mobile-icons d-lg-none ms-auto"><?= $mobileIcons ?></div>
        <?php endif ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainMenuContent" aria-controls="navbarMainMenuContent" aria-expanded="false" aria-label="Toggle navigation" id="navbarMainMenuToggleButton">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMainMenuContent">
            <?php IncludeCom('_dev/menu/sub_menu', ['menu' => $menu, 'css' => 'me-auto menu-left-part']) ?>
            <?php if ($incSearch): ?>
                <form class="d-none search-panel" action="<?= $searchUrl ?>" method="get" role="search">
                    <div class="d-block d-lg-none mt-5"></div>
                    <div class="input-group">
                        <input type="search" name="q" class="form-control" placeholder="Что хотите найти?" value="<?= Get('q', '') ?>" aria-label="Поиск"/>
                        <span class="input-group-text search-panel-button-toggle rounded-pill"><i class="bi bi-x-lg"></i></span>
                    </div>
                    <div class="d-block d-lg-none mt-5"></div>
                </form>
            <?php endif ?>
            <?php IncludeCom('_dev/menu/sub_menu', ['menu' => $rightMenu, 'css' => 'navbar-nav-right mr-auto menu-right-part']) ?>
            <?php if (!empty($afterRight)): ?>
                <div class="navbar-after-right d-none d-lg-flex align-items-center"><?= $afterRight ?></div>
            <?php endif ?>
        </div>
    </div>
</nav>