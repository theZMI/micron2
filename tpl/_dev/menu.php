<nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbarMainMenu">
    <div class="<?= $w100 ? 'container-fluid' : 'container' ?>">
        <?php if (!empty($logo)): ?>
            <a class="navbar-brand <?= $logo['css'] ?>" href="<?= $logo['link'] ?>" title="<?= $logo['title'] ?>">
                <?= $logo['logo'] ?>
            </a>
        <?php endif ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainMenuContent" aria-controls="navbarMainMenuContent" aria-expanded="false" aria-label="Toggle navigation" id="navbarMainMenuToggleButton">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMainMenuContent">
            <?php IncludeCom('_dev/menu/sub_menu', ['menu' => $menu, 'css' => 'me-auto menu-left-part']) ?>
            <?php if ($incSearch): ?>
                <form class="d-none search-panel">
                    <div class="d-block d-lg-none mt-5"></div>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Что хотите найти?"/>
                        <span class="input-group-text search-panel-button-toggle rounded-pill"><i class="bi bi-x-lg"></i></span>
                    </div>
                    <div class="d-block d-lg-none mt-5"></div>
                </form>
            <?php endif ?>
            <?php IncludeCom('_dev/menu/sub_menu', ['menu' => $rightMenu, 'css' => 'navbar-nav-right mr-auto menu-right-part']) ?>
        </div>
    </div>
</nav>