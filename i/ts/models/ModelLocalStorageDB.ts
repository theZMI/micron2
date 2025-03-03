import { Model } from "@/models/Model";
import FallbackDB from "@/libraries/FallbackDB";

export class ModelLocalStorageDB extends Model {
    getTableData() {
        const data = FallbackDB.restore(this.table);
        return data || [];
    }

    setTableData(data: []) {
        FallbackDB.save(this.table, data);
    }
}