<?php

// Цена в валюте магазина: 1 620 ₽ (копейки показываем, только если они есть)
function ShopPrice(float $amount): string
{
    $decimals = fmod($amount, 1.0) == 0.0 ? 0 : 2;
    return OutputFormats::amount($amount, $decimals, Config('shop')['currency_sign']);
}

// Данные для индикаторов в меню и для JS после действий с корзиной/избранным
function ShopHeaderState(): array
{
    $cart = ShopCart::summary();
    return [
        'cart_count'      => $cart['count'],
        'cart_total'      => $cart['total'],
        'cart_total_text' => strip_tags(ShopPrice($cart['total'])),
        'favorites_count' => ShopFavorites::count(),
    ];
}
