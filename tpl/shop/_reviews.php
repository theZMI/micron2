<?php
// Отзывы о товаре: итог (средняя оценка и распределение), список, форма
$distribution = $product->reviews_count > 0 ? (new ShopReviewModel())->ratingDistribution((int)$product->id) : [];
$isEdit       = $myReview && $myReview->isExists();
?>
<section class="home-section product-reviews" id="reviews">
    <div class="section-head"><h2 class="section-title">Отзывы<?= $product->reviews_count ? ' <span class="text-secondary">' . $product->reviews_count . '</span>' : '' ?></h2></div>
    <?= $reviewMsg ?>
    <div class="product-reviews-layout">
        <aside class="product-reviews-summary">
            <?php if ($product->reviews_count > 0): ?>
                <div class="product-reviews-avg"><?= number_format($product->rating, 1, ',', '') ?></div>
                <?php IncludeCom('shop/_rating', ['rating' => $product->rating, 'size' => 'lg']) ?>
                <div class="text-secondary small mt-1"><?= $product->reviews_count ?> <?= Plural($product->reviews_count, 'отзыв', 'отзыва', 'отзывов') ?></div>
                <div class="product-reviews-bars">
                    <?php foreach ($distribution as $stars => $count): ?>
                        <div><span><?= $stars ?> <i class="bi bi-star-fill"></i></span><span class="bar"><span style="width: <?= round($count / max(1, $product->reviews_count) * 100) ?>%"></span></span><span><?= $count ?></span></div>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <p class="text-secondary mb-0">Отзывов пока нет. Будьте первым!</p>
            <?php endif ?>
        </aside>
        <div class="product-reviews-main">
            <?php if ($myReview === null): ?>
                <div class="product-review-login">
                    <a href="<?= SiteRoot('user/login&back_url=' . urlencode($product->url() . '#reviews')) ?>" class="btn btn-outline-dark">Войдите, чтобы оставить отзыв</a>
                </div>
            <?php else: ?>
                <details class="product-review-form"<?= $reviewMsg && !Get('review') ? ' open' : '' ?>>
                    <summary class="btn btn-outline-dark"><?= $isEdit ? 'Изменить мой отзыв' : 'Написать отзыв' ?></summary>
                    <form action="<?= $product->url() ?>#reviews" method="post">
                        <input type="hidden" name="a" value="review">
                        <?php if ($isEdit && $myReview->status !== ShopReviewModel::STATUS_PUBLISHED): ?>
                            <p class="small text-secondary">Статус вашего отзыва: <?= mb_strtolower(ShopReviewModel::getStatuses()[$myReview->status] ?? '') ?></p>
                        <?php endif ?>
                        <div class="mb-3">
                            <div class="form-label">Оценка *</div>
                            <div class="rating-input" role="radiogroup" aria-label="Оценка">
                                <?php foreach (range(5, 1) as $i): ?>
                                    <input type="radio" name="rating" value="<?= $i ?>" id="rating-<?= $i ?>" <?= $reviewForm['rating'] === $i ? 'checked' : '' ?> required>
                                    <label for="rating-<?= $i ?>" title="<?= $i ?> из 5"><i class="bi bi-star-fill"></i></label>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="review-name">Имя *</label>
                            <input type="text" name="author_name" id="review-name" class="form-control" value="<?= $reviewForm['name'] ?>" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="review-text">Отзыв *</label>
                            <textarea name="text" id="review-text" class="form-control" rows="4" minlength="10" maxlength="5000" required placeholder="Что понравилось, что нет, подошёл ли размер"><?= $reviewForm['text'] ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark">Отправить</button>
                    </form>
                </details>
            <?php endif ?>

            <?php foreach ($reviews as $review): ?>
                <article class="product-review">
                    <header>
                        <b><?= $review->author_name ?></b>
                        <?php if ($review->is_buyer): ?><span class="product-review-buyer"><i class="bi bi-patch-check-fill"></i> Покупатель</span><?php endif ?>
                        <time class="text-secondary small ms-auto" datetime="<?= date('c', $review->create_time) ?>"><?= date('d.m.Y', $review->create_time) ?></time>
                    </header>
                    <?php IncludeCom('shop/_rating', ['rating' => (float)$review->rating]) ?>
                    <p><?= nl2br($review->text) ?></p>
                </article>
            <?php endforeach ?>
        </div>
    </div>
</section>
