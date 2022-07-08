function DebugPanel() {
    this.toggle = function (id) {
        var e = document.getElementById(id);
        e.style.display = (e.style.display === 'none' ? 'block' : 'none');
    }

    this.togglePanel = function () {
        this.toggle('i-debug-panel-all-panels');
        this.toggle('i-debug-panel-list');
        this.toggle('i-show-profile');
    }

    this.showExtensionFuncs = function (id) {
        var e = document.getElementById(id);
        document.getElementById('i-ext-show').innerHTML = e.innerHTML;
    }
}

var g_debug = new DebugPanel();