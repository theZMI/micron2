import { getNow } from "@/helpers/format_date";

export abstract class Model {
    public data: {[key: string]: any} = {};
    public table: string

    // Возвращает данные всей таблицы из локальной БД (нужна в getDataFromDB)
    abstract getTableData(): [];

    // Записывает все данные для локальной таблицы
    abstract setTableData(data: []): void;

    getDataFromDB(id: number): Object {
        return this.getTableData().find(v => +v.id === +id) || {};
    }

    setDataToDB(id: number): number {
        const all = this.getTableData();
        const data = this.getData();
        let found = all.findIndex(v => +v.id === +id); // Пробуем найти данные с переданным id
        found = ~found ? found :all.findIndex(v => +v.id === +data.id); // Если с таким не нашли, то возможно какое-то запрос до уже исправил временный id и потому пытается найти уже с нормальным id

        ~found
            ? all[found] = data // if (k !== -1) then update row
            : all.push(data); // Add new row
        this.setTableData(all);

        return id;
    }

    constructor(table: string, id = null) {
        this.table = table;
        if (id) {
            this.setData(this.getDataFromDB(id));
        }
    }

    // Получает поле data объекта
    getData() {
        return this.data;
    }

    // Устанавливает поле data объекта (нужен в конструкторе, чтобы установить начальную data)
    setData(data: { [key: string]: any }) {
        this.data = data || {};
    }

    isExists() {
        const arr = Object.values( this.getData() ).filter(v => !!v);
        const isEmptyModel = arr.length === 0;
        return this.id !== undefined && !isEmptyModel;
    }

    // Проверяет были ли внесены изменения в данные объекта
    hasChanges() {
        const was = this.getDataFromDB(this.id);
        const res = JSON.stringify(was) !== JSON.stringify(this.getData())
        return res;
    }

    getList() {
        const all = this.getTableData();
        return all.map(v => new this.constructor(v.id));
    }

    flush(): number {
        if (!this.hasChanges()) {
            return 0;
        }
        this.last_update_time = +getNow();
        return +this.setDataToDB(+this.id);
    }

    __get(target, prop, receiver) {
        if (target[prop] !== undefined) { // If variable exists into object, then return it
            return target[prop];
        } else if (target.data[prop] !== undefined) { // If this is a property into data-field, then return data[property]
            return target.data[prop];
        }
        return undefined;
    }

    __set(target, prop, value, receiver) {
        if (target[prop] !== undefined) {
            target[prop] = value;
        } else if (target.data[prop] !== undefined) {
            target.data[prop] = value;
        } else {
            target[prop] = value;
        }
        return true;
    }

    delete() {
        let all = this.getTableData();
        let id = this.id;
        all = all.filter(v => +v.id !== +id);
        this.setTableData(all);
        return true;
    }
}