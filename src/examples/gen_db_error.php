<?php

$db = GetDefaultDb();
$db->query("SELECT * FROM `no_exists_table`");

echo "Теперь надо проверить лог ошибочных запросов в tmp-папке и тут, в debug-panel-е внизу (справа внизу страницы, если DEBUG_MODE=1)";