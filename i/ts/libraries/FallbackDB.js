export default {
    save(table, data) {
        localStorage.setItem(`DB_${table}`, JSON.stringify(data));
    },

    restore(table) {
        return JSON.parse( String(localStorage.getItem(`DB_${table}`)) ) || [];
    }
};