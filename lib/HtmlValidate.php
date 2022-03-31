<?php

/**
 * Валидатор html кода
 *
 * Сливает head-ы в один
 */
class HtmlValidate
{
    private $html;

    public function __construct($html)
    {
        $this->html = $this->headBodyMerge($html);
    }

    public function get()
    {
        return $this->html;
    }

    /**
     * Убирает вложенные head-ы
     */
    private function deleteInnerHtml($html)
    {
        // Если нет head-ов то и делать тут нечего
        preg_match('~<head(.*?)>~is', $html, $m);
        if (!isset($m[0])) {
            return $html;
        }

        // Заменяем первый head с атрибутами на обычный что бы проще было искать
        $headAttrs = isset($m[1]) ? $m[1] : ''; // Атрибуты главного head-а если были
        $html      = $m[0] === '<head>' ? $html : _StrReplaceFirst($m[0], '<head>', $html);

        do {
            $headOffset = 0;
            do {
                $open        = stripos($html, "<head>", $headOffset); // Находим первое открытие
                $close       = stripos($html, "</head>", $headOffset); // Находим первое закрытие
                $nextOpen    = stripos($html, "<head>", intval($open + strlen("<head>")));
                $isInnerOpen = $nextOpen < $close;
                if (!$isInnerOpen) {
                    $headOffset = $nextOpen;
                }
            } while (!$isInnerOpen && $nextOpen !== false);

            // Если следущего head-а не было, то значит от того head-а который взяли вложенных не стояло, потому просто выходим
            if ($nextOpen === false) {
                break;
            }

            // Подсчитаем сколько вложенных <head> без закрытия идёт
            $countInnerOpens = 0;
            $innerHeadOffset = $open + strlen("<head>"); // Кусок от которого считаем это за первым head-ом
            do {
                $pos         = stripos($html, "<head>", $innerHeadOffset);
                $isInnerOpen = $pos < $close && $pos !== false;
                if ($isInnerOpen) {
                    $countInnerOpens++;
                    $innerHeadOffset = $pos + strlen("<head>");
                }
            } while ($isInnerOpen);

            // Убираем столько внутренних head-ов и сразу за ними закрывающих head-ов сколько посчитали как вложенных
            $replaceOffset = $open + strlen("<head>");
            for ($i = 0; $i < $countInnerOpens; $i++) {
                $html = _StrReplaceFirst("<head>", "", $html, $replaceOffset);
                $html = _StrReplaceFirst("</head>", "", $html, $replaceOffset);
            }
        } while (true);

        // Возвращаем доп. теги в главный head если они были
        $ret = _StrReplaceFirst("<head>", "<head{$headAttrs}>", $html);

        return $ret;
    }

    /**
     * Сливает несколько разделов head в 1
     */
    private function headBodyMerge($html)
    {
        $html = $this->deleteInnerHtml($html);

        preg_match_all('~<head(.*?)>(.*?)</head>~is', $html, $m);

        // Если <head>...</head> больше чем 1 тогда имеет смысл их объединять
        if (count($m[0]) > 1) {
            // Собираем общий head
            $head = '<head' . $m[1][0] . '>' . implode('', array_filter(array_unique($m[2]))) . '</head>';

            // Заменяем все <head>...</head> на 1 слитый
            $html = str_replace($m[0][0], $head, $html);
            $html = str_replace(array_splice($m[0], 1), '', $html);
        }

        return $html;
    }
}
