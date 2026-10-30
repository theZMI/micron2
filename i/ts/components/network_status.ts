export function updateNetworkStatus(statusName: string) {
    const status = document.getElementById('i-network-status');
    const css = `network-status network-status-${statusName}`;
    if (!status) {
        return;
    }
    status.className = css;
}

window.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('online',  () => updateNetworkStatus(navigator.onLine ? 'success' : 'error') );
    window.addEventListener('offline', () => updateNetworkStatus(navigator.onLine ? 'success' : 'error') );
});