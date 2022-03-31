function DebugPanel_Toggle(id) {
    var e = document.getElementById(id);
    e.style.display = (e.style.display === 'none' ? 'block' : 'none');
}

function DebugPanel_ShowHidePanel() {
    DebugPanel_Toggle('i-debug-panel-all-panels');
    DebugPanel_Toggle('i-debug-panel-list');
    DebugPanel_Toggle('i-show-profile');
}

function DebugPanel_ShowExtensionFuncs(id) {
    var e = document.getElementById(id);
    document.getElementById('i-ext-show').innerHTML = e.innerHTML;
}
