<?php // Звёзды оценки: $rating (0-5, дробное), $count - количество отзывов (необязательно), $size - sm|lg ?>
<span class="rating-stars-view<?= !empty($size) ? ' rating-stars-' . $size : '' ?>" title="Оценка <?= number_format($rating, 1, ',', '') ?> из 5">
    <?php foreach (range(1, 5) as $i): ?><i class="bi bi-star<?= $rating >= $i - 0.25 ? '-fill' : ($rating >= $i - 0.75 ? '-half' : '') ?>"></i><?php endforeach ?>
    <?php if (isset($count)): ?><span class="rating-stars-text"><?= number_format($rating, 1, ',', '') ?><?= $count !== null ? ' · ' . $count . ' ' . Plural($count, 'отзыв', 'отзыва', 'отзывов') : '' ?></span><?php endif ?>
</span>
