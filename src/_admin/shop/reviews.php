<?php

// Отзывы о товарах: модерация (опубликовать / отклонить / удалить)
$review = new ShopReviewModel(intval(Post('id', Get('id'))) ?: null);
if ($review->isExists()) {
    switch (Post('a')) {
        case 'publish':
            $review->setStatus(ShopReviewModel::STATUS_PUBLISHED);
            break;
        case 'reject':
            $review->setStatus(ShopReviewModel::STATUS_REJECTED);
            break;
        case 'delete':
            $review->delete();
            break;
    }
    if (Post('a')) {
        UrlRedirect::go(GetCurUrl('id=' . M_DELETE_PARAM));
    }
}

$statuses = ShopReviewModel::getStatuses();
// По умолчанию - на модерации, если такие есть
$status   = match (true) {
    array_key_exists((string)Get('status', ''), $statuses) => (string)Get('status'),
    Get('status') === 'all'                                => '',
    default                                                => ShopReviewModel::countNew() ? ShopReviewModel::STATUS_NEW : '',
};
$list     = (new ShopReviewModel())->getAll($status ?: null);
