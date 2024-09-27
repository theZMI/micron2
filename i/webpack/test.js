// Тестовый пример для подключения css/less/json/js/ts файлов
import "@css/test.css";
import "@less/test.less";
import {this_is_a_js_test} from "@js/test";
import {this_is_a_ts_test} from "@ts/test";
import countries from "@data/countries.json";

console.log('Countries from json file: ', countries);
console.log(this_is_a_ts_test());
console.log(this_is_a_js_test());
setTimeout(() => {
    $('#wrapper')
        .append('<div class="this-is-css-test">This is a CSS test</div>')
        .append('<div class="this-is-less-test">This is a LESS test</div>');
}, 2500);