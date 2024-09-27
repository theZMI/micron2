<head>
    <script type="text/javascript">
        $(() => {
            $('#ms-params-test').magicSuggest({
                placeholder: 'Выбрать...',
                data: [
                    'Test 1',
                    'Test 2',
                    'Test 3',
                    'Test 4',
                    'Test 5'
                ],
                value: [
                    'Test 2'
                ],
                allowFreeEntries: true, // Разрешить собственный вариант
                maxSelection: 2, // Максимальное количество вариантов
            });
        });
    </script>
</head>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mt-4 mb-4">Magicsuggest</h1>
        </div>
        <div class="col">
            <div class="mb-3">
                <label class="form-label">Magicsuggest</label>
                <input type="text" name="params[param_number_1]" class="form-control" id="ms-params-test">
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label class="form-label">Regular input</label>
                <input type="text" name="params[param_number_2]" class="form-control">
            </div>
        </div>
    </div>
</div>