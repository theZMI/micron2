import { Model } from "@ts/models/Model";

export class ModelWindowDB extends Model {
    getTableData() {
        return window.DB[this.table];
    }

    setTableData(data: []) {
        window.DB[this.table] = data;
    }

    getDataFromDB(id: number): Object {
        const all = this.getTableData();
        return all.find(v => +v.id === id);
    }

    hasChanges() {
        return true;
    }

    flush(): number {
        const all = window.DB[this.table];
        for (const k in all) {
            const v = all[k];
            if (+v.id === this.id) {
                all[k] = this.getData();
                return +this.id;
            }
        }
        return 0;
    }
}