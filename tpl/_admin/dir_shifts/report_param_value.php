<?php if (+$model->param->type === ParamModel::TYPE_STRING): ?>
    <div class="param-value param-value-text"><?= $model->value ?></div>
<?php endif ?>
<?php if (+$model->param->type === ParamModel::TYPE_IMAGE): ?>
    <div class="param-value param-value-teximage">
        <?php if ($model->value): ?>
            <img src="<?= $model->value ?>" alt="Preview to <?= $model->id ?>" class="rounded img-fluid">
        <?php else: ?>
            <span class="text-muted">Нет изображения</span>
        <?php endif; ?>
    </div>
<?php endif ?>
<?php if (+$model->param->type === ParamModel::TYPE_NUMBER): ?>
    <div class="param-value param-value-number">
        <?= floatval($model->value) ?>
    </div>
<?php endif ?>
<?php if (+$model->param->type === ParamModel::TYPE_DATETIME): ?>
    <div class="param-value param-value-date">
        <?= $model->value ?>
    </div>
<?php endif ?>
<?php if (+$model->param->type === ParamModel::TYPE_TIME): ?>
    <div class="param-value param-value-time">
        <?= $model->value ?>
    </div>
<?php endif ?>