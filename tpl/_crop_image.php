<head>
    <?php IncludeCom('_dev/jcrop') ?>
    <style>
        .jcrop-tracker {
            max-width: 100% !important;
            max-height: 100% !important;
        }
    </style>
    <script>
        $(function () {
            $('#jcrop-target').on('load', function () {
                const my_basename = path => path.replace(/.*\//, '');
                const my_dirname = path => path.match(/.*\//);
                const defBorderOffset = 15;

                $('#jcrop-target').Jcrop({
                    <?php if ($hasDefHW): ?>
                    aspectRatio: <?= $aspectRatio ?>,
                    <?php endif ?>
                    setSelect: [defBorderOffset, defBorderOffset, parseInt($('#jcrop-target').width() - defBorderOffset), parseInt($('#jcrop-target').height() - defBorderOffset)],
                    onSelect: Jcrop_updateCoords
                });

                $('#i-jcrop-form').ajaxForm(function () {
                    <?php foreach ($thumbs as $thumb): ?>
                    const w = '<?= $thumb[0] ?>';
                    const h = '<?= $thumb[1] ?>';
                    const url = '<?= $url ?>';
                    const previewUrl = my_dirname(url) + w + 'x' + h + '/' + my_basename(url);
                    const previewImg = $('.is-<?= $field ?>-' + w + 'x' + h + '-preview');

                    if (previewImg.length) {
                        previewImg.attr('src', previewUrl);
                    }
                    <?php endforeach ?>

                    $('#i-jcrop-form-close-button').trigger('click');
                });
            });
        });

        function Jcrop_updateCoords(c) {
            $('#i-x').val(c.x);
            $('#i-y').val(c.y);
            $('#i-w').val(c.w);
            $('#i-h').val(c.h);
            $('#i-visible-w').val($('#jcrop-target').width());
        }

        function Jcrop_checkCoords() {
            if (parseInt($('#i-w').val())) {
                return true;
            }
            alert('Please select a crop region then press submit.');
            return false;
        }
    </script>
</head>


<form action="<?= GetCurUrl() ?>" method="post" onsubmit="return Jcrop_checkCoords();" class="jcrop-form" id="i-jcrop-form">
    <div class="jcrop-form-preview">
        <img src="<?= $url ?>" id="jcrop-target" style="max-width: 100%; display: block;">
    </div>
    <input type="hidden" name="is_set" value="1"/>
    <input type="hidden" name="uri" value="<?= $uri ?>"/>
    <input type="hidden" name="x" id="i-x" value="0"/>
    <input type="hidden" name="y" id="i-y" value="0"/>
    <input type="hidden" name="w" id="i-w" value="0"/>
    <input type="hidden" name="h" id="i-h" value="0"/>
    <input type="hidden" name="visible_w" id="i-visible-w" value="0"/>

    <div class="jcrop-form-actions">
        <button type="button" class="btn btn-link" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" id="i-jcrop-form-close-button">Отмена</button>
        <button type="submit" class="btn btn-primary">Обрезать</button>
    </div>
</form>