<?php

// Страница товара: /shop/p/<id>-<название> (маршрут в core/init/router.php). Старый адрес shop/product?id=N - 301 на новый
$product = new ShopProductModel((int)($productId ?? 0) ?: (intval(Get('id')) ?: null));
if (!$product->isExists() || !$product->is_active) {
    IncludeCom('404');
    ExitCom();
    return;
}
if (!isset($productSlug) || $productSlug !== $product->slug()) {
    $query = array_diff_key($_GET, ['id' => 1, 'micron_query' => 1]);
    UrlRedirect::go($product->url() . ($query ? '?' . http_build_query($query) : ''), 301);
}

$msg = '';

// «Сообщить о поступлении»
if (Post('a') === 'stock_subscribe' && !$product->isAvailable()) {
    $email = trim(html_entity_decode((string)Post('email')));
    if (IsValidEmail($email)) {
        ShopStockSubscriptionModel::subscribe((int)$product->id, htmlspecialchars($email));
        UrlRedirect::go($product->url() . '?subscribed=1'); // Сообщение - через параметр, без сессии
    }
    $msg = MsgErr('Укажите правильный e-mail');
}

// Отзыв: только вошедший пользователь, один на товар (можно исправить свой)
$reviewsEnabled = ShopReviewModel::isEnabled();
$myReview       = $reviewsEnabled && IS_USER_AUTH ? ShopReviewModel::findByUser((int)$product->id, (int)$g_user->id) : null;
$reviewForm     = [
    'rating' => (int)Post('rating', $myReview && $myReview->isExists() ? $myReview->rating : 0),
    'text'   => (string)Post('text', $myReview && $myReview->isExists() ? $myReview->text : ''),
    'name'   => (string)Post('author_name', $myReview && $myReview->isExists() ? $myReview->author_name : (IS_USER_AUTH ? trim((string)$g_user->full_name) : '')),
];
$reviewMsg = '';
if (Post('a') === 'review' && $myReview) {
    $errs = [];
    if ($reviewForm['rating'] < 1 || $reviewForm['rating'] > 5) {
        $errs[] = 'Поставьте оценку от 1 до 5 звёзд';
    }
    if (mb_strlen(trim($reviewForm['text'])) < 10) {
        $errs[] = 'Напишите хотя бы пару слов о товаре';
    }
    if (trim($reviewForm['name']) === '') {
        $errs[] = 'Укажите имя - оно будет видно в отзыве';
    }
    if ($errs) {
        $reviewMsg = MsgErr(implode('<br>', $errs));
    } else {
        $isModeration          = !empty(Config('shop')['reviews']['moderation']);
        $myReview->product_id  = (int)$product->id;
        $myReview->user_id     = (int)$g_user->id;
        $myReview->author_name = mb_substr(trim($reviewForm['name']), 0, 100);
        $myReview->rating      = $reviewForm['rating'];
        $myReview->text        = mb_substr(trim($reviewForm['text']), 0, 5000);
        $myReview->is_buyer    = ShopReviewModel::isBuyer((int)$product->id, (int)$g_user->id);
        $myReview->status      = $isModeration ? ShopReviewModel::STATUS_NEW : ShopReviewModel::STATUS_PUBLISHED;
        $myReview->flush();
        ShopReviewModel::recalcProduct((int)$product->id);

        if ($isModeration && ($managers = Config('shop')['manager_emails'])) {
            try {
                $link = SiteRoot('_admin/shop/reviews');
                SendMail($managers, "Новый отзыв: {$product->name}", "<p>{$myReview->author_name}, {$myReview->rating} из 5:</p><p>" . nl2br($myReview->text) . "</p><p><a href=\"{$link}\">Модерация отзывов</a></p>");
            } catch (Throwable $e) {
                ToLog('Shop review: can\'t send email: ' . $e->getMessage());
            }
        }
        UrlRedirect::go($product->url() . '?review=' . ($isModeration ? 'moderation' : 'published') . '#reviews');
    }
}
if (Get('subscribed')) {
    $msg = MsgOk('Напишем вам, как только товар появится');
}
if (Get('review')) {
    $reviewMsg = MsgOk(Get('review') === 'moderation' ? 'Спасибо! Отзыв появится после проверки' : 'Спасибо за отзыв!');
}
$reviews   = $reviewsEnabled ? (new ShopReviewModel())->getPublished((int)$product->id) : [];

$boughtTogether = $product->getBoughtTogether(4);
$similar        = $product->getSimilar(4);
$recentlyViewed = ShopRecentlyViewed::getProducts($product->id, 4);
ShopRecentlyViewed::add($product->id);

BreadCrumb::getInstance()->add(['text' => 'Каталог', 'link' => SiteRoot('shop')]);
foreach ($product->category_id ? ShopCategoryModel::path($product->category_id) : [] as $row) {
    BreadCrumb::getInstance()->add(['text' => $row['name'], 'link' => SiteRoot("shop/c/{$row['slug']}")]);
}
BreadCrumb::getInstance()->add(['text' => $product->name]);

// SEO
$seoDescription          = mb_strimwidth(trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode((string)$product->description)))), 0, 200, '…');
$g_lang['m_title']       = $product->name . L('m_titlePostfix');
$g_lang['m_description'] = $seoDescription !== '' ? htmlspecialchars($seoDescription) : $product->name;
