// @ts-nocheck
"use strict";

// import "@webpack/test"; // Just example to include js/ts/less/css files
import "@scss/_all";
import * as bootstrap from "bootstrap";
import $ from "jquery";
import "@webpack/src/jQueryCompat"; // Удалённые в jQuery 4 $.isArray, $.trim и т.п. - для старых плагинов, до их подключения
import "jquery-form"; // Plugin ajaxForm, ajaxSubmit
import "@scss/_dev/_ajax_upload"; // Styles for _ajax_upload.php
import "@scss/_dev/_crop_image"; // Styles for _crop_image.php
import { apply as viewportExtraApply } from "viewport-extra"; // Минимальная ширина страницы на узких экранах (масштабирует вниз)
import "magicsuggest-alpine/magicsuggest";
import "magicsuggest-alpine/magicsuggest.css";
import "@scss/components/magicsuggest.scss";
import "@webpack/src/MainMenuInit";
import "@webpack/src/shop";
import "@webpack/src/IMaskInit";
import "@webpack/src/CodeHighlightInit"; // Подсветка блоков кода (highlight.js грузится отдельно, только где есть код)
import "@ts/helpers/pwdShower";
import { trClick } from "@ts/helpers/trClick";
import { createApp } from "vue/dist/vue.esm-bundler";
import { UserModel } from "@ts/models/UserModel";
import { updateNetworkStatus } from "@ts/components/network_status";
import pinCodeForm from "@webpack/src/pinCodeForm";

// На экранах уже 400px страница уменьшается, а не ломает вёрстку
viewportExtraApply([{ content: { minimumWidth: 400 } }]);

window.bootstrap = bootstrap;
window.Vue = { createApp };
window.UserModel = UserModel;
window.trClick = trClick;
window.updateNetworkStatus = updateNetworkStatus; // Индикатор сети (компонент _dev/network_status), вызывается из _dev/pwa
window.pinCodeForm = pinCodeForm; // Vue-mixin для компонента _dev/pin_code_block

$.webpackCompitedAt = parseInt(Date.now() / 1000);