<h1 class="mt-4 mb-4">Mockup-модели</h1>
<p class="mb-4">
    Это такие модели-заглушки в которые данные вписаны прямо здесь и сейчас, но возможно, в будущем, они будут преобразованы в настоящие модели.<br><br>
    Например: Пусть сейчас мы разрабатываем сайт, и у нас возникла необходимость ввести `user_profession` которое будет являться списком возможных вариантов.<br>
    Мы бы хотели сделать таблицу `user_professions` со структурой: `id`, `name`<br>
    А в будущем, мы бы хотели, написать админку для добавления новых профессий и изменения существующих.<br>
    Однако, сейчас делать этого не хочется так как это будущее улучшение проекта, а не "необходимый минимум".<br>
    Потому мы создаём fake-модель которая эмулирует работу нашей обычной модели (на все данные в ней захардкожены).
</p>
<div class="row">
    <div class="col">
        [code=Php]
        // Создаём файл model/UserProfessionModel.php в котором наследуем нашу модель от MockupModel
        class UserProfessionModel extends MockupModel
        {
            // Определяем все наши захардкоженные данные прямо тут
            protected function _pseudoDB()
            {
                return [
                    1 => ['id' => 1, 'name' => 'Программист'],
                    2 => ['id' => 2, 'name' => 'Владелец продукта'],
                    3 => ['id' => 3, 'name' => 'Менеджер проекта'],
                    4 => ['id' => 4, 'name' => 'Scrum-мастер'],
                ];
            }
        }

        // Теперь можем в компонентах использовать псевдо-модель будто она настоящая:
        $model = new UserProfessionModel(4);
        echo "<strong>Под ID=4 у нас в БД:</strong>";
        __($model->getData());
        echo "<br>";
        echo "<strong>А весь список в БД:</strong>";
        __($model->getList());
        [/code]
    </div>
    <div class="col" style="font-size: 13px;">
        <?php
        $model = new UserProfessionModel(4);
        echo "<strong>Под ID=4 у нас в БД:</strong>";
        __($model->getData());
        echo "<br>";
        echo "<strong>А весь список в БД:</strong>";
        __($model->getList());
        ?>
    </div>
</div>