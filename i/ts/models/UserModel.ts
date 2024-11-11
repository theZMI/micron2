import {magicMethods} from "@js/_dev/magicMethods";
import {ModelWindowDB} from "@ts/models/ModelWindowDB";

export const UserModel = magicMethods(class UserModel extends ModelWindowDB {
    constructor(id = null) {
        super('users', +id);
    }

    __get(name: string) {
        let ret;
        switch (name) {
            case 'full_name':
                ret = [this.first_name, this.last_name].filter(v => !!v).join(' ');
                break;
            default:
                ret = super.__get(name);
        }
        return ret;
    }
});