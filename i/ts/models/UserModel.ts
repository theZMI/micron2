import { addMagicMethods } from "@/helpers/addMagicMethods";
import { ModelWindowDB } from "@/models/ModelWindowDB";

export class UserModel extends ModelWindowDB {
    constructor(id = null) {
        super('users', +id);
        return addMagicMethods(this);
    }

    override __get(target, prop, receiver) {
        switch (prop) {
            case 'full_name':
                return [receiver.surname, receiver.first_name, receiver.patronymic].filter(v => !!v).join(' ');
        }
        return super.__get(target, prop, receiver);
    }
}