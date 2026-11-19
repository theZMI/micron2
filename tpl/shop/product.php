<?php
// SEO: canonical, Open Graph и разметка schema.org Product (цена, наличие, рейтинг) для поисковиков
$images  = $product->getImages();
$jsonLd  = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Product',
    'name'        => html_entity_decode($product->name, ENT_QUOTES),
    'url'         => $product->url(),
    'description' => $seoDescription,
    'image'       => array_map(fn($i) => ShopAbsUrl($i['url']), $images),
    'offers'      => [
        '@type'         => 'Offer',
        'price'         => ShopRubles($product->price), // В копейках у нас, в разметке - рубли
        'priceCurrency' => Config('shop')['currency'],
        'availability'  => $product->isAvailable() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'url'           => $product->url(),
    ],
];
if ($product->vendor_code !== '') {
    $jsonLd['sku'] = html_entity_decode($product->vendor_code, ENT_QUOTES);
}
if ($product->reviews_count > 0) {
    $jsonLd['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => round($product->rating, 1), 'reviewCount' => $product->reviews_count];
}
?>
<head>
    <link rel="canonical" href="<?= $product->url() ?>">
    <meta property="og:type" content="product">
    <meta property="og:title" content="<?= $product->name ?>">
    <meta property="og:url" content="<?= $product->url() ?>">
    <?php if ($seoDescription !== ''): ?><meta property="og:description" content="<?= htmlspecialchars($seoDescription) ?>"><?php endif ?>
    <?php if ($images): ?><meta property="og:image" content="<?= ShopAbsUrl($images[0]['url']) ?>"><?php endif ?>
    <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
</head>
<div class="shop-page">
    <?php IncludeCom('shop/_product_details', ['product' => $product, 'full' => true, 'msg' => $msg]) ?>

    <?php if ($reviewsEnabled): ?>
        <?php IncludeCom('shop/_reviews', ['product' => $product, 'reviews' => $reviews, 'myReview' => $myReview, 'reviewForm' => $reviewForm, 'reviewMsg' => $reviewMsg]) ?>
    <?php endif ?>

    <?php if ($similar): ?>
        <section class="home-section">
            <div class="section-head"><h2 class="section-title">Похожие товары</h2></div>
            <div class="product-grid product-grid-wide">
                <?php foreach ($similar as $item): ?>
                    <?php IncludeCom('shop/_product_card', ['product' => $item]) ?>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>

    <?php if ($boughtTogether): ?>
        <section class="home-section">
            <div class="section-head"><h2 class="section-title">Покупают вместе</h2></div>
            <div class="product-grid product-grid-wide">
                <?php foreach ($boughtTogether as $item): ?>
                    <?php IncludeCom('shop/_product_card', ['product' => $item]) ?>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>

    <?php if ($recentlyViewed): ?>
        <section class="home-section">
            <div class="section-head"><h2 class="section-title">Вы недавно смотрели</h2></div>
            <div class="product-grid product-grid-wide">
                <?php foreach ($recentlyViewed as $item): ?>
                    <?php IncludeCom('shop/_product_card', ['product' => $item]) ?>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>
</div>
