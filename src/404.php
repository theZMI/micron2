<?php

header(Php::status(404));
IncludeCom('_status_page', ['title' => '404', 'message' => 'Страница не найдена']);