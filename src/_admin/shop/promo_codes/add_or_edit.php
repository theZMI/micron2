<?php

$model      = new ShopPromoCodeModel(intval(Get('id')) ?: null);
$modelParam = fn($param, $default = '') => ModelParam($model, $param, Post($param, $default));
$dateValue  = fn($param) => Post('is_set') ? (string)Post($param) : ($model->isExists() && $model->$param ? date('Y-m-d', $model->$param) : '');

$msg = '';
if (Post('is_set')) {
    $code  = ShopPromoCodeModel::normalizeCode((string)Post('code'));
    $type  = array_key_exists((string)Post('type'), ShopPromoCodeModel::types()) ? (string)Post('type') : ShopPromoCodeModel::TYPE_PERCENT;
    $value = ShopKopecks((string)Post('value', '0')) ?? 0; // Рубли -> копейки, проценты -> сотые доли процента (оба ×100)
    $start = trim((string)Post('start_date'));
    $end   = trim((string)Post('end_date'));
    $errs  = [];
    if ($code === '' || !preg_match('/^[\p{L}\p{N}_-]{2,50}$/u', $code)) {
        $errs[] = 'Код - от 2 до 50 букв, цифр, «-» или «_», без пробелов';
    } elseif (GetDefaultDb()->selectCell("SELECT 1 FROM `shop_promo_codes` WHERE `code` = ? AND `id` <> ?d", $code, (int)$model->id)) {
        $errs[] = "Промокод {$code} уже есть";
    }
    if ($value <= 0 || ($type === ShopPromoCodeModel::TYPE_PERCENT && $value > 10000)) {
        $errs[] = $type === ShopPromoCodeModel::TYPE_PERCENT ? 'Процент - от 0 до 100' : 'Сумма скидки должна быть больше нуля';
    }

    if ($errs) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->code       = $code;
        $model->type       = $type;
        $model->value      = $value;
        $model->min_amount = max(0, (int)ShopKopecks((string)Post('min_amount', '0')));
        $model->max_uses   = max(0, intval(Post('max_uses')));
        $model->start_time = $start !== '' ? (int)strtotime("{$start} 00:00:00") : 0;
        $model->end_time   = $end !== '' ? (int)strtotime("{$end} 23:59:59") : 0;
        $model->is_active  = (bool)Post('is_active');
        $model->comment    = mb_substr(trim((string)Post('comment')), 0, 255);
        $model->flush();
        UrlRedirect::go(SiteRoot('_admin/shop/promo_codes'));
    }
}
