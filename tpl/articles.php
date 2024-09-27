<div class="container">
    <div class="row">
        <div class="col mt-5 mb-5">
            <h1>Блог</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="<?= SiteRoot() ?>"><i class="bi bi-house-fill"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Блог</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row mb-5">
        <?php foreach ($articles as $article): ?>
            <div class="col-12 col-sm-12 col-md-4">
                <?php IncludeCom('article_preview', ['article' => $article]); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>