<script src="<?= Root('i/js/_dev/yandex-address-input.js') ?>"></script>
<script>
    $(function () {
        initMap(ymaps, 51.507351, -0.127696);
    });
</script>
<script src="https://api-maps.yandex.ru/2.1/?apikey=215f771a-e251-4871-b409-b1dfb06ed996&suggest_apikey=45ccec0e-0618-4571-b0fa-9e5ec4a080ad&lang=ru_RU&mode=debug&load=package.full&onload=mscDistance.Ymaps.ready&ns=ymaps" type="text/javascript"></script>

<h1 class="mt-4 mb-4">Yandex address input</h1>
<div class="form-group">
    <label>Адрес</label>
    <input type="text" name="address" class="form-control" id="id-suggest" value=""/>
    <div id="id-map" class="location-block-map" style="height: 350px; border-radius: 10px;"></div>
</div>
<div class="form-group">
    <label>Координаты</label>
    <div class="row">
        <div class="col">
            <input type="text" class="form-control xdata-address-latitude"/>
        </div>
        <div class="col">
            <input type="text" class="form-control xdata-address-longitude"/>
        </div>
    </div>
</div>