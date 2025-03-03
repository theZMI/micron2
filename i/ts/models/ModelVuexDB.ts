import { Model } from "@/models/Model";

export class ModelVuexDB extends Model {
    // В наследнике надо определить getTableData() и setTableData() для получения и сохранения таблицы данных во vuex-переменную

    hasChanges() { // ModelVuexDB меняет данные в реактивных переменных сразу по ходу присвоения данных. Потому в момент flush никакого прошлого значения нет
        return true;
    }
}