<?php // Тема сайта: выбор посетителя > тема ОС > тёмная. Ставится до отрисовки, чтобы не было вспышки белого в тёмной теме ?>
<meta name="theme-color" content="#000000"/>
<script>
    function SiteSetThemeColor(theme) {
        document.querySelector('meta[name="theme-color"]').content = theme === 'light' ? '#FFFFFF' : '#000000';
    }

    (function () {
        var theme = null;
        try {
            theme = localStorage.getItem('site-theme');
        } catch (e) {
        }
        if (theme !== 'light' && theme !== 'dark') {
            theme = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
        }
        document.documentElement.setAttribute('data-bs-theme', theme);
        SiteSetThemeColor(theme);
    })();

    // Переключатель в шапке. Новая тема раскрывается кругом от переключателя (View Transitions API), где его нет - меняется сразу
    function SiteToggleTheme(button) {
        var root = document.documentElement;
        var theme = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        try {
            localStorage.setItem('site-theme', theme);
        } catch (e) {
        }
        var apply = function () {
            root.setAttribute('data-bs-theme', theme);
            SiteSetThemeColor(theme);
        };
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!document.startViewTransition || reduce || !button) {
            apply();
            return;
        }
        var rect = button.getBoundingClientRect();
        var x = rect.left + rect.width / 2, y = rect.top + rect.height / 2;
        var radius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
        document.startViewTransition(apply).ready.then(function () {
            root.animate(
                {clipPath: ['circle(0 at ' + x + 'px ' + y + 'px)', 'circle(' + radius + 'px at ' + x + 'px ' + y + 'px)']},
                {duration: 600, easing: 'ease-in-out', pseudoElement: '::view-transition-new(root)'}
            );
        });
    }
</script>
