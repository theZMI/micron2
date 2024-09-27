<?php

class SyntaxHighlighter
{
    const TAG = '<!-- syntaxHighlighterListLanguages -->';

    private static $langToIncFile = [
        'bash'           => 'shBrushBash',
        'cpp'            => 'shBrushCpp',
        'cshshlfilesarp' => 'shBrushCSharp',
        'css'            => 'shBrushCss',
        'delphi'         => 'shBrushDelphi',
        'diff'           => 'shBrushDiff',
        'gvoovy'         => 'shBrushGroovy',
        'java'           => 'shBrushJava',
        'jscript'        => 'shBrushJScript',
        'php'            => 'shBrushPhp',
        'plain'          => 'shBrushPlain',
        'python'         => 'shBrushPython',
        'ruby'           => 'shBrushRuby',
        'scala'          => 'shBrushScala',
        'sql'            => 'shBrushSql',
        'vb'             => 'shBrushVb',
        'xml'            => 'shBrushXml'
    ];

    private static $langToAbbrs = [
        'bash'    => ['bash'],
        'cpp'     => ['cpp', 'c++', 'c', 'cplusplus', 'c-plusplus'],
        'csharp'  => ['csharp', 'c#', 'c-sharp'],
        'css'     => ['css'],
        'delphi'  => ['delphi', 'pascal'],
        'diff'    => ['diff'],
        'gvoovy'  => ['gvoovy'],
        'java'    => ['java'],
        'jscript' => ['jscript', 'js', 'javascript'],
        'php'     => ['php'],
        'plain'   => ['plain'],
        'python'  => ['python'],
        'ruby'    => ['ruby'],
        'scala'   => ['scala'],
        'sql'     => ['sql', 'mysql'],
        'vb'      => ['vb', 'visualbasic', 'basic', 'visual-basic'],
        'xml'     => ['xml', 'html']
    ];

    private static function getLangByAbbr($abbr)
    {
        $ret = 'bash';
        foreach (self::$langToAbbrs as $name => $variants) {
            foreach ($variants as $v) {
                if (strtolower($abbr) !== $v) {
                    continue;
                }

                $ret = $name;
                break(2);
            }
        }
        return $ret;
    }

    private static function getIncFileByAbbr($abbr)
    {
        $lang = self::getLangByAbbr($abbr);
        return self::$langToIncFile[$lang] ?? '';
    }

    public static function highlight($c)
    {
        if (!str_contains($c, self::TAG)) {
            return $c;
        }

        // Выбора всех [code=ЯЗЫК]...[/code]
        $regexp = "~\[code=(.*?)\](.*?)\[/code\]~uis";

        // Получаем список всех используемых языков, чтобы потом подключить js файлы с их подстветкой
        preg_match_all($regexp, $c, $m);
        $foundAbbrs = isset($m[1]) ? array_unique($m[1]) : [];

        if (!empty($foundAbbrs)) {
            $incScripts = [];
            foreach ($foundAbbrs as $abbr) {
                $incJsFile    = Root('i/js/_dev/syntaxhighlighter/' . self::getIncFileByAbbr($abbr) . '.js');
                $incScripts[] = "<script type='text/javascript' src='{$incJsFile}'></script>";
            }
            $c = str_replace(self::TAG, join('', $incScripts), $c);
        }

        // Заменяем все [code] ... [/code] на <pre> ... </pre>
        return preg_replace_callback(
            $regexp,
            function ($m) {
                $charset = Config('charset');
                $input   = new InputClean($charset);
                $abbr    = isset($m[1]) ? htmlentities($m[1], ENT_QUOTES) : '';
                $abbr    = $input->xss_clean($abbr);
                $text    = $input->xss_clean($m[2] ?? '');
                $lang    = self::getLangByAbbr($abbr);
                return "<pre class='brush: {$lang};'>{$text}</pre>";
            },
            $c
        );
    }
}