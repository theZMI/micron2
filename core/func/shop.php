<?php

// Деньги в магазине - целые копейки (int): в БД (BIGINT), в моделях и в расчётах. Рубли - только на входе (формы, таблица импорта)
// и на выходе (ShopPrice, фиды, банки)

// Цена в валюте магазина из копеек: 1 620 ₽ (копейки показываем, только если они есть)
function ShopPrice(int $kopecks): string
{
    return OutputFormats::amount($kopecks / 100, $kopecks % 100 === 0 ? 0 : 2, Config('shop')['currency_sign']);
}

// Рубли из формы/таблицы -> копейки: «1 620,50», «1620.5», «1 620 ₽» -> 162050. Неправильное число - null
function ShopKopecks($rubles): ?int
{
    if (is_int($rubles)) {
        return $rubles * 100;
    }
    $value = str_replace([',', ' ', "\u{00A0}", "\u{202F}", '₽', 'руб.', 'руб'], ['.', '', '', '', '', '', ''], trim((string)$rubles));
    // Только обычная запись: цифры и одна точка. is_numeric() пропустил бы и «1.5e2» - и цена стала бы в 100 раз меньше
    if (!preg_match('/^[+-]?(\d+(\.\d*)?|\.\d+)$/', $value)) {
        return null;
    }
    // Через строку, а не float × 100: «0.29» × 100 во float - это 28.999...
    [$int, $frac] = explode('.', ltrim($value, '+') . '.', 3) + [1 => ''];
    $sign = str_starts_with($int, '-') ? -1 : 1;
    $frac = str_pad(substr($frac, 0, 3), 3, '0');
    return $sign * ((int)ltrim($int, '-') * 100 + (int)round((int)$frac / 10));
}

// Копейки -> рубли для полей ввода, таблиц и фидов: 162050 -> «1620.5», 162000 -> «1620»
function ShopRubles(int $kopecks): string
{
    return rtrim(rtrim(number_format($kopecks / 100, 2, '.', ''), '0'), '.');
}

// Подпись единицы после количества: «шт.», «м», «кг»
function ShopUnitName(string $unit): string
{
    return Config('shop')['units'][$unit]['name'] ?? $unit;
}

// Количество с единицей: «2,5 м», «3 шт.» (без лишних нулей)
function ShopQuantity(float $quantity, string $unit = '', bool $withUnit = true): string
{
    $text = rtrim(rtrim(number_format($quantity, 3, ',', ' '), '0'), ',');
    return $withUnit && $unit !== '' ? $text . ' ' . ShopUnitName($unit) : $text;
}

// Число для value у <input type="number">: 2.5 (точка, без лишних нулей)
function ShopQuantityValue(float $quantity): string
{
    return rtrim(rtrim(number_format($quantity, 3, '.', ''), '0'), '.');
}

// Абсолютный адрес (для Open Graph, sitemap, фидов): Root('i/...') -> http://site.ru/i/...
function ShopAbsUrl(string $url): string
{
    return preg_match('~^https?://~', $url) ? $url : rtrim((string)Env('SITE_ROOT_URL'), '/') . '/' . ltrim($url, '/');
}

// Данные для индикаторов в меню и для JS после действий с корзиной/избранным/сравнением
function ShopHeaderState(): array
{
    $cart = ShopCart::summary();
    return [
        'cart_count'      => $cart['count'],
        'cart_total'      => $cart['total'],
        'cart_total_text' => strip_tags(ShopPrice($cart['total'])),
        'favorites_count' => ShopFavorites::count(),
        'compare_count'   => ShopCompare::count(),
    ];
}
