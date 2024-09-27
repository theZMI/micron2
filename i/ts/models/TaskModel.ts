import {magicMethods} from "@js/_dev/magicMethods";
import {ModelWindowDB} from "@ts/models/ModelWindowDB";
import {UserModel} from "@ts/models/UserModel";
import {formatDateTime} from "@js/_dev/format_date";

export const TaskModel = magicMethods(class TaskModel extends ModelWindowDB {
    constructor(id = null) {
        super('tasks', +id);
    }

    __get(name: string) {
        let ret;
        switch (name) {
            case 'user':
                ret = new UserModel(+this.user_id);
                break;
            case 'deadline_inFormat':
                ret = formatDateTime(this.deadline_time);
                break;
            default:
                ret = super.__get(name);
        }
        return ret;
    }
});

