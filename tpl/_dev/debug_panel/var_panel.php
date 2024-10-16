<a href="javascript:g_debug.toggle('<?= $link ?>')"><?= $head ?> <span>(<?= count($arr) ?>)</span></a>

<div id="<?= $link ?>" style="display: none;">
    <table>
        <tr>
            <th>#</th>
            <th>key</th>
            <th>value</th>
        </tr>
        <?php
        if (count($arr)):
            $i = 0;
            foreach ($arr as $k => $v): ?>
                <tr>
                    <td class="num"><?= ++$i ?></td>
                    <td><?= $k ?></td>
                    <td><?= VarDump($v) ?></td>
                </tr>
            <?php
            endforeach;
        else: ?>
            <tr>
                <td colspan="3" class="center">Empty</td>
            </tr>
        <?php endif ?>
    </table>
</div>
