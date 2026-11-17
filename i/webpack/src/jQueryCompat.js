// Совместимость старых плагинов с jQuery 4: возвращаем удалённые в нём функции-утилиты.
// Нужны magicsuggest-alpine и jquery-form (npm), а также плагинам из i/js/_dev, подключаемым через ExtraPacker.
// В своём коде их не использовать - есть Array.isArray, typeof === 'function', String.prototype.trim и т.д.
import $ from "jquery";

const compat = {
    isArray: Array.isArray,
    isFunction: obj => typeof obj === "function",
    isWindow: obj => obj != null && obj === obj.window,
    isNumeric: obj => {
        const type = typeof obj;
        return (type === "number" || type === "string") && !isNaN(obj - parseFloat(obj));
    },
    trim: text => text == null ? "" : String(text).trim(),
    now: Date.now,
    parseJSON: JSON.parse,
    type: obj => {
        if (obj == null) {
            return obj + "";
        }
        const type = typeof obj;
        if (type !== "object" && type !== "function") {
            return type;
        }
        if (Array.isArray(obj)) {
            return "array";
        }
        const tag = Object.prototype.toString.call(obj).slice(8, -1).toLowerCase();
        return ["boolean", "number", "string", "function", "array", "date", "regexp", "object", "error", "symbol"].includes(tag) ? tag : "object";
    },
};

for (const [name, fn] of Object.entries(compat)) {
    if (typeof $[name] !== "function") {
        $[name] = fn;
    }
}

export default $;
