<head>
    <?php IncludeCom('_dev/jcrop') ?>
    <script type="text/javascript">
        $(function () {
            const form_<?= $uplName?> = $('#i-upl-form-<?= $uplName?>');
            const uplInput_<?= $uplName?> = $('#i-upl-form-<?= $uplName?> input[type="file"]');
            const progress_<?= $uplName?> = $('#i-upl-form-<?= $uplName?> .progress-wrapper');
            const bar_<?= $uplName?> = $('#i-upl-form-<?= $uplName?> .progress .progress-bar');

            function SetFileUplProgress_<?= $uplName?>(percentVal) {
                bar_<?= $uplName?>
                    .width(percentVal)
                    .attr('aria-valuenow', percentVal)
                    .html(percentVal);
            }

            function DeleteUploadFile_<?= $uplName?>() {
                SetFileUplProgress_<?= $uplName?>('0%');
                $('input[name="<?= $uplName?>_rdy_name"]').val('');
                uplInput_<?= $uplName?>.show();
                progress_<?= $uplName?>.hide();
            }

            $("#i-ajax-form-<?= $uplName?>-button-image-delete button").on("click", () => DeleteUploadFile_<?= $uplName?>());
            $("#i-ajax-form-<?= $uplName?>-button-image-refresh button").on("click", () => DeleteUploadFile_<?= $uplName?>());

            form_<?= $uplName?>.ajaxForm({
                beforeSend: function () {
                    uplInput_<?= $uplName?>.hide();
                    progress_<?= $uplName?>.show();
                    SetFileUplProgress_<?= $uplName?>('0%');
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    SetFileUplProgress_<?= $uplName?>(percentComplete + '%');
                },
                success: function () {
                    SetFileUplProgress_<?= $uplName?>('100%');
                },
                complete: function (xhr) {
                    SetFileUplProgress_<?= $uplName?>('100%');
                    let json = null;
                    let isValidJson = false;

                    try {
                        json = JSON.parse(xhr.responseText);
                        isValidJson = true;
                    } catch (err) {
                    }

                    <?php if ($isImageUpload):?>
                    $('#i-ajax-form-<?= $uplName?>-button-image-edit').hide();
                    <?php endif;?>
                    $('#i-ajax-form-<?= $uplName?>-button-image-delete').hide();
                    $('#i-ajax-form-<?= $uplName?>-button-image-refresh').hide();
                    bar_<?= $uplName?>.removeClass('progress-bar-danger');

                    if (isValidJson && json?.success) {
                        $('input[name="<?= $uplName?>_rdy_name"]').val(json.fileName);
                        <?php if ($isImageUpload):?>
                        $('#i-ajax-form-<?= $uplName?>-button-image-edit').show();
                        const urlStart = $('#i-ajax-form-<?= $uplName?>-button-image-edit').find('a').data('start-url');
                        const url = "/_crop_image?field=<?= $uplName?>&has_thumbs=<?= intval($hasThumbs)?>&thumbs=<?= $thumbs?>&def_w=<?= $defWidth?>&def_h=<?= $defHeight?>&uri=" + urlStart + json.fileName + '&ekkoLightboxFixer=1';
                        $('#i-ajax-form-<?= $uplName?>-button-image-edit').find('a').attr('href', url);
                        <?php endif;?>
                        $('#i-ajax-form-<?= $uplName?>-button-image-delete').show();
                        <?php if ($isImageUpload):?>
                        setTimeout(
                            () => $('#i-ajax-form-<?= $uplName?>-button-image-edit a').trigger('click'),
                            250
                        );
                        <?php endif;?>
                    } else {
                        bar_<?= $uplName?>.addClass('progress-bar-danger');
                        $('#i-ajax-form-<?= $uplName?>-button-image-refresh').show();
                        let errorMessage = "<span style='display: block; font-weight: normal' class='text-overflow'>" + json?.errors + "</span>";
                        SetFileUplProgress_<?= $uplName?>(errorMessage);
                    }
                }
            });
        });
    </script>
</head>


<div id="i-upl-form-<?= $uplName ?>-rdy-form" style="display: none;">
    <input type="hidden" name="<?= $uplName ?>_rdy_name" value="">
</div>

<form action="<?= GetCurUrl() ?>" <?= Uploader::FORM_LOAD ?> id="i-upl-form-<?= $uplName ?>" class="ajax-upload-form">
    <input type="hidden" name="is_set_upl_form_<?= $uplName ?>" value="1">
    <input type="file" name="<?= $uplName ?>" onchange="$('#i-upl-form-<?= $uplName ?>').trigger('submit');">
    <div class="row progress-wrapper" style="display: none;">
        <div class="col-6 col-md-9">
            <div class="progress" role="progressbar" aria-valuenow="0%" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-striped" style="width: 0;">0%</div>
            </div>
        </div>
        <?php if ($isImageUpload): ?>
            <div class="col-2 col-md-1 text-center" id="i-ajax-form-<?= $uplName ?>-button-image-edit" style="display: none;">
                <a href="#" data-start-url="<?= $uplURL ?>" class="btn btn-link btn-sm ajax-upload-form-button" title="Обрезать изображение" data-toggle="lightbox" data-on-close="" data-title="Обрезать изображение"><i class="bi bi-crop"></i></a>
            </div>
        <?php endif ?>
        <div class="col-2 col-md-1 text-center" id="i-ajax-form-<?= $uplName ?>-button-image-delete" style="display: none;">
            <button type="button" class="btn btn-link btn-sm ajax-upload-form-button ajax-upload-form-button-red" title="Удалить"><i class="bi bi-trash3-fill"></i></button>
        </div>
        <div class="col-2 col-md-1 text-center" id="i-ajax-form-<?= $uplName ?>-button-image-refresh" style="display: none;">
            <button type="button" class="btn btn-link btn-sm ajax-upload-form-button ajax-upload-form-button-red" title="Попробовать ещё раз"><i class="bi bi-arrow-clockwise"></i></button>
        </div>
    </div>
</form>
