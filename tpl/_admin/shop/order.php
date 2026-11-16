<?php $sign = Config('shop')['currency_sign'] ?>
<div class="container-fluid mb-4">
    <div class="row">
        <div class="col" style="max-width: 1000px;">
            <h1 class="my-4">Заказ №<?= $order->id ?> <small class="text-muted fs-5"><?= date('d.m.Y H:i', $order->create_time) ?></small></h1>
            <?= $msg ?>
            <div class="row g-4 mb-4">
                <div class="col-md-7">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Покупатель</dt><dd class="col-sm-8"><?= $order->customer_name ?><?= $order->user_id ? " <a href=\"" . SiteRoot("_admin/users/add_or_edit&id={$order->user_id}") . "\">(пользователь #{$order->user_id})</a>" : ' (гость)' ?></dd>
                        <dt class="col-sm-4">E-mail</dt><dd class="col-sm-8"><a href="mailto:<?= $order->customer_email ?>"><?= $order->customer_email ?></a></dd>
                        <dt class="col-sm-4">Телефон</dt><dd class="col-sm-8"><?= $order->customer_phone ? OutputFormats::mobilePhone($order->customer_phone) : '—' ?></dd>
                        <dt class="col-sm-4">Комментарий</dt><dd class="col-sm-8"><?= $order->comment ? nl2br($order->comment) : '—' ?></dd>
                        <dt class="col-sm-4">Оплачен</dt><dd class="col-sm-8"><?= $order->paid_time ? date('d.m.Y H:i', $order->paid_time) : '—' ?></dd>
                        <dt class="col-sm-4">Ссылка покупателя</dt><dd class="col-sm-8"><a href="<?= $order->url() ?>" target="_blank">открыть</a></dd>
                    </dl>
                </div>
                <div class="col-md-5">
                    <form action="<?= GetCurUrl() ?>" method="post" class="card card-body">
                        <input type="hidden" name="a" value="set_status">
                        <label class="form-label fw-bold">Статус заказа</label>
                        <div class="input-group">
                            <select name="status" class="form-select">
                                <?php foreach (ShopOrderModel::getStatuses() as $k => $name): ?>
                                    <option value="<?= $k ?>" <?= $k === $order->status ? 'selected' : '' ?>><?= $name ?></option>
                                <?php endforeach ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php IncludeCom('shop/_order_items', ['order' => $order]) ?>

            <h2 class="h4 mt-4 d-flex align-items-center">
                <span class="me-auto">Платежи</span>
                <?php if ($payments): ?>
                    <form action="<?= GetCurUrl() ?>" method="post">
                        <input type="hidden" name="a" value="check_payments">
                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill"><i class="bi bi-arrow-repeat pe-1"></i>Проверить в банке</button>
                    </form>
                <?php endif ?>
            </h2>
            <?php if (empty($payments)): ?>
                <p class="text-muted">Покупатель ещё не переходил к оплате</p>
            <?php else: ?>
                <table class="table table-sm">
                    <thead><tr><th>#</th><th>Создан</th><th>ID в банке</th><th class="text-end">Сумма</th><th>Статус</th></tr></thead>
                    <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= $payment->id ?></td>
                            <td><?= date('d.m.Y H:i', $payment->pay_time) ?></td>
                            <td><code><?= htmlspecialchars($payment->transfer_id) ?></code></td>
                            <td class="text-end"><?= OutputFormats::amount($payment->amount, 2, $sign) ?></td>
                            <td><?= PaymentModel::getStatuses()[$payment->status] ?? $payment->status ?></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
            <a href="<?= SiteRoot('_admin/shop/orders') ?>" class="btn btn-link"><i class="bi bi-arrow-left pe-2"></i>К списку заказов</a>
        </div>
    </div>
</div>
