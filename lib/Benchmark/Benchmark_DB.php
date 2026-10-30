<?php

class Benchmarks_DB implements IBenchmark
{
    public array $marks = [];

    public function add($mark, $comment = '')
    {
        if (isset($this->marks[$mark])) {
            $this->marks[$mark]['end']        = microtime(true);
            $this->marks[$mark]['comment']    .= $comment;
            $this->marks[$mark]['usedMemory'] = intval(memory_get_usage() - $this->marks[$mark]['usedMemory']);
            return;
        }

        $this->marks[$mark] = [
            'start'      => microtime(true),
            'end'        => 0,
            'comment'    => $comment,
            'usedMemory' => memory_get_usage()
        ];
    }

    public function stat()
    {
        $benchmarkGroups = [
            'PHP'       => ['execTime' => 0, 'count' => 1, 'usedMemory' => 0],
            'Services'  => ['execTime' => 0, 'count' => 0, 'usedMemory' => 0],
            'Databases' => ['execTime' => 0, 'count' => 0, 'usedMemory' => 0],
        ];
        $stat            = [];

        foreach ($this->marks as $k => $v) {
            $start    = $v['start'];
            $end      = $v['end'] ?: microtime(true);
            $stat[$k] = [
                'execTime'   => $end - $start,
                'usedMemory' => $v['end'] ? $v['usedMemory'] : intval(memory_get_usage() - $v['usedMemory'])
            ];
        }

        foreach ($stat as $mark => $v) {
            $groupMark = explode(" / ", $mark)[0] ?? $mark;
            if (stripos($mark, 'Databases') === 0) {
                $groupMark = 'Databases';
            }
            if (isset($benchmarkGroups[$groupMark])) {
                $benchmarkGroups[$groupMark]['execTime']   += $v['execTime'];
                $benchmarkGroups[$groupMark]['usedMemory'] += $v['usedMemory'];
                $benchmarkGroups[$groupMark]['count']++;
            }
        }

        foreach ($stat as $mark => $v) {
            $groupMark = explode(" / ", $mark)[0] ?? $mark;
            if (stripos($mark, 'Services') === 0) {
                $groupMark = 'Services';
            }
            if (isset($benchmarkGroups[$groupMark])) {
                $benchmarkGroups[$groupMark]['execTime']   += $v['execTime'];
                $benchmarkGroups[$groupMark]['usedMemory'] += $v['usedMemory'];
                $benchmarkGroups[$groupMark]['count']++;
            }
        }

        $benchmarkGroups['PHP'] = [
            'execTime'   => 0
                + $stat['Request']['execTime']
                - $benchmarkGroups['Databases']['execTime'] // Вычитаем запросы в БД
                - $benchmarkGroups['Services']['execTime'] // Вычитаем запросы в сервисы
                - ($stat['Request / Prepare content / Output->addDebug']['execTime'] ?? 0), // Удаляем время работы ф-ии по добавлению debug-панели
            'usedMemory' => 0
                + $stat['Request']['usedMemory']
                - $benchmarkGroups['Databases']['usedMemory']
                - $benchmarkGroups['Services']['usedMemory']
                - ($stat['Request / Prepare content / Output->addDebug']['usedMemory'] ?? 0),
            'count'      => 1
        ];

        $benchmarkGroups['full_stat'] = $stat;

        return $benchmarkGroups;
    }

    public function __destruct()
    {
    }
}
