import {magicMethods} from "@js/_dev/magicMethods";
import {Model} from "@ts/models/Model";

export const ModelWindowDB = magicMethods(class ModelWindowDB extends Model {
    getDataFromDB(id: number): Object {
        const all = window.DB[this.table];
        return all.find(v => +v.id === id);
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

    // Don't remove, because it's need to magicMethods
    override __get(name: string) {
        return super.__get(name);
    }
});