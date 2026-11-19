<?php

/**
 * Поиск по товарам без внешних сервисов (для каталогов до нескольких тысяч товаров):
 * - слова ищутся по отдельности и в любом порядке: «синяя футболка» найдёт «Футболка синяя»;
 * - окончания отбрасываются (простой стемминг): «футболки» найдёт «футболка», «синюю» - «синий»;
 * - ищется в названии, артикуле, категории, характеристиках и описании; совпадение в названии весит больше;
 * - запрос в неправильной раскладке исправляется: «aenbjkrf» -> «футболка».
 * Товар находится, только если в нём нашлись все слова запроса
 */
class ShopSearch
{
    // Вес совпадения по полям
    private const WEIGHTS = [
        'name'        => 10,
        'vendor_code' => 15,
        'category'    => 6,
        'params'      => 4,
        'description' => 1,
    ];

    private const STOP_WORDS = ['и', 'в', 'во', 'на', 'с', 'со', 'для', 'по', 'из', 'от', 'к', 'ко', 'у', 'о', 'об', 'а', 'the', 'and', 'for', 'of'];

    // Окончания русских слов, от длинных к коротким
    private const ENDINGS = [
        'иями', 'ями', 'ами', 'ией', 'иям', 'ием', 'иях', 'ого', 'его', 'ому', 'ему', 'ыми', 'ими',
        'ая', 'яя', 'ое', 'ее', 'ые', 'ие', 'ый', 'ий', 'ой', 'ую', 'юю', 'ов', 'ев', 'ей', 'ам', 'ям', 'ах', 'ях', 'ом', 'ем',
        'ия', 'ья', 'ью', 'ии', 'ою', 'ею',
        'а', 'я', 'о', 'е', 'ы', 'и', 'у', 'ю', 'ь', 'й',
    ];

    private const LAYOUT_EN = "qwertyuiop[]asdfghjkl;'zxcvbnm,.`";
    private const LAYOUT_RU = 'йцукенгшщзхъфывапролджэячсмитьбюё';

    // Нижний регистр, ё -> е, всё кроме букв и цифр - пробелы
    public static function normalize(string $text): string
    {
        $text = mb_strtolower(html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8'));
        $text = str_replace('ё', 'е', $text);
        return trim(preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text));
    }

    public static function words(string $text): array
    {
        $text = self::normalize($text);
        return $text === '' ? [] : array_values(array_unique(explode(' ', $text)));
    }

    // Основа слова: без окончания, но не короче 3 букв. Латиница - без множественного -s/-es
    public static function stem(string $word): string
    {
        $len = mb_strlen($word);
        if (preg_match('/^\p{Cyrillic}+$/u', $word)) {
            if ($len < 4) {
                return $word;
            }
            foreach (self::ENDINGS as $ending) {
                $endLen = mb_strlen($ending);
                if ($len - $endLen >= 3 && mb_substr($word, -$endLen) === $ending) {
                    return mb_substr($word, 0, $len - $endLen);
                }
            }
            return $word;
        }
        if (preg_match('/^[a-z]+$/', $word) && $len > 3) {
            return preg_replace('/(es|s)$/', '', $word);
        }
        return $word;
    }

    // Слова запроса без предлогов -> их основы
    public static function queryStems(string $q): array
    {
        $words = array_filter(self::words($q), fn($w) => !in_array($w, self::STOP_WORDS, true));
        return array_values(array_unique(array_map([self::class, 'stem'], $words)));
    }

    // Переключение раскладки: «ghbdtn» -> «привет», «руддщ» -> «hello»
    public static function switchLayout(string $text): string
    {
        $en  = mb_str_split(self::LAYOUT_EN);
        $ru  = mb_str_split(self::LAYOUT_RU);
        $map = array_combine($en, $ru) + array_combine($ru, $en);
        return implode('', array_map(fn($ch) => $map[$ch] ?? $ch, mb_str_split(mb_strtolower($text))));
    }

    /**
     * Ищет запрос в строках товаров. $rows: [['id', 'name', 'vendor_code', 'category', 'params' (массив), 'description'], ...]
     * Возвращает ['scores' => [id => релевантность], 'query' => по какому запросу нашлось (может быть в другой раскладке)]
     */
    public static function search(string $q, array $rows): array
    {
        $scores = self::match($q, $rows);
        if (!$scores) {
            $switched = self::switchLayout($q);
            if ($switched !== mb_strtolower($q) && ($switchedScores = self::match($switched, $rows))) {
                return ['scores' => $switchedScores, 'query' => $switched];
            }
        }
        return ['scores' => $scores, 'query' => $q];
    }

    private static function match(string $q, array $rows): array
    {
        $stems  = self::queryStems($q);
        $phrase = self::normalize($q);
        if (!$stems) {
            return [];
        }

        $ret = [];
        foreach ($rows as $row) {
            $fields = [
                'name'        => self::words((string)$row['name']),
                'vendor_code' => self::words((string)$row['vendor_code']),
                'category'    => self::words((string)($row['category'] ?? '')),
                'params'      => self::words(implode(' ', array_map('strval', array_merge(array_keys((array)$row['params']), array_values((array)$row['params']))))),
                'description' => self::words((string)($row['description'] ?? '')),
            ];
            $score = 0;
            foreach ($stems as $stem) {
                $best = 0;
                foreach ($fields as $field => $words) {
                    if (self::WEIGHTS[$field] <= $best) {
                        continue;
                    }
                    foreach ($words as $word) {
                        if (str_starts_with($word, $stem)) {
                            $best = self::WEIGHTS[$field] + ($word === $stem ? 1 : 0);
                            break;
                        }
                    }
                }
                if ($best === 0) {
                    continue 2; // Не нашлось одно из слов - товар не подходит
                }
                $score += $best;
            }
            // Запрос целиком в названии или точный артикул - выше всех
            $name = self::normalize((string)$row['name']);
            if ($phrase !== '' && str_contains($name, $phrase)) {
                $score += 20;
            }
            if ($phrase !== '' && self::normalize((string)$row['vendor_code']) === $phrase) {
                $score += 50;
            }
            $ret[(int)$row['id']] = $score;
        }
        return $ret;
    }
}
