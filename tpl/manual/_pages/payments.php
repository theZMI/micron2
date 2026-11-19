<h1>Приём оплаты</h1>
<p class="lead">Встроены две платёжные системы - ЮKassa и Сбер. Платёж создаётся через единый класс <code>PaySystem</code>, поэтому сменить банк можно одной строкой.</p>

<h2>Настройка</h2>
<ol>
    <li>Задайте ключи в <code>.env</code>: <code>PAY_SYSTEM_YOOKASSA_SHOP_ID</code> и <code>PAY_SYSTEM_YOOKASSA_PASSWORD</code> или <code>PAY_SYSTEM_SBER_USERNAME</code> и <code>PAY_SYSTEM_SBER_PASSWORD</code>.</li>
    <li>Выберите систему в <code>core/init/pay_system.php</code>:
        <?= DocCode(<<<'CODE'
            Config('pay_system', Config('pay_system_yookassa')); // или Config('pay_system_sber')
            CODE) ?>
    </li>
    <li>Параметры чека (НДС, система налогообложения, признак предмета расчёта, мера количества по ФФД 1.2 - <code>send_measure</code> у ЮKassa) - в <code>core/config/pay_systems/*.php</code>. Количество бывает дробным (2.5 м), мера берётся из единицы товара, доставка идёт отдельной позицией-услугой.</li>
    <li>Укажите адрес уведомлений в личном кабинете банка: <code>https://site.ru/pay_system/notify</code>. В ЮKassa - «HTTP-уведомления», события <code>payment.succeeded</code> и <code>payment.canceled</code>; в Сбере - callback-уведомления.</li>
    <li>Добавьте в расписание сервера проверку зависших платежей: <code>/_cron/pay_system_check?key=CRON_SECRET</code>.</li>
</ol>
<div class="manual-note is-warning">
    <p>Если в <code>.env</code> локальной копии стоят боевые ключи, любая попытка оплаты создаёт настоящий платёж в банке. Для разработки используйте тестовый магазин ЮKassa или тестовые ключи Сбера.</p>
</div>

<h2>Путь покупателя</h2>
<div class="manual-flow">
    <span>pay_system/pay_form</span><i class="bi bi-arrow-right"></i>
    <span>страница банка</span><i class="bi bi-arrow-right"></i>
    <span>pay_system/waiting</span><i class="bi bi-arrow-right"></i>
    <span>succeeded / failed</span>
</div>
<p>Ссылку на оплату даёт сам заказ: <code>$order-&gt;url('pay_system/pay_form')</code>. Если прошлый платёж по заказу ещё ждёт оплаты, покупатель отправляется на него, а не создаётся новый.</p>

<h2>Как узнаём, что оплачено</h2>
<p>Статус платежа приходит тремя путями:</p>
<ol>
    <li>покупатель вернулся с сайта банка (<code>pay_system/waiting</code>);</li>
    <li>банк прислал уведомление на <code>/pay_system/notify</code>;</li>
    <li>крон <code>/_cron/pay_system_check</code> проверил зависшие платежи (есть и кнопка в админке).</li>
</ol>
<p>Уведомлению не верим на слово: статус всегда перепроверяется запросом к банку. Меняется он только через <code>PaymentModel::applyStatus()</code> атомарным запросом, поэтому обработчики оплаты выполняются ровно один раз, даже если все три пути сработали одновременно.</p>

<h2>Что происходит после оплаты</h2>
<p>Вызываются обработчики из настроек (в них приходит <code>PaymentModel</code>):</p>
<?= DocCode(<<<'CODE'
    Config(['pay_system', 'on_paid'],   [ShopOrderModel::class, 'onPaymentPaid']);   // Заказ оплачен, письма покупателю и менеджерам
    Config(['pay_system', 'on_failed'], [ShopOrderModel::class, 'onPaymentFailed']);
    CODE, 'php', 'core/init/pay_system.php') ?>
<p>Если оплата пришла по уже отменённому заказу (например, покупатель оплатил после автоотмены), заказ восстанавливается, а товар снова списывается со склада.</p>

<h2>Свой тип заказа</h2>
<p>Оплачивать можно не только заказы магазина, но и, например, подписки или билеты. Реализуйте интерфейс <code>IShopOrderModel</code>:</p>
<?= DocCode(<<<'CODE'
    interface IShopOrderModel
    {
        public function getId(): int;
        public function getTotalAmount(): int;   // В копейках
        public function getCurrency(): string;  // RUB, USD...
        public function getPayItems(): array;   // Позиции для чека (price и amount - в копейках)
        public function getCustomer(): array;   // Почта/телефон покупателя для чека
        public function getPayBackUrl(): string; // Куда вернуть покупателя после оплаты
    }
    CODE) ?>
<p>Создайте платёж через <code>PaySystem::getInstance()-&gt;createPayment($order, new PaymentModel())</code> (метод вернёт адрес страницы банка) и поменяйте обработчики <code>on_paid</code> / <code>on_failed</code> на свои.</p>
