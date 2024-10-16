<div class="container-fluid">
    <div class="row">
        <div class="col">

            <h1 class="mt-4 mb-4">
                <span>
                    Редактирование страниц сайта <span class="badge bg-secondary text-uppercase"><?= $lang ?></span>
                </span>
            </h1>
            <div id="list-page">
                <?= RecursiveUl($dirs, array('class' => 'list-unstyled'), 'CreateLinkFromElement', $exceptions) ?>
            </div>

        </div>
    </div>
</div>