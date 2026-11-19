<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 800px;">
            <h1 class="my-4"><?= $model->isExists() ? 'Промокод ' . $model->code : 'Новый промокод' ?></h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Код *</label>
                        <input type="text" name="code" class="form-control text-uppercase" value="<?= $modelParam('code') ?>" required placeholder="SALE10">
                        <div class="form-text">Регистр не важен</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Скидка *</label>
                        <div class="input-group">
                            <input type="text" name="value" class="form-control" inputmode="decimal" value="<?= $model->isExists() ? ShopRubles((int)$model->value) : Post('value', '') ?>" required>
                            <select name="type" class="form-select" style="max-width: 7rem;">
                                <?php foreach (ShopPromoCodeModel::types() as $key => $name): ?>
                                    <option value="<?= $key ?>" <?= $modelParam('type', ShopPromoCodeModel::TYPE_PERCENT) === $key ? 'selected' : '' ?>><?= $key === ShopPromoCodeModel::TYPE_PERCENT ? '%' : Config('shop')['currency_sign'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-text">На товары, без доставки</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Заказ от, <?= Config('shop')['currency_sign'] ?></label>
                        <input type="text" name="min_amount" class="form-control" inputmode="decimal" value="<?= $model->isExists() ? ($model->min_amount > 0 ? ShopRubles((int)$model->min_amount) : '') : Post('min_amount', '') ?>">
                        <div class="form-text">Пусто - любая сумма</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Сколько раз можно использовать</label>
                        <input type="number" name="max_uses" min="0" class="form-control" value="<?= $model->isExists() ? ($model->max_uses ?: '') : Post('max_uses', '') ?>">
                        <div class="form-text">Пусто - без ограничений<?= $model->isExists() ? '. Уже использован: ' . $model->used_count : '' ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Действует с</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $dateValue('start_time') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Действует по</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $dateValue('end_time') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Комментарий</label>
                    <input type="text" name="comment" class="form-control" value="<?= $modelParam('comment') ?>" placeholder="Для кого и зачем - видно только в админке">
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="i-is-active" <?= $model->isExists() ? ($model->is_active ? 'checked' : '') : 'checked' ?>>
                    <label class="form-check-label" for="i-is-active">Включён</label>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/shop/promo_codes') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
