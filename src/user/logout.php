<?php

$g_user->logout();
UrlRedirect::go(SiteRoot('user/login'));