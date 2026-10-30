<div class="pin-code-block" data-length="<?= $pinCodeLength ?>">
    <div class="row pin-code-block-inputs">
        <?php for ($i = 1; $i <= $pinCodeLength; $i++): ?>
            <div class="col-<?= $colWidth ?>">
                <input type="tel" class="form-control pin-code-block-input pin-code-block-input_<?= $i ?> input-focus-sequence" name="code[]"
                       data-next-focus="<?= $i < $pinCodeLength ? '.pin-code-block-input_' . ($i + 1) : '#i-pin-code-block_next-button' ?>"
                       maxlength="1" inputmode="numeric" autocomplete="<?= $i === 1 ? 'one-time-code' : 'off' ?>" placeholder="•" required/>
            </div>
        <?php endfor; ?>
    </div>
    <div class="pin-code-block-resend-block">
        <transition name="animation-fade">
            <div v-if="canResendPinCode">
                <button type="button" class="btn btn-link" @click="resendPinCode"><i class="bi bi-arrow-clockwise me-2"></i>Отправить код повторно</button>
            </div>
            <div v-else>
                Получить новый код можно через: <span class="badge rounded-pill bg-danger">{{ resendPinCodeTimer }}</span> сек.
            </div>
        </transition>
    </div>
    <div class="help-block with-errors pin-code-block-error-block">
        <div>Неверный код.</div>
        <div>Пожалуйста попробуйте ещё раз</div>
    </div>
</div>
