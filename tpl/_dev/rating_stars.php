<head>
    <link rel="stylesheet" type="text/css" href="<?= Root('i/css/_dev/rating_stars.less') ?>" />
    <script type="text/javascript" src="<?= Root('i/js/_dev/rating_stars.js') ?>"></script>
</head>

<section class='rating-widget' data-callback-func="<?= $jsCallback ?>">
    <div class='rating-stars'>
        <ul id='stars'>
            <?php foreach (range(1, 5) as $value): ?>
                <li class='star<?= $rating >= $value ? ' selected' : '' ?>' data-value='<?= $value ?>'>
                    <i class="bi bi-star"></i>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>