<h1 class="my-4">Примеры для number_format.js</h1>

<script type="text/javascript" src="<?= Root('i/js/_dev/number_format.js') ?>"></script>
<script>
    document.write(
        "number_format(1234.56, 2, ',', ' ') = " + number_format(1234.56, 2, ',', ' ')
    );
    document.write('<br>---<br>');
    document.write(
        "number_format(1, 0, '.', '0') = " + number_format(1, 2, '.', '0')
    );
</script>
<br>
[code=JavaScript]
   example 1: number_format(1234.56);
   returns 1: '1,235'
   example 2: number_format(1234.56, 2, ',', ' ');
   returns 2: '1 234,56'
   example 3: number_format(1234.5678, 2, '.', '');
   returns 3: '1234.57'
   example 4: number_format(67, 2, ',', '.');
   returns 4: '67,00'
   example 5: number_format(1000);
   returns 5: '1,000'
   example 6: number_format(67.311, 2);
   returns 6: '67.31'
   example 7: number_format(1000.55, 1);
   returns 7: '1,000.6'
   example 8: number_format(67000, 5, ',', '.');
   returns 8: '67.000,00000'
   example 9: number_format(0.9, 0);
   returns 9: '1'
   example 10: number_format('1.20', 2);
   returns 10: '1.20'
   example 11: number_format('1.20', 4);
   returns 11: '1.2000'
   example 12: number_format('1.2000', 3);
   returns 12: '1.200'
   example 13: number_format('1 000,50', 2, '.', ' ');
   returns 13: '100 050.00'
   example 14: number_format(1e-8, 8, '.', '');
   returns 14: '0.00000001'
[/code]