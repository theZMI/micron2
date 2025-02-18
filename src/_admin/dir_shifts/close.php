<?php

$model = new DirShiftsModel(+Get('id'));
$shifts = $model->shifts;
array_walk($shifts, function ($shift) {
    $shift->status = ShiftModel::STATUS_DONE;
});
UrlRedirect::go(SiteRoot('_admin/reports'));