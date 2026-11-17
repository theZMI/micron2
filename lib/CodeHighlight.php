<?php

/**
 * Блоки программного кода: на сервере код экранируется и оборачивается в <pre><code class="language-...">,
 * а раскрашивает его в браузере highlight.js (i/webpack/src/CodeHighlight.js, грузится только на страницах с кодом).
 *
 * Два способа вставить код:
 *  - в шаблоне: <?= DocCode(<<<'CODE' ... CODE, 'php', 'src/news.php') ?> (см. core/func/doc_code.php)
 *  - в любом тексте (шаблон, языковой файл, страница из админки): [code=php] ... [/code] - заменяется
 *    функцией пост-обработки CodeHighlight::prepare (Config('prepare_functions'))
 */
class CodeHighlight
{
    // Названия языков, которые можно писать в [code=...] и DocCode(), -> язык highlight.js
    const LANGS = [
        'php'        => 'php',
        'html'       => 'xml',
        'xml'        => 'xml',
        'js'         => 'javascript',
        'javascript' => 'javascript',
        'jscript'    => 'javascript',
        'ts'         => 'typescript',
        'typescript' => 'typescript',
        'css'        => 'scss', // Стили сайта пишутся на SCSS/Less - подсветка SCSS подходит и для обычного css
        'scss'       => 'scss',
        'less'       => 'less',
        'bash'       => 'bash',
        'sh'         => 'bash',
        'shell'      => 'bash',
        'env'        => 'ini',
        'ini'        => 'ini',
        'sql'        => 'sql',
        'json'       => 'json',
        'nginx'      => 'nginx',
        'apache'     => 'apache',
        'c++'        => 'cpp',
        'cpp'        => 'cpp',
        'c'          => 'cpp',
        'plain'      => 'plaintext',
        'text'       => 'plaintext',
    ];

    /**
     * HTML блока кода. $code - исходный текст (экранируется здесь)
     */
    public static function block(string $code, string $lang = 'php', string $title = ''): string
    {
        $code  = self::dedent($code);
        $lang  = self::lang($lang, $code);
        $html  = str_replace('[', '&#91;', htmlspecialchars($code, ENT_QUOTES)); // Чтобы пример с [code=...] внутри кода не превратился в блок
        $title = $title !== '' ? '<div class="code-block-title">' . htmlspecialchars($title) . '</div>' : '';
        return "<figure class=\"code-block\">{$title}<pre><code class=\"language-{$lang}\">{$html}</code></pre></figure>";
    }

    /**
     * Функция пост-обработки страницы: [code=язык] ... [/code] -> блок кода
     */
    public static function prepare(string $content): string
    {
        return preg_replace_callback(
            '~\[code=([a-z0-9+#_-]+)\](.*?)\[/code\]~uis',
            fn($m) => self::block(html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5), $m[1]), // Код, вставленный через визуальный редактор, приходит уже экранированным
            $content
        );
    }

    // Язык highlight.js. PHP с HTML или с тегами <?php - это шаблон (php-template): HTML подсвечивается как HTML, PHP - как PHP
    private static function lang(string $lang, string $code): string
    {
        $lang = self::LANGS[strtolower(trim($lang))] ?? 'plaintext';
        return $lang === 'php' && str_contains($code, '<?') ? 'php-template' : $lang;
    }

    // Убирает общий отступ слева и пустые строки по краям: код в шаблонах обычно вложен в разметку
    private static function dedent(string $code): string
    {
        $lines  = explode("\n", str_replace(["\r\n", "\r"], "\n", $code));
        while ($lines && trim($lines[0]) === '') {
            array_shift($lines);
        }
        while ($lines && trim(end($lines)) === '') {
            array_pop($lines);
        }
        $indent = null;
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $indent = min($indent ?? PHP_INT_MAX, strlen($line) - strlen(ltrim($line, " \t")));
            }
        }
        return implode("\n", array_map(fn($line) => rtrim(substr($line, (int)$indent)), $lines));
    }
}
