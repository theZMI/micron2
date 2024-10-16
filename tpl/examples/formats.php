<h1 class="mt-4 mb-4">Форматы вывода данных</h1>

<div class="row mb-4">
    <div class="col-12">
        Вывод числа со склонением:
    </div>
    <div class="col">
        [code=Php]
        $count = 7;
        echo $count . ' ' . OutputFormats::byCount($count, 'день', 'дня', 'дней'); // 7 дней
        [/code]
    </div>
    <div class="col">
        <?php
        $count = 7;
        echo $count . ' ' . OutputFormats::byCount($count, 'день', 'дня', 'дней'); // 7 дней
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Даты в формате
        <nobr>ДД-ММ-ГГГГ</nobr>
        :
    </div>
    <div class="col">
        [code=Php]
        echo OutputFormats::date(time())
        [/code]
    </div>
    <div class="col">
        <?php
        echo OutputFormats::date(time());
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Даты в формате
        <nobr>ДД-ММ-ГГГГ ЧЧ:ММ[:СС]</nobr>
        :
    </div>
    <div class="col">
        [code=Php]
        $withSeconds = true;
        echo OutputFormats::dateTime(time(), $withSeconds);
        [/code]
    </div>
    <div class="col">
        <?php
        $withSeconds = true;
        echo OutputFormats::dateTime(time(), $withSeconds);
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Даты в формате
        <nobr>01 Января 2023 г. в 13:47[:00]</nobr>
        (год будет опущен если равен текущему, секунды опциональны)
    </div>
    <div class="col">
        [code=Php]
        echo FormatDate(time(), 'ru');
        [/code]
    </div>
    <div class="col">
        <?php
        echo FormatDate(time(), 'ru');
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Даты в формате
        <nobr>01 January 2023 y. at 13:47[:00]</nobr>
        (год будет опущен если равен текущему, секунды опциональны)
    </div>
    <div class="col">
        [code=Php]
        echo FormatDate(time(), 'en');
        [/code]
    </div>
    <div class="col">
        <?php
        echo FormatDate(time(), 'en');
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Показывать интервал времени ЧЧ:ММ[:СС]
    </div>
    <div class="col">
        [code=Php]
        $withSeconds = true;
        echo FormatTimeInterval(2*3600 + 600 + 59, $withSeconds); // 02:10:59
        [/code]
    </div>
    <div class="col">
        <?php
        $withSeconds = true;
        echo FormatTimeInterval(2 * 3600 + 600 + 59, $withSeconds); // 02:10:59
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Из даты в timestamp
    </div>
    <div class="col">
        [code=Php]
        echo $timestamp = OutputFormats::fromDate('23-01-2024');
        [/code]
    </div>
    <div class="col">
        <?php
        echo $timestamp = OutputFormats::fromDate('23-01-2024');
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Timestamp в формат понятный datepicker-у
    </div>
    <div class="col">
        [code=Php]
        echo $timeInStr = OutputFormats::dateForDatePicker(time());
        [/code]
    </div>
    <div class="col">
        <?php
        echo $timeInStr = OutputFormats::dateForDatePicker(time());
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Цена/сумма в красивом виде
    </div>
    <div class="col">
        [code=Php]
        echo OutputFormats::amount(10999.54) . '<br>';
        echo OutputFormats::amount(10999.54, 2, '$') . '<br>';
        echo OutputFormats::amount(10999.54, 0) . '<br>';
        echo OutputFormats::amount(10999.54, 0, '$') . '<br>';
        [/code]
    </div>
    <div class="col">
        <?php
        echo OutputFormats::amount(10999.54) . '<br>';
        echo OutputFormats::amount(10999.54, 2, '$') . '<br>';
        echo OutputFormats::amount(10999.54, 0) . '<br>';
        echo OutputFormats::amount(10999.54, 0, '$') . '<br>';
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Из суммы/цены в число
    </div>
    <div class="col">
        [code=Php]
        echo OutputFormats::fromAmount('10 999,54 €');
        [/code]
    </div>
    <div class="col">
        <?php
        echo OutputFormats::fromAmount('10 999,54 €');
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Число в красивом виде
    </div>
    <div class="col">
        [code=Php]
        echo OutputFormats::number(10999.54) . '<br>';
        echo OutputFormats::number(10999.54, 0) . '<br>';
        [/code]
    </div>
    <div class="col">
        <?php
        echo OutputFormats::number(10999.54) . '<br>';
        echo OutputFormats::number(10999.54, 0) . '<br>';
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Из суммы/цены в число
    </div>
    <div class="col">
        [code=Php]
        echo OutputFormats::fromNumber('10 999,54');
        [/code]
    </div>
    <div class="col">
        <?php
        echo OutputFormats::fromNumber('10 999,54');
        ?>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        Мобильный телефон
    </div>
    <div class="col">
        [code=Php]
        echo OutputFormats::mobilePhone('79992553777');
        [/code]
    </div>
    <div class="col">
        <?php
        echo OutputFormats::mobilePhone('79992553777');
        ?>
    </div>
</div>