<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">Datepicker</h1>
            <div class="mb-3">
                <label class="form-label">Выпадайка через jqueryui/datepicker + ввод руками через imask</label>
                <input type="text" class="form-control datepicker datepicker-mask" autocomplete="off" value="<?= OutputFormats::dateForDatePicker(time()) ?>">
            </div>
        </div>
    </div>
</div>