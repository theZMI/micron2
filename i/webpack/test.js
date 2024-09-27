// Тестовый пример для подключения css/less/json/js/ts файлов
import "@css/test.css";
import "@less/test.less";
import {this_is_js_test} from "@js/test";
import {this_is_ts_test} from "@ts/test";
import countries from "@data/countries.json";

console.log('countries from json file: ', countries);
console.log(this_is_ts_test());
console.log(this_is_js_test());
setTimeout(() => {
    $('#wrapper')
        .append('<div class="this-is-css-test">This is CSS test</div>')
        .append('<div class="this-is-less-test">This is LESS test</div>');
}, 2500);