<?php

/**
 * Заказ магазина
 *
 * Гость видит свой заказ по ссылке с секретным ключом (url()), авторизованный пользователь - свои заказы и без ключа.
 * Оплата идёт через PaySystem; когда платёж становится paid/failed, PaymentModel вызывает onPaymentPaid()/onPaymentFailed()
 * (подключено в core/init/pay_system.php)
 */
class ShopOrderModel extends SiteModel implements IShopOrderModel
{
    const STATUS_NEW      = 'new';      // Создан, ждёт оплаты
    const STATUS_PAID     = 'paid';     // Оплачен
    const STATUS_DONE     = 'done';     // Выполнен (выставляет менеджер)
    const STATUS_CANCELED = 'canceled'; // Отменён

    const PAYMENT_ONLINE      = 'online';      // Оплата сразу через платёжную систему
    const PAYMENT_ON_DELIVERY = 'on_delivery'; // Оплата при получении: заказ принимается без оплаты

    public function scheme()
    {
        return [
            'id'               => 'int',
            'user_id'          => 'int',
            'access_key'       => 'string',
            'status'           => 'string',
            'is_stock_returned' => 'bool', // Остатки по отменённому заказу уже возвращены на склад
            'is_promo_returned' => 'bool', // Использование промокода по отменённому заказу уже возвращено
            'total_amount'     => 'int', // К оплате, в копейках: товары - скидка + доставка
            'items_amount'     => 'int', // Товары без скидки, в копейках
            'discount_amount'  => 'int', // Скидка по промокоду, в копейках
            'promo_code'       => 'string',
            'delivery_method'  => 'string', // Ключ из Config('shop')['delivery']
            'delivery_name'    => 'string', // Название способа доставки на момент заказа
            'delivery_price'   => 'int', // В копейках
            'delivery_address' => 'string',
            'payment_method'   => 'string', // online | on_delivery
            'currency'         => 'string',
            'customer_name'    => 'string',
            'customer_email'   => 'string',
            'customer_phone'   => 'string',
            'comment'          => 'string',
            'paid_time'        => 'int',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `user_id` INT DEFAULT NULL,
                `access_key` VARCHAR(64) NOT NULL DEFAULT '',
                `status` VARCHAR(20) NOT NULL DEFAULT 'new',
                `is_stock_returned` TINYINT(1) NOT NULL DEFAULT 0,
                `is_promo_returned` TINYINT(1) NOT NULL DEFAULT 0,
                `total_amount` BIGINT NOT NULL DEFAULT 0,
                `items_amount` BIGINT NOT NULL DEFAULT 0,
                `discount_amount` BIGINT NOT NULL DEFAULT 0,
                `promo_code` VARCHAR(50) NOT NULL DEFAULT '',
                `delivery_method` VARCHAR(30) NOT NULL DEFAULT '',
                `delivery_name` VARCHAR(255) NOT NULL DEFAULT '',
                `delivery_price` BIGINT NOT NULL DEFAULT 0,
                `delivery_address` TEXT DEFAULT NULL,
                `payment_method` VARCHAR(20) NOT NULL DEFAULT 'online',
                `currency` VARCHAR(10) NOT NULL DEFAULT 'RUB',
                `customer_name` VARCHAR(255) NOT NULL DEFAULT '',
                `customer_email` VARCHAR(255) NOT NULL DEFAULT '',
                `customer_phone` VARCHAR(50) NOT NULL DEFAULT '',
                `comment` TEXT DEFAULT NULL,
                `paid_time` INT DEFAULT NULL,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                KEY `user_id` (`user_id`),
                KEY `status` (`status`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_orders', $id);
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW      => 'Ждёт оплаты',
            self::STATUS_PAID     => 'Оплачен',
            self::STATUS_DONE     => 'Выполнен',
            self::STATUS_CANCELED => 'Отменён',
        ];
    }

    public function statusName(): string
    {
        if ($this->status === self::STATUS_NEW && $this->isPayOnDelivery()) {
            return 'Принят, оплата при получении';
        }
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Создаёт заказ из позиций корзины (ShopCart::getItems()): цены берутся из товаров, суммы считаются здесь же.
     * $customer: name, email, phone, comment, user_id.
     * $checkout: delivery_method, delivery_address, payment_method, promo_code (необязательные).
     * Скидка по промокоду раскладывается по ценам позиций, доставка - отдельная сумма
     */
    public static function createFromCart(array $cartItems, array $customer, array $checkout = []): self
    {
        if (empty($cartItems)) {
            throw new InvalidArgumentException('Корзина пуста');
        }
        $cartItems = array_values($cartItems);

        // Конструкторы моделей выполняют CREATE TABLE IF NOT EXISTS, а DDL неявно завершает транзакцию - создаём их до неё
        new self();
        new ShopOrderItemModel();
        new ShopProductModel();
        new ShopPromoCodeModel();

        $deliveryMethod = (string)($checkout['delivery_method'] ?? '');
        $deliveryMethod = ShopDelivery::exists($deliveryMethod) ? $deliveryMethod : ShopDelivery::defaultMethod();
        $paymentMethod  = (string)($checkout['payment_method'] ?? '');
        $paymentMethod  = isset(ShopDelivery::paymentMethods()[$paymentMethod]) ? $paymentMethod : ShopDelivery::defaultPaymentMethod();

        // Позиции заказа: товары и отдельными позициями их доп. услуги (так они и в чеке)
        $lines = ShopCart::orderLines($cartItems);

        // Промокод: проверяем ещё раз (мог закончиться, пока покупатель оформлял)
        $itemsCents = array_sum(array_map(fn($l) => ShopCart::lineCents($l['price'], $l['quantity']), $lines));
        $promo      = null;
        $discounted = ['items' => [], 'discount' => 0];
        if (($code = (string)($checkout['promo_code'] ?? '')) !== '') {
            $promo = ShopPromoCodeModel::findByCode($code);
            $error = $promo->checkError($itemsCents);
            if ($error !== '') {
                throw new ShopCheckoutException("Промокод {$code}: " . mb_strtolower(mb_substr($error, 0, 1)) . mb_substr($error, 1));
            }
            $discounted = ShopPromoCodeModel::discountedItems($lines, $promo);
        }

        $order = null;
        $item  = null;
        try {
            DbTransaction(function () use ($lines, $customer, $checkout, $deliveryMethod, $paymentMethod, $promo, $discounted, &$order, &$item) {
                $order                   = new self();
                $order->user_id          = (int)($customer['user_id'] ?? 0);
                $order->access_key       = bin2hex(random_bytes(16));
                $order->status           = self::STATUS_NEW;
                $order->currency         = Config('shop')['currency'];
                $order->customer_name    = (string)($customer['name'] ?? '');
                $order->customer_email   = (string)($customer['email'] ?? '');
                $order->customer_phone   = (string)($customer['phone'] ?? '');
                $order->comment          = (string)($customer['comment'] ?? '');
                $order->delivery_method  = $deliveryMethod;
                $order->delivery_name    = ShopDelivery::name($deliveryMethod);
                $order->delivery_address = ShopDelivery::needAddress($deliveryMethod) ? (string)($checkout['delivery_address'] ?? '') : '';
                $order->payment_method   = $paymentMethod;
                $order->promo_code       = $promo ? $promo->code : '';
                $order->total_amount     = 0;
                $order_id                = (int)$order->flush();

                $fullCents = 0;
                $paidCents = 0;
                $parentIds = []; // Строка корзины => id позиции товара в заказе (к ней привязываются доп. услуги)
                foreach ($lines as $i => $line) {
                    $product   = $line['product'];
                    $addon     = $line['addon'];
                    $quantity  = (float)$line['quantity'];
                    $fullPrice = (int)$line['price'];
                    $price     = isset($discounted['items'][$i]) ? (int)$discounted['items'][$i]['price'] : $fullPrice;
                    $lineCents = ShopCart::lineCents($price, $quantity);
                    if (!$addon) {
                        self::takeFromStock((int)$product->id, $quantity, $product->name, $product->unitKey());
                    }

                    $item                 = new ShopOrderItemModel();
                    $item->shop_order_id  = $order_id;
                    $item->parent_item_id = $addon ? ($parentIds[$line['item']] ?? null) : null;
                    $item->product_id     = $product->id;
                    $item->addon_id       = $addon ? (int)$addon->id : null;
                    $item->name           = $addon ? $addon->name : $product->name;
                    $item->vendor_code    = $addon ? '' : $product->vendor_code;
                    $item->price          = $price;
                    $item->price_full     = $fullPrice;
                    $item->quantity       = $quantity;
                    $item->unit           = $addon ? 'шт' : $product->unitKey();
                    $item->is_service     = $addon ? (bool)$addon->is_service : false;
                    $item->amount         = $lineCents;
                    $itemId               = (int)$item->flush();
                    if (!$addon) {
                        $parentIds[$line['item']] = $itemId;
                    }

                    $fullCents += ShopCart::lineCents($fullPrice, $quantity);
                    $paidCents += $lineCents;
                }

                if ($promo) {
                    ShopPromoCodeModel::useCode((int)$promo->id);
                }
                $deliveryCents          = ShopDelivery::price($deliveryMethod, $paidCents);
                $order->items_amount    = $fullCents;
                $order->discount_amount = $fullCents - $paidCents;
                $order->delivery_price  = $deliveryCents;
                $order->total_amount    = $paidCents + $deliveryCents;
                $order->flush();
            });
        } catch (Throwable $e) {
            // Модели сохраняются сами в __destruct - сбрасываем несохранённое, чтобы оно не записалось после отката
            $item && $item->resetChanges();
            $order && $order->resetChanges();
            throw $e;
        }

        if ($order->total_amount <= 0) {
            // Платить нечего (промокод на всю сумму, доставка бесплатная): банк такой платёж не примет, а крон отменил бы
            // «неоплаченный» заказ - сразу отмечаем оплаченным
            $order->setStatus(self::STATUS_PAID);
            $order->notify(
                "Заказ №{$order->id} оформлен",
                "<p>Здравствуйте, {$order->customer_name}!</p><p>Мы приняли ваш заказ №{$order->id}. Оплачивать ничего не нужно - скоро свяжемся с вами.</p>",
                "<p>Новый заказ №{$order->id} на 0 ₽ (промокод {$order->promo_code}): {$order->customer_name} ({$order->customer_email}, {$order->customer_phone}).</p>"
            );
        } elseif ($order->isPayOnDelivery()) {
            $order->notify(
                "Заказ №{$order->id} принят",
                "<p>Здравствуйте, {$order->customer_name}!</p><p>Мы приняли ваш заказ №{$order->id} на сумму " . strip_tags(ShopPrice($order->total_amount)) . ". Оплата - при получении. Скоро свяжемся с вами.</p>",
                "<p>Новый заказ №{$order->id} с оплатой при получении: {$order->customer_name} ({$order->customer_email}, {$order->customer_phone}).</p>"
            );
        }
        return $order;
    }

    // Списывает остаток (если он учитывается). Строка блокируется до конца транзакции, чтобы два заказа не купили последнюю штуку
    private static function takeFromStock(int $product_id, float $quantity, string $name, string $unit): void
    {
        $db  = GetDefaultDb();
        $row = $db->selectRow("SELECT `track_stock`, `stock` FROM `shop_products` WHERE `id` = ?d FOR UPDATE", $product_id);
        if (!$row || !(int)$row['track_stock']) {
            return;
        }
        $stock = round((float)$row['stock'], 3);
        if ($stock + 0.0001 < $quantity) {
            throw new ShopOutOfStockException(
                $stock > 0 ? "«{$name}»: в наличии только " . ShopQuantity($stock, $unit) : "«{$name}» закончился"
            );
        }
        $db->query("UPDATE `shop_products` SET `stock` = `stock` - ? WHERE `id` = ?d", (string)$quantity, $product_id);
    }

    /**
     * Смена статуса с учётом склада: при отмене остатки возвращаются (один раз), при возврате из «Отменён» - снова списываются.
     * Всё в одной транзакции с блокировкой строки заказа, поэтому крон, банк и менеджер не вернут остаток дважды.
     * $expectStatus - какой статус должен быть сейчас: если его уже сменили, ничего не делаем (так крон не отменит оплаченный заказ).
     * Возвращает true, если статус действительно сменился
     */
    public function setStatus(string $status, ?string $expectStatus = null): bool
    {
        if (!array_key_exists($status, self::getStatuses()) || $status === $this->status) {
            return false;
        }
        $db       = GetDefaultDb();
        $returned = []; // Товары, которые вернулись на склад
        $changed  = false;

        DbTransaction(function () use ($db, $status, $expectStatus, &$returned, &$changed) {
            // Блокируем заказ до конца транзакции: иначе два процесса одновременно увидят is_stock_returned = 0 и вернут остаток дважды
            $row = $db->selectRow(
                "SELECT `status`, `is_stock_returned`, `is_promo_returned`, `promo_code` FROM `shop_orders` WHERE `id` = ?d FOR UPDATE",
                $this->id
            );
            if (!$row || ($expectStatus !== null && $row['status'] !== $expectStatus) || $row['status'] === $status) {
                return;
            }

            $stockReturned = (int)$row['is_stock_returned'];
            $promoReturned = (int)$row['is_promo_returned'];
            $isCancel      = $status === self::STATUS_CANCELED && !$stockReturned;
            $isRevive      = $row['status'] === self::STATUS_CANCELED && $stockReturned;

            if ($isCancel || $isRevive) {
                foreach ($this->getItems() as $item) {
                    if ($item->addon_id) {
                        continue; // Доп. услуга - не товар со склада
                    }
                    if ($isRevive) {
                        // Товар могли продать, пока заказ был отменён: не уводим остаток в минус
                        $stock = $db->selectRow(
                            "SELECT `track_stock`, `stock` FROM `shop_products` WHERE `id` = ?d FOR UPDATE",
                            $item->product_id
                        );
                        if ($stock && (int)$stock['track_stock'] && (float)$stock['stock'] + 0.0001 < (float)$item->quantity) {
                            ToLog("Shop order #{$this->id}: товар #{$item->product_id} не вернулся на склад (остаток " . $stock['stock'] . "), заказ оплачен без него");
                            continue;
                        }
                    }
                    $db->query(
                        "UPDATE `shop_products` SET `stock` = `stock` " . ($isCancel ? '+' : '-') . " ? WHERE `id` = ?d AND `track_stock` = 1",
                        (string)$item->quantity,
                        $item->product_id
                    );
                    $returned[] = (int)$item->product_id;
                }
                $stockReturned = $isCancel ? 1 : 0;
            }

            // Промокод: отменили заказ - возвращаем использованный раз, вернули заказ из отмены - снова расходуем
            if ($status === self::STATUS_CANCELED && !$promoReturned && (string)$row['promo_code'] !== '') {
                $db->query(
                    "UPDATE `shop_promo_codes` SET `used_count` = GREATEST(`used_count` - 1, 0) WHERE `code` = ?",
                    $row['promo_code']
                );
                $promoReturned = 1;
            } elseif ($isRevive && $promoReturned && (string)$row['promo_code'] !== '') {
                $db->query("UPDATE `shop_promo_codes` SET `used_count` = `used_count` + 1 WHERE `code` = ?", $row['promo_code']);
                $promoReturned = 0;
            }

            if ($status === self::STATUS_PAID && !$this->paid_time) {
                $this->paid_time = time();
            }
            $this->status            = $status;
            $this->is_stock_returned = (bool)$stockReturned;
            $this->is_promo_returned = (bool)$promoReturned;
            $this->flush(); // Строка заблокирована - обновляем её целиком
            $changed = true;
        });

        if ($changed && $status === self::STATUS_CANCELED && $returned) {
            ShopStockSubscriptionModel::notifyAvailable($returned); // Только товары этого заказа - кто-то мог их ждать
        }
        return $changed;
    }

    /**
     * Письмо покупателю о смене статуса менеджером (выполнен, отменён, оплачен вручную)
     */
    public function notifyCustomerAboutStatus(): void
    {
        $texts = [
            self::STATUS_PAID     => 'Заказ отмечен как оплаченный.',
            self::STATUS_DONE     => 'Заказ выполнен. Спасибо за покупку!',
            self::STATUS_CANCELED => 'Заказ отменён. Если это ошибка - ответьте на это письмо.',
        ];
        if (!isset($texts[$this->status]) || !IsValidEmail($this->customer_email)) {
            return;
        }
        $this->notify(
            "Заказ №{$this->id}: {$this->statusName()}",
            "<p>Здравствуйте, {$this->customer_name}!</p><p>{$texts[$this->status]}</p>",
            null
        );
    }

    /**
     * Отменяет неоплаченные заказы старше $hours часов (остатки возвращаются на склад). Заказы, по которым банк ещё ждёт оплату, не трогаем.
     * Возвращает номера отменённых заказов
     */
    public static function cancelUnpaid(int $hours): array
    {
        $ids = GetDefaultDb()->selectCol(
            "SELECT o.`id` FROM `shop_orders` o
              WHERE o.`status` = ? AND o.`payment_method` <> ? AND o.`create_time` < ?d
                AND NOT EXISTS (SELECT 1 FROM `payments` p WHERE p.`shop_order_id` = o.`id` AND p.`status` = ? AND p.`is_done` = 0)",
            self::STATUS_NEW,
            self::PAYMENT_ON_DELIVERY,
            time() - $hours * 3600,
            PaymentModel::STATUS_WAITING
        );
        $canceled = [];
        foreach ($ids as $id) {
            // Ожидаем именно «new»: пока крон шёл, заказ могли оплатить - такой не отменяем
            if ((new self($id))->setStatus(self::STATUS_CANCELED, self::STATUS_NEW)) {
                $canceled[] = (int)$id;
            }
        }
        return $canceled;
    }

    public function getItems(): array
    {
        return $this->isExists() ? (new ShopOrderItemModel())->getListByShopOrderId($this->id) : [];
    }

    public function getPayments(): array
    {
        return $this->isExists() ? (new PaymentModel())->getListByShopOrderId($this->id) : [];
    }

    public function getListByUserId(int $user_id): array
    {
        $ids = $this->db->selectCol("SELECT `id` FROM ?# WHERE `user_id` = ?d ORDER BY `id` DESC", $this->table, $user_id);
        return array_map(fn($id) => new static($id), $ids);
    }

    public function getAll(): array
    {
        $ret = [];
        foreach ($this->db->selectCol("SELECT `id` FROM ?# ORDER BY `id` DESC", $this->table) as $id) {
            $ret[(int)$id] = new static($id);
        }
        return $ret;
    }

    // Ссылка на страницу заказа (или pay_system/...), по которой его откроет и гость
    public function url(string $component = 'shop/order'): string
    {
        return SiteRoot("{$component}&id={$this->id}&key={$this->access_key}");
    }

    // Может ли текущий посетитель смотреть заказ: админ, владелец или тот, у кого есть ключ из ссылки
    public function canView(?string $key = null): bool
    {
        global $g_user;

        if (!$this->isExists()) {
            return false;
        }
        if (defined('IS_ADMIN_AUTH') && IS_ADMIN_AUTH) {
            return true;
        }
        if ($this->user_id && defined('IS_USER_AUTH') && IS_USER_AUTH && (int)$g_user->id === $this->user_id) {
            return true;
        }
        return $key !== null && $key !== '' && hash_equals($this->access_key, $key);
    }

    // Заказ из запроса (?id=..&key=..), если текущий посетитель может его смотреть
    public static function findForCurrentVisitor(): ?self
    {
        $order = new self(intval(Get('id')) ?: null);
        return $order->canView((string)Get('key', '')) ? $order : null;
    }

    public function isPayOnDelivery(): bool
    {
        return $this->payment_method === self::PAYMENT_ON_DELIVERY;
    }

    public function canPay(): bool
    {
        return $this->isExists() && $this->status === self::STATUS_NEW && !$this->isPayOnDelivery() && $this->total_amount > 0;
    }

    // ---- IShopOrderModel ----

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getTotalAmount(): int
    {
        return $this->total_amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Позиции для чека: товары и доп. услуги (цены уже со скидкой) и доставка отдельной позицией-услугой. Суммы - в копейках.
     * Банк проверяет, что цена × количество = сумма позиции. При дробном количестве (2,5 м по 3,33 ₽ = 8,325 ₽, а у нас 8,33)
     * так бывает не всегда - тогда позиция уходит как 1 × сумма, а количество пишется в названии: «Кабель (2,5 м)»
     */
    public function getPayItems(): array
    {
        $units = Config('shop')['units'];
        $ret   = array_map(function (ShopOrderItemModel $item) use ($units) {
            $unit    = $item->unit ?: 'шт';
            $isExact = abs($item->price * $item->quantity - $item->amount) < 0.001; // В копейках - целое число
            return [
                'name'        => $isExact ? $item->name : $item->name . ' (' . ShopQuantity($item->quantity, $unit) . ')',
                'vendor_code' => $item->vendor_code,
                'price'       => $isExact ? $item->price : $item->amount,
                'quantity'    => $isExact ? $item->quantity : 1,
                'amount'      => $item->amount,
                'unit'        => $isExact ? ShopUnitName($unit) : 'шт.',
                'measure'     => $isExact ? ($units[$unit]['measure'] ?? 'piece') : 'piece',
                'is_service'  => (bool)$item->is_service, // Доп. услуга (упаковка, гарантия) - услуга в чеке
            ];
        }, $this->getItems());
        // Позиции на 0 ₽ (всё съел промокод) банк не примет, а на сумму они не влияют
        $ret = array_values(array_filter($ret, fn($i) => $i['amount'] > 0));
        if ($this->delivery_price > 0) {
            $ret[] = [
                'name'        => 'Доставка: ' . $this->delivery_name,
                'vendor_code' => 'delivery',
                'price'       => $this->delivery_price,
                'quantity'    => 1,
                'amount'      => $this->delivery_price,
                'unit'        => 'шт.',
                'measure'     => 'piece',
                'is_service'  => true,
            ];
        }
        return $ret;
    }

    public function getCustomer(): array
    {
        return [
            'user_id' => $this->user_id ?: null,
            'name'    => $this->customer_name,
            'email'   => $this->customer_email,
            'phone'   => $this->customer_phone,
        ];
    }

    public function getPayBackUrl(): string
    {
        return $this->url('pay_system/waiting');
    }

    // ---- Обработчики оплаты (см. core/init/pay_system.php) ----

    public static function onPaymentPaid(PaymentModel $payment): void
    {
        $order = new self($payment->shop_order_id ?: null);
        if (!$order->isExists()) {
            return;
        }

        $paidCents  = (int)$payment->amount;
        $totalCents = (int)$order->total_amount;

        if (!in_array($order->status, [self::STATUS_NEW, self::STATUS_CANCELED], true)) {
            // Заказ уже оплачен или выполнен: пришла вторая оплата - деньги нужно вернуть, но узнать об этом должен менеджер
            ToLog("Shop order #{$order->id}: повторная оплата платежом #{$payment->id} на " . ShopPrice($paidCents) . ", статус {$order->status}");
            $order->notifyManagers(
                "Заказ №{$order->id}: повторная оплата",
                "<p>По заказу №{$order->id} пришла ещё одна оплата: " . strip_tags(ShopPrice($paidCents)) . " (платёж #{$payment->id}). "
                . "Заказ в статусе «{$order->statusName()}» - верните деньги покупателю.</p>"
            );
            return;
        }

        if ($paidCents < $totalCents) {
            // Оплата меньше суммы: заказ оплаченным не отмечаем, но и молчать нельзя
            ToLog("Shop order #{$order->id}: платёж #{$payment->id} на " . ShopPrice($paidCents) . " меньше суммы заказа " . ShopPrice($totalCents));
            $order->notifyManagers(
                "Заказ №{$order->id}: оплата меньше суммы",
                "<p>Платёж #{$payment->id} на " . strip_tags(ShopPrice($paidCents)) . " меньше суммы заказа "
                . strip_tags(ShopPrice($totalCents)) . ". Заказ оставлен неоплаченным - проверьте платёж.</p>"
            );
            return;
        }

        $wasCanceled = $order->status === self::STATUS_CANCELED; // Оплата пришла после отмены - заказ оживает, остаток списывается снова
        $isOverpaid  = $paidCents > $totalCents;
        $order->setStatus(self::STATUS_PAID);

        $order->notify(
            "Заказ №{$order->id} оплачен",
            "<p>Здравствуйте, {$order->customer_name}!</p><p>Мы получили оплату заказа №{$order->id} на сумму " . strip_tags(ShopPrice($order->total_amount)) . ".</p>",
            "<p>Покупатель {$order->customer_name} ({$order->customer_email}, {$order->customer_phone}) оплатил заказ №{$order->id}.</p>"
            . ($wasCanceled ? '<p><b>Внимание: заказ был отменён до оплаты</b> - проверьте наличие товара.</p>' : '')
            . ($isOverpaid ? '<p><b>Оплата больше суммы заказа:</b> ' . strip_tags(ShopPrice($paidCents)) . ' - верните разницу.</p>' : '')
        );
    }

    // Неудачный платёж заказ не отменяет: покупатель может попробовать оплатить ещё раз
    public static function onPaymentFailed(PaymentModel $payment): void
    {
        $order = new self($payment->shop_order_id ?: null);
        if (!$order->isExists()) {
            return;
        }
        $order->notify(
            "Заказ №{$order->id}: оплата не прошла",
            "<p>Здравствуйте, {$order->customer_name}!</p><p>Банк не провёл оплату заказа №{$order->id}. Вы можете попробовать оплатить ещё раз.</p>",
            "<p>У покупателя {$order->customer_name} ({$order->customer_email}, {$order->customer_phone}) не прошла оплата заказа №{$order->id}.</p>"
        );
    }

    // Письмо только менеджерам (покупателю про такие случаи писать рано)
    private function notifyManagers(string $subject, string $html): void
    {
        $managers = Config('shop')['manager_emails'];
        if (!$managers) {
            return;
        }
        try {
            $adminLink = SiteRoot("_admin/shop/order&id={$this->id}");
            SendMail($managers, $subject, $html . "<p>Заказ в админке: <a href=\"{$adminLink}\">{$adminLink}</a></p>");
        } catch (Throwable $e) {
            ToLog("Shop order #{$this->id}: can't send email: " . $e->getMessage());
        }
    }

    // Письма покупателю и менеджерам. Ошибка отправки не должна ломать обработку платежа
    private function notify(string $subject, string $customerHtml, ?string $managerHtml): void
    {
        $orderLink = $this->url();
        try {
            if (IsValidEmail($this->customer_email)) {
                SendMail($this->customer_email, $subject, $customerHtml . "<p>Заказ: <a href=\"{$orderLink}\">{$orderLink}</a></p>");
            }
            $managers = Config('shop')['manager_emails'];
            if ($managers && $managerHtml !== null) {
                $adminLink = SiteRoot("_admin/shop/order&id={$this->id}");
                SendMail($managers, $subject, $managerHtml . "<p>Заказ в админке: <a href=\"{$adminLink}\">{$adminLink}</a></p>");
            }
        } catch (Throwable $e) {
            ToLog("Shop order #{$this->id}: can't send email: " . $e->getMessage());
        }
    }
}
