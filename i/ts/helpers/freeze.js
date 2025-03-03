export function freeze(object) {
    return JSON.parse(JSON.stringify(object));
}