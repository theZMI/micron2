<?php

// Платёж ищется по ID из уведомления банка (PaymentModel::findByTransferId) - нужен индекс. Новые таблицы создаются с ним сразу (PaymentModel::createTable)
return function ($db) {
    if (!Migrator::tableExists($db, 'payments')) {
        return;
    }
    $hasIndex = (bool)$db->selectCell(
        "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'payments' AND index_name = 'transfer_id'"
    );
    if (!$hasIndex) {
        $db->query("ALTER TABLE `payments` ADD KEY `transfer_id` (`transfer_id`)");
    }
};
