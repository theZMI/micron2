export function addMagicMethods(modelObject) {
    return new Proxy(
        modelObject,
        {
            get: (target, prop, receiver) => target.__get(target, prop, receiver),
            set: (target, prop, value, receiver) => target.__set(target, prop, value, receiver)
        }
    );
}