<?php if ($hasManifest): ?>
    <link rel="manifest" href="<?= Root('manifest.json') ?>" />
<?php endif; ?>

<!-- Application icon for IOS dashboard -->
<link rel="apple-touch-icon" href="<?= Root('i/image/touch_icon/180x180.png') ?>"/>
<meta name="apple-mobile-web-app-title" content="<?= L('m_shortTitle') ?>"/>

<!-- Accept fullscreen mode for PWA -->
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="mobile-web-app-capable" content="yes" />
<meta name="apple-touch-fullscreen" content="yes" />

<!-- Style for top status bar -->
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<!-- Tab color for Chrome, Firefox OS and Opera -->
<meta name="theme-color" content="#000000" />

<!-- Tab color for Windows Phone -->
<meta name="msapplication-navbutton-color" content="#000000" />

<!-- Disables automatic detection of possible phone numbers in a webpage in Safari on iOS -->
<meta name="format-detection" content="telephone=no" />
<meta name="format-detection" content="address=no" />

<!-- Open links in current window if this site install like PWA application -->
<script type="text/javascript" src="<?= Root('i/js/_dev/jquery.stayInWebApp.js') ?>"></script>
<script>
    const registerServiceWorker = async () => {
        if (!('serviceWorker' in navigator)) {
            console.log('Service workers are not support');
            return;
        }
        try {
            await navigator.serviceWorker.register('<?= Root('service_worker.js') ?>');
            console.log('Service worker was registered');
        } catch (e) {
            console.error('Error of registration service worker');
        }

        // Service worker сообщает о состоянии запросов - показываем его в компоненте _dev/network_status
        navigator.serviceWorker.onmessage = event => {
            const statuses = {REQUEST_LOADING: 'loading', REQUEST_FAILED: 'error', REQUEST_SUCCESSFUL: 'success'};
            if (statuses[event.data.status] && window.updateNetworkStatus) {
                window.updateNetworkStatus(statuses[event.data.status]);
            }
        };
    };

    $(async () => {
        $.stayInWebApp();
        <?php if ($hasServiceWorker && !IsLocalEnv()): ?>
            await registerServiceWorker();
        <?php endif; ?>
    });
</script>
