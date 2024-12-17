import { ModelWindowDB } from "@ts/models/ModelWindowDB";
import { addMagicMethods } from "@ts/helpers/addMagicMethods";
import { UserModel } from "@ts/models/UserModel";
import { formatDateTime } from "@js/_dev/format_date";

export class TaskModel extends ModelWindowDB {
    constructor(id = null) {
        super('tasks', +id);
        return addMagicMethods(this);
    }

    override __get(target, prop, receiver) {
        let ret;
        switch (prop) {
            case 'user':
                ret = new UserModel(+receiver.user_id);
                break;
            case 'deadline_inFormat':
                ret = formatDateTime(receiver.deadline_time);
                break;
            default:
                ret = super.__get(target, prop, receiver);
        }
        return ret;
    }
}

