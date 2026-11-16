<div class="container my-4" style="max-width: 900px;">
    <h1 class="mb-4">Оформление заказа</h1>
    <div class="row g-4">
        <div class="col-md-7">
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="mb-3">
                    <label class="form-label">Имя *</label>
                    <input type="text" name="name" class="form-control" value="<?= $form['name'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail *</label>
                    <input type="email" name="email" class="form-control" value="<?= $form['email'] ?>" required>
                    <div class="form-text">Пришлём чек и ссылку на заказ</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Телефон</label>
                    <input type="tel" name="phone" class="form-control phone-mask" value="<?= $form['phone'] ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Комментарий к заказу</label>
                    <textarea name="comment" class="form-control" rows="3"><?= $form['comment'] ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100">Перейти к оплате</button>
            </form>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ваш заказ</h5>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between small py-1">
                            <span><?= $item['product']->name ?> × <?= $item['quantity'] ?></span>
                            <span class="text-nowrap ps-2"><?= OutputFormats::amount($item['amount'], 2, Config('shop')['currency_sign']) ?></span>
                        </div>
                    <?php endforeach ?>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Итого</span><span><?= OutputFormats::amount($total, 2, Config('shop')['currency_sign']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
