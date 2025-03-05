import { getNow } from "@/helpers/datetime";
export class Model {
    getDataFromDB(id) {
        return this.getTableData().find(v => +v.id === +id) || {};
    }
    setDataToDB(id) {
        const all = this.getTableData();
        const data = this.getData();
        let found = all.findIndex(v => +v.id === +id);
        found = ~found ? found : all.findIndex(v => +v.id === +data.id);
        ~found
            ? all[found] = data
            : all.push(data);
        this.setTableData(all);
        return id;
    }
    constructor(table, id = null) {
        this.data = {};
        this.table = table;
        if (id) {
            this.setData(this.getDataFromDB(id));
        }
    }
    getData() {
        return this.data;
    }
    setData(data) {
        this.data = data || {};
    }
    isExists() {
        const arr = Object.values(this.getData()).filter(v => !!v);
        const isEmptyModel = arr.length === 0;
        return this.id !== undefined && !isEmptyModel;
    }
    hasChanges() {
        const was = this.getDataFromDB(this.id);
        const res = JSON.stringify(was) !== JSON.stringify(this.getData());
        return res;
    }
    getList() {
        const all = this.getTableData();
        return all.map(v => new this.constructor(v.id));
    }
    flush() {
        if (!this.hasChanges()) {
            return 0;
        }
        this.last_update_time = +getNow();
        return +this.setDataToDB(+this.id);
    }
    __get(target, prop, receiver) {
        if (target[prop] !== undefined) {
            return target[prop];
        }
        else if (target.data[prop] !== undefined) {
            return target.data[prop];
        }
        return undefined;
    }
    __set(target, prop, value, receiver) {
        if (target[prop] !== undefined) {
            target[prop] = value;
        }
        else if (target.data[prop] !== undefined) {
            target.data[prop] = value;
        }
        else {
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
//# sourceMappingURL=Model.js.map