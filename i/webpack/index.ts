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
import "@ts/_dev/pwd_shower";
import {createApp} from "vue/dist/vue.esm-bundler";
import {UserModel} from "@ts/models/UserModel";
import {TaskModel} from "@ts/models/TaskModel";

window.bootstrap = bootstrap;
window.Vue = {createApp};
window.UserModel = UserModel;
window.TaskModel = TaskModel;

$.webpackCompitedAt = parseInt(Date.now() / 1000);