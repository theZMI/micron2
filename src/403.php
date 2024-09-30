<?php

header(Php::status(403));
IncludeCom('_status_page', ['title' => '403', 'message' => 'Нет доступа к разделу']);