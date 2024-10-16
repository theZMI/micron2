<?php IncludeCom('_dev/pwd_shower') ?>

<h1 class="mt-4 mb-4">Показ пароля по кнопке:</h1>
<div class="input-group mb-3">
    <input
            type="password"
            id="i-pwd"
            class="form-control"
            placeholder="Введите пароль"
            name="pwd"
            value=""
            autocomplete="off"
    >
    <button
            class="btn btn-outline-secondary pwd-shower"
            type="button"
            data-field="#i-pwd"
            data-class-show="bi-eye-fill"
            data-class-hide="bi-eye-slash-fill"
    >
        <span class="bi bi-eye-slash-fill"></span>
    </button>
</div>