<h1 class="mt-4 mb-4">Micron cache</h1>
<p>
    Скрипт при первом вызове сохраняет данные в кеш, при повторном вызове скрипта берёт данные из кеша.<br>
    В model/MicronCache можно указать драйвер для сохранения (DbCacheModel или FileCacheModel).<br>
    Для повторной проверки сохранения измените ключ ($key) в данном скрипте
</p>
<?php
$key = "cache_test_1";
if (MemoryHas($key)) {
    $data = MemoryGet($key);
    echo "Данные из кеша:";
    echo "<pre class='p-3 border rounded'>{$data}</pre>";
} else {
    $data    = 'ЭТО КЕШ ДАНННЫЕ<br>ВРЕМЯ ГЕНЕРАЦИИ: ' . FormatDate(time());
    $isSaved = MemorySet($key, $data);
    echo "Сохранено ли в кеш? = " . ($isSaved ? 'да' : 'нет');
}
?>