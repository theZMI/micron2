export abstract class Model {
    protected data: {[key: string]: any} = {};
    protected table: string

    constructor(table: string, id = null) {
        this.table = table;
        if (id) {
            this.setData(this.getDataFromDB(id));
        }
    }

    getData() {
        return this.data;
    }

    setData(data: { [key: string]: any }) {
        this.data = data;
    }

    isExists() {
        return !!this.id;
    }

    abstract getDataFromDB(id: number): Object;

    abstract flush(): number;

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
}