<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 1100px;">
            <h1 class="my-4">Отзывы</h1>
            <ul class="nav nav-pills mb-4">
                <li class="nav-item"><a class="nav-link<?= $status === '' ? ' active' : '' ?>" href="<?= GetCurUrl('status=all') ?>">Все</a></li>
                <?php foreach ($statuses as $key => $name): ?>
                    <li class="nav-item"><a class="nav-link<?= $status === $key ? ' active' : '' ?>" href="<?= GetCurUrl('status=' . $key) ?>"><?= $name ?><?= $key === ShopReviewModel::STATUS_NEW && ($new = ShopReviewModel::countNew()) ? " <span class=\"badge bg-danger\">{$new}</span>" : '' ?></a></li>
                <?php endforeach ?>
            </ul>
            <?php if (empty($list)): ?>
                <p class="text-muted">Отзывов нет</p>
            <?php endif ?>
            <?php foreach ($list as $review): $product = new ShopProductModel($review->product_id) ?>
                <div class="card card-body mb-3">
                    <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                        <b><?= $review->author_name ?></b>
                        <a href="<?= SiteRoot("_admin/users/add_or_edit&id={$review->user_id}") ?>" class="small">#<?= $review->user_id ?></a>
                        <?php if ($review->is_buyer): ?><span class="badge bg-success">Покупатель</span><?php endif ?>
                        <span class="text-warning"><?= str_repeat('★', $review->rating) ?><span class="text-muted"><?= str_repeat('★', 5 - $review->rating) ?></span></span>
                        <span class="badge <?= $review->status === ShopReviewModel::STATUS_PUBLISHED ? 'bg-primary' : ($review->status === ShopReviewModel::STATUS_NEW ? 'bg-warning text-dark' : 'bg-secondary') ?>"><?= $statuses[$review->status] ?? $review->status ?></span>
                        <span class="text-muted small ms-auto"><?= date('d.m.Y H:i', $review->create_time) ?></span>
                    </div>
                    <div class="small mb-2">
                        <?php if ($product->isExists()): ?>
                            <a href="<?= $product->url() ?>#reviews" target="_blank"><?= $product->name ?></a>
                        <?php else: ?>
                            <span class="text-muted">Товар удалён</span>
                        <?php endif ?>
                    </div>
                    <p class="mb-3"><?= nl2br($review->text) ?></p>
                    <form action="<?= GetCurUrl() ?>" method="post" class="d-flex gap-2">
                        <input type="hidden" name="id" value="<?= $review->id ?>">
                        <?php if ($review->status !== ShopReviewModel::STATUS_PUBLISHED): ?>
                            <button type="submit" name="a" value="publish" class="btn btn-sm btn-primary rounded-pill"><i class="bi bi-check-lg pe-1"></i>Опубликовать</button>
                        <?php endif ?>
                        <?php if ($review->status !== ShopReviewModel::STATUS_REJECTED): ?>
                            <button type="submit" name="a" value="reject" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="bi bi-eye-slash pe-1"></i>Отклонить</button>
                        <?php endif ?>
                        <button type="submit" name="a" value="delete" class="btn btn-sm btn-outline-danger rounded-pill ms-auto" onclick="return confirm('Удалить отзыв? Пользователь сможет написать новый')"><i class="bi bi-trash3"></i></button>
                    </form>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
