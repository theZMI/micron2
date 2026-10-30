// @ts-nocheck
"use strict";

// import "@webpack/test"; // Just example to include js/ts/less/css files
import "@scss/_all";
import * as bootstrap from "bootstrap";
import $ from "jquery";
import "jquery-form"; // Plugin ajaxForm, ajaxSubmit
import "@scss/_dev/_ajax_upload"; // Styles for _ajax_upload.php
import "@scss/_dev/_crop_image"; // Styles for _crop_image.php
import "viewport-extra"; // For switch on min-width in viewport-tag
import "magicsuggest-alpine/magicsuggest";
import "magicsuggest-alpine/magicsuggest.css";
import "@scss/components/magicsuggest.scss";
import "@webpack/src/MainMenuInit";
import "@webpack/src/IMaskInit";
import "@ts/helpers/pwdShower";
import { trClick } from "@ts/helpers/trClick";
import { createApp } from "vue/dist/vue.esm-bundler";
import { UserModel } from "@ts/models/UserModel";
import { updateNetworkStatus } from "@ts/components/network_status";
import pinCodeForm from "@webpack/src/pinCodeForm";

window.bootstrap = bootstrap;
window.Vue = { createApp };
window.UserModel = UserModel;
window.trClick = trClick;
window.updateNetworkStatus = updateNetworkStatus; // Индикатор сети (компонент _dev/network_status), вызывается из _dev/pwa
window.pinCodeForm = pinCodeForm; // Vue-mixin для компонента _dev/pin_code_block

$.webpackCompitedAt = parseInt(Date.now() / 1000);