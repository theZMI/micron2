<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMainMenu" aria-labelledby="offcanvasMainMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasMainMenuLabel">Каталог:</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
    </div>
    <div class="offcanvas-body">
        <div class="offcanvas-menu-list">
            <ul class="list-unstyled">
                <li><a href="#"><span>Хиты продаж</span></a></li>
                <li><a href="#" class="text-danger"><span>SALE</span></a></li>
                <li><a href="#"><span>Костюмы</span></a></li>
                <li><a href="#"><span>Платья</span></a></li>
                <li><a href="#"><span>Комбинезоны</span></a></li>
                <li><a href="#"><span>Боди</span></a></li>
                <li><a href="#"><span>Топы / футболки</span></a></li>
                <li><a href="#"><span>Рубашки / блузы</span></a></li>
                <li><a href="#"><span>Брюки / леггинсы</span></a></li>
                <li><a href="#"><span>Верхняя одежда</span></a></li>
                <li><a href="#"><span>Пляжная одежда</span></a>
            </ul>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasUserMenu" aria-labelledby="offcanvasUserMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasUserMenuLabel">Личный кабинет:</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
    </div>
    <div class="offcanvas-body">
        <div class="offcanvas-menu-list">
            <ul class="list-unstyled">
                <li><a href="#"><span>Вход / регистрация</span></a></li>
                <li><a href="#"><span>Мои заказы</span></a></li>
                <li><a href="#"><span>Отследить заказ</span></a></li>
                <li><a href="#"><span>Помощь</span></a></li>
            </ul>
        </div>
    </div>
</div>

<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light" id="navbarMainMenu">
    <div class="container-fluid">
        <a class="navbar-brand pe-3" href="<?= SiteRoot() ?>">
            Balttara
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainMenuContent"
                aria-controls="navbarMainMenuContent" aria-expanded="false" aria-label="Toggle navigation"
                id="navbarMainMenuToggleButton">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMainMenuContent">
            <ul class="navbar-nav me-auto menu-left-part">
                <li class="nav-item active">
                    <a class="nav-link" data-bs-toggle="offcanvas" href="#offcanvasMainMenu" role="button"
                       aria-controls="offcanvasMainMenu"><span>Каталог</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><span>Хиты продаж</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link is-important" href="#"><span>SALE</span></a>
                </li>
            </ul>
            <form class="d-none search-panel">
                <div class="d-block d-lg-none mt-5"></div>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Что хотите найти?"/>
                    <span class="input-group-text search-panel-button-toggle rounded-pill"><i
                                class="bi bi-x-lg"></i></span>
                </div>
                <div class="d-block d-lg-none mt-5"></div>
            </form>
            <ul class="navbar-nav navbar-nav-right mr-auto menu-right-part">
                <li class="nav-item d-grid">
                    <button class="btn btn-link nav-link position-relative search-panel-button-toggle">
                        <span class="d-inline d-lg-none">Поиск</span>
                        <i class="bi bi-search me-2 d-none d-lg-inline"></i>
                    </button>
                </li>
                <li class="nav-item">
                    <a class="nav-link position-relative" data-bs-toggle="offcanvas" href="#offcanvasUserMenu"
                       role="button" aria-controls="offcanvasUserMenu">
                        <span class="d-inline d-lg-none">Личный кабинет</span>
                        <i class="bi bi-lock-fill me-2 d-none d-lg-inline"></i>
                        <span class="position-absolute top-50 translate-middle badge border border-light rounded-pill bg-danger">
                            +1
                            <span class="visually-hidden">Уведомления</span>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link position-relative" data-bs-toggle="offcanvas" href="#offcanvasUserLikesMenu"
                       role="button" aria-controls="offcanvasUserLikesMenu">
                        <span class="d-inline d-lg-none">Понравилось</span>
                        <i class="bi bi-suit-heart-fill me-2 d-none d-lg-inline"></i>
                        <span class="position-absolute top-50 translate-middle badge border border-light rounded-pill bg-danger">
                            18
                            <span class="visually-hidden">Товаров в понравилось</span>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link position-relative last" data-bs-toggle="offcanvas" href="#offcanvasUserCartMenu"
                       role="button" aria-controls="offcanvasUserCartMenu">
                        <span class="d-inline d-lg-none">Корзина</span>
                        <i class="bi bi-cart-fill me-2 d-none d-lg-inline"></i>
                        <span class="position-absolute top-50 translate-middle badge border border-light rounded-pill bg-danger">
                            4
                            <span class="visually-hidden">Товаров в корзине</span>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasUserLikesMenu"
     aria-labelledby="offcanvasUserLikesMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasUserLikesMenuLabel">Избранное</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <div class="cart-page-empty-wrapper cart-page-empty-with-centering" id="i-favorites"
                 style="display: block;">
                <?php IncludeCom('_favorites') ?>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasUserCartMenu"
     aria-labelledby="offcanvasUserCartMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasUserCartMenuLabel">Корзина</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
    </div>
    <div class="offcanvas-body">
        <div class="super-cart-wrapper cart-page-empty-with-centering">
            <?php IncludeCom('shop/_cart') ?>
        </div>
    </div>
</div>