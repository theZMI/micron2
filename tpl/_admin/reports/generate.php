<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                Сгенерировать отчёт
            </h1>
            <form action="<?= GetCurUrl() ?>" method="post">
                <input type="hidden" name="is_set" value="1">
                <?= $msg ?>
                <div class="row mb-3">
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">С даты:</label>
                            <div class="input-group mb-3">
                                <input type="text" name="time_from" class="form-control datepicker datepicker-mask" value="<?= OutputFormats::dateForDatePicker(strtotime("-1 week")) ?>" autocomplete="off" placeholder="ДД-ММ-ГГГГ">
                                <span class="input-group-text">00:00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">По дату:</label>
                            <div class="input-group mb-3">
                                <input type="text" name="time_to" class="form-control datepicker datepicker-mask" value="<?= OutputFormats::dateForDatePicker(time()) ?>" autocomplete="off" placeholder="ДД-ММ-ГГГГ">
                                <span class="input-group-text">23:59</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <a href="<?= SiteRoot('_admin/reports') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-check-lg pe-2"></i>Сгенерировать</button>
                </div>
            </form>
        </div>
    </div>
</div>