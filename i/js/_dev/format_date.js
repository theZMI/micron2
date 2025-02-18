export function formatDate(timestamp) {
    const dt = new Date(timestamp * 1000);
    return `${dt.getDate().toString().padStart(2, '0')}-${(dt.getMonth() + 1).toString().padStart(2, '0')}-${dt.getFullYear().toString().padStart(4, '0')}`;
}

export function formatDateTime(timestamp) {
    const dt = new Date(timestamp * 1000);
    return `${dt.getDate().toString().padStart(2, '0')}-${(dt.getMonth() + 1).toString().padStart(2, '0')}-${dt.getFullYear().toString().padStart(4, '0')} ${dt.getHours().toString().padStart(2, '0')}:${dt.getMinutes().toString().padStart(2, '0')}:${dt.getSeconds().toString().padStart(2, '0')}`;
}

export function formatTimer(timeInSeconds, withSeconds = true) {
    const dt = new Date(timeInSeconds * 1000);
    const h = dt.getUTCHours().toString().padStart(2, '0');
    const m = dt.getUTCMinutes().toString().padStart(2, '0');
    const s = withSeconds ? dt.getUTCSeconds().toString().padStart(2, '0') : '';
    return (+h > 0 ? `${h}:` : '') + [m, s].filter(v => !!v).join(':');
}

export function getNow() {
    return Math.round((+new Date()) / 1000);
}