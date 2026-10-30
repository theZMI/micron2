<div class="simple-captcha-code">
    <div class="text-start mb-1">
        Введите код с картинки:
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <img src="<?= $captcha->getImage(); ?>" alt="Captcha Code" class="rounded border border-secondary img-fluid" id="i-simple-captcha-img" />
            <div class="simple-captcha-code-spacer"></div>
        </div>
        <div>
            <i class="bi bi-chevron-double-right text-secondary px-1"></i>
            <div class="simple-captcha-code-spacer"></div>
        </div>
        <div class="form-group">
            <div class="required-field" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Обязательное поле"><span>*</span></div>
            <input type="text" class="form-control text-uppercase simple-captcha-code-input"  name="captcha_code" value="" required data-error="Введите код с картинки" placeholder="✱✱✱✱✱" maxlength="5" style="" />
            <div class="simple-captcha-code-spacer">
                <div class="help-block with-errors"></div>
            </div>
        </div>
    </div>
</div>