import { addMagicMethods } from "@ts/helpers/addMagicMethods";
import { ModelWindowDB } from "@ts/models/ModelWindowDB";

export class UserModel extends ModelWindowDB {
    constructor(id = null) {
        super('users', +id);
        return addMagicMethods(this);
    }

    override __get(target, prop, receiver) {
        switch (prop) {
            case 'full_name':
                return [receiver.surname, receiver.first_name, receiver.patronymic].filter(v => !!v).join(' ');
            case 'avatar_full_url':
                return `/upl/users/${receiver.avatar}`;
        }
        return super.__get(target, prop, receiver);
    }
}