<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">Информация по склеенным extraPacker-ом файлам</h1>
        </div>
    </div>
    <div class="row">
        <?php foreach ($dirs as $site => $arr): ?>
            <div class="col mb-5">
                <h2 class="mb-3"><?= $site ?></h2>
                <?php foreach (['css', 'js'] as $type): ?>
                    <div class="ps-3">
                        <h4><?= ucfirst($type) ?></h4>
                        <div class="mb-5">
                            <strong>Время архивации:</strong><br>
                            <p class="ps-3"><?= FormatDate($arr[$type]['timestamp']) ?> UTC</p>
                        </div>
                        <div class="mb-5">
                            <strong>Время работы extraPacker-а:</strong>
                            <div class="ps-3">
                                <p><?= OutputFormats::number($arr[$type]['pack_time']["{$type}_pack_time"]) ?>с.</p>
                            </div>
                        </div>
                        <div class="mb-5">
                            <strong>Запакованные файлы <?= ucfirst($type) ?>:</strong><br>
                            <ul class="list-unstyled ps-3">
                                <?php foreach ($arr[$type]['files'] as $file): ?>
                                    <li class="mb-1">
                                        <?= $file['addr'] ?> <span class="badge bg-dark"><?= FormatDate($file['time']) ?></span>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endforeach ?>
    </div>
</div>