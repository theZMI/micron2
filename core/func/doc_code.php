<?php

/**
 * Блок программного кода с подсветкой (см. lib/CodeHighlight.php). Текст выводится как есть, без выполнения.
 * Удобно передавать nowdoc-строкой, тогда внутри можно писать любой php/html:
 *   <?= DocCode(<<<'CODE'
 *       <?php IncludeCom('menu') ?>
 *       CODE, 'php', 'tpl/header.php') ?>
 */
function DocCode(string $code, string $lang = 'php', string $title = ''): string
{
    return CodeHighlight::block($code, $lang, $title);
}
