// Блоки кода (их размечает lib/CodeHighlight.php: <figure class="code-block"><pre><code class="language-...">):
//  - кнопка «Скопировать» в правом верхнем углу
//  - подсветка: highlight.js грузится отдельным файлом (dist/highlight.js) и только на страницах, где есть код

// Копирование в буфер. navigator.clipboard работает только по https (и на localhost), поэтому есть запасной вариант
function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }
    return new Promise((resolve, reject) => {
        const area = document.createElement('textarea');
        area.value = text;
        area.setAttribute('readonly', '');
        area.style.cssText = 'position: fixed; top: -1000px; opacity: 0;';
        document.body.appendChild(area);
        area.select();
        const ok = document.execCommand('copy');
        area.remove();
        ok ? resolve() : reject(new Error('copy failed'));
    });
}

function addCopyButton(figure) {
    const code = figure.querySelector('pre code');
    if (!code || figure.querySelector('.code-block-copy')) {
        return;
    }
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'code-block-copy';
    button.innerHTML = '<i class="bi bi-copy"></i><span>Скопировать</span>';
    button.setAttribute('aria-label', 'Скопировать код');
    button.addEventListener('click', () => {
        copyText(code.textContent).then(
            () => showState(button, 'bi-check2', 'Скопировано'),
            () => showState(button, 'bi-x', 'Не удалось')
        );
    });
    figure.appendChild(button);
}

function showState(button, icon, text) {
    clearTimeout(button._resetTimer);
    button.classList.add('is-done');
    button.innerHTML = `<i class="bi ${icon}"></i><span>${text}</span>`;
    button._resetTimer = setTimeout(() => {
        button.classList.remove('is-done');
        button.innerHTML = '<i class="bi bi-copy"></i><span>Скопировать</span>';
    }, 1500);
}

function initCodeBlocks() {
    document.querySelectorAll('.code-block').forEach(addCopyButton);

    const blocks = document.querySelectorAll('pre code[class*="language-"]:not(.hljs)');
    if (!blocks.length) {
        return;
    }
    import(/* webpackChunkName: "highlight" */ './CodeHighlight').then(({ default: hljs }) => {
        blocks.forEach(block => hljs.highlightElement(block));
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCodeBlocks);
} else {
    initCodeBlocks();
}
