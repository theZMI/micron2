<?php if (+$model->param->type === ParamModel::TYPE_STRING): ?>
    <div class="mb-3">
        <label class="form-label"><?= $model->param->name ?></label>
        <input type="text" class="form-control" name="shift_params[<?= $model->id ?>]" value="<?= $model->value ?>">
    </div>
<?php endif ?>
<?php if (+$model->param->type === ParamModel::TYPE_IMAGE): ?>
    <div class="mb-3">
        <label class="form-label"><?= $model->param->name ?></label>
        <div>
            <?php if ($model->value): ?>
                <img src="<?= $model->value ?>" alt="Preview to <?= $model->id ?>" class="img-fluid rounded">
            <?php else: ?>
                <input type="text" readonly class="form-control-plaintext text-secondary" id="staticEmail" value="Нет изображения">
            <?php endif; ?>
        </div>
    </div>
<?php endif ?>
<?php if (+$model->param->type === ParamModel::TYPE_NUMBER): ?>
    <div class="mb-3">
        <label class="form-label"><?= $model->param->name ?></label>
        <input type="number" class="form-control" name="shift_params[<?= $model->id ?>]" value="<?= +$model->value ?>">
    </div>
<?php endif ?>
<?php if (+$model->param->type === ParamModel::TYPE_DATETIME): ?>
    <div class="mb-3">
        <label class="form-label"><?= $model->param->name ?></label>
        <input type="text" class="form-control" name="shift_params[<?= $model->id ?>]" value="<?= OutputFormats::dateTime(+$model->value) ?>">
    </div>
<?php endif ?>
<?php if (+$model->param->type === ParamModel::TYPE_TIME): ?>
    <div class="mb-3">
        <label class="form-label"><?= $model->param->name ?></label>
        <input type="text" class="form-control" name="shift_params[<?= $model->id ?>]" value="<?= FormatTimeInterval(+$model->value) ?>">
    </div>
<?php endif ?>