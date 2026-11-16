<?php

$orders = (new ShopOrderModel())->getListByUserId((int)$g_user->id);
