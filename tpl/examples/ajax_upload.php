<h1>Example of ajax upload (for files and images)</h1>
<form action="<?= GetCurUrl() ?>" id="ajax-example-form" <?= Uploader::FORM_LOAD ?>>
    <input type="hidden" name="is_set" value="1">
    <?= $msg ?>
    <div class="form-group">
        <label>Превью-изображение</label><br>
        <!-- В это hidden-поле будет вставлено имя загруженное файла -->
        <input type="hidden" name="image_1_rdy_name" value=""/>

        <!-- Вместо этой картинки появится превьюшка загруженной и crop-нутой картинки -->
        <img src="<?= Root('i/image/def_img_bg.png') ?>" style="width: 320px; height: 240px;" alt="preview" class="is-image_1-640x480-preview rounded">
    </div>
</form>
<!-- Форма загрузки и сама является формой, потому должна быть вынесена за пределы основной формы -->
<div class="mt-2">
    <?= $preview_image_form ?>
</div>
<div class="text-center mt-2">
    <!-- Кнопка эмулирует submit по форме так как вынесена за пределы формы -->
    <button type="button" onclick="$('#ajax-example-form').trigger('submit')" class="btn btn-primary btn-lg">Сохранить</button>
</div>