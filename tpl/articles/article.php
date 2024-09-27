<div class="container">
    <div class="row">
        <div class="col mt-5 mb-5">
            <h1><?= $article->name ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="<?= SiteRoot() ?>"><i class="bi bi-house-fill"></i></a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="<?= SiteRoot('articles') ?>">Блог</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $article->name ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-6">
            <i class="bi bi-calendar-event-fill"></i> <?= OutputFormats::dateTimeRu($article->publication_time) ?>
        </div>
        <div class="col-6 text-end">
            <i class="bi bi-eye-fill"></i> 0
            &nbsp;&nbsp;
            <i class="bi bi-chat-fill"></i> 0
        </div>
        <div class="col-12 article">
            <div class="text-center">
                <img src="<?= $article->preview_image_url ?>" alt="<?= $article->name ?>" class="w-100 rounded mt-4 mb-4">
            </div>
            <div><?= $article->text ?></div>
            <br><br>
            <div style="display: flex; flex-direction: row; justify-content: right; align-items: center;">
                <img src="<?= Root('upl/users/' . $article->author_avatar) ?>" alt="" class="rounded-circle" style="width: 65px; height: 65px;"/>
                <span class="ps-3"><?= $article->author_name ?></span>
            </div>
        </div>
    </div>
</div>