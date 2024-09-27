<a href="<?= SiteRoot("articles/article&id={$article->id}") ?>" class="card" style="display: block;">
    <img src="<?= $article->preview_image_url ?>" alt="<?= $article->name ?>" class="card-img-top">
    <div class="card-body text-start">
        <h5 class="card-title"><?= $article->name ?></h5>
        <span class="text-muted"><?= OutputFormats::dateTimeRu($article->publication_time) ?></span>
    </div>
</a>