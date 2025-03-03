import { Model } from "@/models/Model";
export class ModelWindowDB extends Model {
    getTableData() {
        return window.DB[this.table] || [];
    }
    setTableData(data) {
        window.DB[this.table] = data;
    }
    hasChanges() {
        return true;
    }
}
//# sourceMappingURL=ModelWindowDB.js.map