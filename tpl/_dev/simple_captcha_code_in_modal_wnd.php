<button type="button" data-bs-toggle="modal" data-bs-target="#captchaModal" id="captchaModalToggleButton" class="d-none"></button>
<div class="modal fade" id="captchaModal" tabindex="-1" aria-labelledby="captchaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-clear-style modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h3 class="m-0">Введите код с картинки:</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ms-auto me-auto">
                <div class="simple-captcha-code-in-modal-wnd">
                    <?php IncludeCom('_dev/simple_captcha_code', ['captcha' => GetCaptchaInstance()]) ?>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="submit" data-bs-dismiss="modal" class="btn btn-primary btn-lg btn-min-width rounded-pill">Отправить<i class="ms-2 bi bi-arrow-right"></i></button>
            </div>
        </div>
    </div>
</div>