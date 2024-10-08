// lat, lon - необязательный параметры. В случае их отсутствия будет запущен механизм автоматического определения местоположения
function initMap(ymaps, lat, lon) {
    ymaps.ready(function () {
        let suggestView1;
        let inputSelectorID = 'id-suggest';
        let map;
        let mapContainerSelectorID = 'id-map';
        let mapContainer = $('#' + mapContainerSelectorID);
        let myPlacemark;
        const DEFAULT_ZOOM = 14;
        const DEFAULT_LATTTUDE = 54.728503;
        const DEFAULT_LONGITUDE = 20.532604;

        if (lat && lon) {
            createMap({
                center: [lat, lon],
                zoom: 2
            });
        } else {
            // Создаём карту и центрируем пользователя
            ymaps.geolocation.get().then(function (res) {
                let bounds = res.geoObjects.get(0).properties.get('boundedBy'),
                    // Рассчитываем видимую область для текущей положения пользователя.
                    mapState = ymaps.util.bounds.getCenterAndZoom(
                        bounds,
                        [mapContainer.width(), mapContainer.height()]
                    );
                createMap(mapState);
            }, function (e) {
                // Если местоположение невозможно получить, то просто создаем карту.
                createMap({
                    center: [DEFAULT_LATTTUDE, DEFAULT_LONGITUDE],
                    zoom: 2
                });
            });
        }

        // Создаём карту и заполняем поле поиска
        function createMap(state) {
            let coords = state.center;
            state.controls = ['geolocationControl', 'zoomControl'];
            state.zoom = DEFAULT_ZOOM;
            map = new ymaps.Map(
                mapContainerSelectorID,
                state
            );
            specialCentringMap(map);
            suggestView1 = new ymaps.SuggestView(inputSelectorID);

            // Слушаем клик на карте.
            map.events.add('click', function (e) {
                let coords = e.get('coords');

                // Если метка уже создана – просто передвигаем ее.
                myPlacemark.geometry.setCoordinates(coords);
                getAddress(coords);
            });

            myPlacemark = createPlacemark(coords);
            map.geoObjects.add(myPlacemark);
            getAddress(myPlacemark.geometry.getCoordinates());

            // Слушаем событие окончания перетаскивания на метке.
            myPlacemark.events.add('dragend', function () {
                getAddress(myPlacemark.geometry.getCoordinates());
            });
        }

        // Создание метки.
        function createPlacemark(coords) {
            return new ymaps.Placemark(coords, {
                iconCaption: 'Поиск...'
            }, {
                preset: 'islands#violetDotIconWithCaption',
                draggable: true
            });
        }

        // Специальное смещение карты
        function specialCentringMap(map, offset = 0) {
            let coordinates = map.center;
            let pixelCenter = map.getGlobalPixelCenter(coordinates);
            pixelCenter = [
                pixelCenter[0],
                pixelCenter[1] - offset
            ];
            let geoCenter = map.options.get('projection').fromGlobalPixels(pixelCenter, map.getZoom());
            map.setCenter(geoCenter);
        }

        // Определяем адрес по координатам (обратное геокодирование).
        function getAddress(coordinates) {
            $('.xdata-address-latitude').val(coordinates[0]);
            $('.xdata-address-longitude').val(coordinates[1]);

            ymaps.geocode(coordinates).then(function (res) {
                let firstGeoObject = res.geoObjects.get(0);

                myPlacemark.properties
                    .set({
                        // Формируем строку с данными об объекте.
                        iconCaption: [
                            // Название населенного пункта или вышестоящее административно-территориальное образование.
                            firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                            // Получаем путь до топонима, если метод вернул null, запрашиваем наименование здания.
                            firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                        ].filter(Boolean).join(', '),
                        // В качестве контента балуна задаем строку с адресом объекта.
                        balloonContent: firstGeoObject.getAddressLine()
                    });
                $('#' + inputSelectorID).val(firstGeoObject.getAddressLine());
            });
        }

        // Ввод данных в поле ввода адреса
        function inputAddressInField(address) {
            ymaps.geocode(address, {results: 1}).then(function (res) {
                let coordinates = res.geoObjects.get(0).geometry.getCoordinates();
                map.setCenter(coordinates, DEFAULT_ZOOM);

                ymaps.geocode(coordinates).then(function (res) {
                    $('.xdata-address-latitude').val(coordinates[0]);
                    $('.xdata-address-longitude').val(coordinates[1]);
                });
            });
        }

        $('#' + inputSelectorID).on('change', (e) => {
            inputAddressInField(e.target.value);
        });
    });
}