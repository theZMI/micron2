<?php

$products = array_slice((new ShopProductModel())->search(['sort' => 'default', 'per_page' => 8])['items'], 0, 8);
