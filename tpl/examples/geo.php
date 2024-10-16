<h1 class="mt-4 mb-4">GEO данные и ф-ии работы с ними</h1>

<div class="row">
    <div class="col-6">
        Данные GEO-позиции по IP:
        [code=Php]
        Xmp(GetLocationByIP());
        [/code]
    </div>
    <div class="col-6">
        <?php
        Xmp(GetLocationByIP());
        ?>
    </div>
</div>

<div class="row">
    <div class="col-6">
        Список стран:
        [code=Php]
        Xmp(GetCountries());
        [/code]
    </div>
    <div class="col-6">
        <div style="max-height: 500px; overflow-y: scroll">
            <?php
            Xmp(GetCountries());
            ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        Страна по коду:
        [code=Php]
        Xmp(GetCountryByCode('US'));
        [/code]
    </div>
    <div class="col-6">
        <?php
        Xmp(GetCountryByCode('US'));
        ?>
    </div>
</div>

<div class="row">
    <div class="col-6">
        Координаты по адресу:
        [code=Php]
        Xmp(GetCoordinatesByAddress('калининградская область южный 54'));
        [/code]
    </div>
    <div class="col-6">
        <?php
        Xmp(GetCoordinatesByAddress('калининградская область южный 54'));
        ?>
    </div>
</div>