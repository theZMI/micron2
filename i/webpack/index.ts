"use strict";

import "@scss/all.scss";

import $ from "jquery";
import "viewport-extra"; // For switch on min-width in viewport-tag
import {MainMenu} from "@ts/MainMenu";

let g_mainMenu = new MainMenu();

$(function () {
    g_mainMenu.init();
})