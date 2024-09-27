export abstract class Model {
    protected data: { [key: string]: any } = {};
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

    __get(name: string) {
        let ret;
        switch (name) {
            default:
                ret = this.data[name] || null;
        }
        return ret;
    }

    abstract getDataFromDB(id: number): Object;

    abstract flush(): number;
}