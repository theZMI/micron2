import Api from "@/libraries/Api";
import { getNow } from "@/helpers/datetime";

export default {
    TICK_TIME: 250,
    queue: [],

    REPEAT_UNTIL_SUCCESS: 1,
    HIGH_PRIORITY: 10,
    REPLACE_EQUAL: 100,

    async callRequest(n) {
        const a = this.queue[n];
        const method = a.request.method || 'get';

        a.request_done_time = 0;
        try {
            const response = await Api[method](a.request.uri, a.request.data);
            a.resolve(response);
            a.request_done_time = +getNow();
        } catch (e) {
            if (!a.repeat_until_success) {
                a.reject(e.data);
                a.request_done_time = +getNow();
            }
        }
    },

    async loop() { // Проходится по очереди поставленных запросов в Api и выполняет каждый их них. В случае если action поставлен с параметром repeat_until_success, то он не удалиться из очереди до тех пор, пока не сервер не выполнит его удачно
        await Promise.allSettled( this.queue.map((v, n) => this.callRequest(n)) );
        this.queue = this.queue.filter(v => !v.request_done_time);
    },

    init() { // Вызывает loop каждые TICK_TIME секунд (если прошлый вызов уже завершился, иначе ждёт)
        let locked = false;

        const worker = async () => {
            if (locked) {
                return;
            }
            locked = true;

            await this.loop();

            locked = false;
        }

        setInterval(worker, this.TICK_TIME);
        worker();
    },

    add(pack) {
        return new Promise((resolve, reject) => {
            this.queue.push({
                ...pack,
                resolve,
                reject,
                create_request_time: +getNow(),
            });
        });
    },

    // Добавляет action в самый верх очереди событий (чтобы выполнилось первым)
    addToTop(pack) {
        return new Promise((resolve, reject) => {
            const firstAction = {
                ...pack,
                resolve,
                reject,
                create_request_time: +getNow(),
            };
            this.queue = [firstAction].concat(this.queue);
        });
    },

    // Заменит в queue action с таким же uri, а если его нет, то создаст его внизу списка
    replace(pack) {
        const found = this.queue.findIndex(v => v.request.uri === pack.request.uri);
        if (~found) {
            return new Promise((resolve, reject) => {
                this.queue[found] = {
                    ...pack,
                    resolve,
                    reject,
                    create_request_time: +getNow(),
                };
            });
        }
        return this.add(pack);
    },

    replaceToTop(pack) {
        const found = this.queue.find(v => v.request.uri === pack.request.uri);
        if (found) {
            return new Promise((resolve, reject) => {
                this.queue[found] = {
                    ...pack,
                    resolve,
                    reject,
                    create_request_time: +getNow(),
                };
            });
        }
        return this.addToTop(pack);
    },

    _call(uri, method, data, params) {
        const repeat_until_success = Boolean(params & this.REPEAT_UNTIL_SUCCESS);
        const funcPrefix = Boolean(params & this.REPLACE_EQUAL) ? 'replace' : 'add';
        const funcPostfix = Boolean(params & this.HIGH_PRIORITY) ? 'ToTop' : '';
        const func = funcPrefix + funcPostfix;

        return this[func]({request: {uri, method, data}, repeat_until_success});
    },

    get(uri, data = [], params = 0) {
        return this._call(uri, 'get', data, params);
    },

    post(uri, data, params = 0) {
        return this._call(uri, 'post', data, params);
    },

    put(uri, data, params = 0) {
        return this._call(uri, 'put', data, params);
    },

    patch(uri, data, params = 0) {
        return this._call(uri, 'patch', data, params);
    },

    delete(uri, data = [], params = 0) {
        return this._call(uri, 'delete', data, params);
    }
}