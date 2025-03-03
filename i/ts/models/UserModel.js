import { addMagicMethods } from "@ts/helpers/addMagicMethods";
import { ModelWindowDB } from "@ts/models/ModelWindowDB";
export class UserModel extends ModelWindowDB {
    constructor(id = null) {
        super('users', +id);
        return addMagicMethods(this);
    }
    __get(target, prop, receiver) {
        switch (prop) {
            case 'full_name':
                return [receiver.surname, receiver.first_name, receiver.patronymic].filter(v => !!v).join(' ');
        }
        return super.__get(target, prop, receiver);
    }
}
//# sourceMappingURL=UserModel.js.map