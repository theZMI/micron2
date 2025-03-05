<h1 class="my-4">GEO данные и ф-ии работы с ними</h1>

<div class="row mb-3">
    <div class="col-6">
        [code=Php]
        // Данные GEO-позиции по IP:
        __(GetLocationByIP());
        [/code]
    </div>
    <div class="col-6">
        <?php
        __(GetLocationByIP());
        ?>
    </div>
</div>

<div class="row mb-3">
    <div class="col-6">
        [code=Php]
        // Список стран:
        __(GetCountries());
        [/code]
    </div>
    <div class="col-6">
        <div style="max-height: 500px; overflow-y: scroll">
            <?php
            __(GetCountries());
            ?>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-6">
        [code=Php]
        // Страна по коду:
        __(GetCountryByCode('US'));
        [/code]
    </div>
    <div class="col-6">
        <?php
        __(GetCountryByCode('US'));
        ?>
    </div>
</div>

<div class="row mb-3">
    <div class="col-6">
        [code=Php]
        // Координаты по адресу:
        __(GetCoordinatesByAddress('калининградская область южный 54'));
        [/code]
    </div>
    <div class="col-6">
        <?php
        __(GetCoordinatesByAddress('калининградская область южный 54'));
        ?>
    </div>
</div>