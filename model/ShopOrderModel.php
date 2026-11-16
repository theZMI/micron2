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

    public function scheme()
    {
        return [
            'id'               => 'int',
            'user_id'          => 'int',
            'access_key'       => 'string',
            'status'           => 'string',
            'total_amount'     => 'float',
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
                `total_amount` DECIMAL(12, 2) NOT NULL DEFAULT 0,
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
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Создаёт заказ из позиций корзины (ShopCart::getItems()): цены берутся из товаров, сумма считается здесь же.
     * $customer: name, email, phone, comment, user_id
     */
    public static function createFromCart(array $cartItems, array $customer): self
    {
        if (empty($cartItems)) {
            throw new InvalidArgumentException('Корзина пуста');
        }

        $db = GetDefaultDb();
        $db->transaction();
        try {
            $order                 = new self();
            $order->user_id        = (int)($customer['user_id'] ?? 0);
            $order->access_key     = bin2hex(random_bytes(16));
            $order->status         = self::STATUS_NEW;
            $order->currency       = Config('shop')['currency'];
            $order->customer_name  = (string)($customer['name'] ?? '');
            $order->customer_email = (string)($customer['email'] ?? '');
            $order->customer_phone = (string)($customer['phone'] ?? '');
            $order->comment        = (string)($customer['comment'] ?? '');
            $order->total_amount   = 0;
            $order_id              = (int)$order->flush();

            $totalCents = 0;
            foreach ($cartItems as $cartItem) {
                $product    = $cartItem['product'];
                $quantity   = (int)$cartItem['quantity'];
                $priceCents = self::toCents($product->price);

                $item                = new ShopOrderItemModel();
                $item->shop_order_id = $order_id;
                $item->product_id    = $product->id;
                $item->name          = $product->name;
                $item->vendor_code   = $product->vendor_code;
                $item->price         = $priceCents / 100;
                $item->quantity      = $quantity;
                $item->amount        = $priceCents * $quantity / 100;
                $item->flush();

                $totalCents += $priceCents * $quantity;
            }

            $order->total_amount = $totalCents / 100;
            $order->flush();
            $db->commit();
        } catch (Throwable $e) {
            $db->rollback();
            // Модели сохраняются сами в __destruct - сбрасываем несохранённое, чтобы оно не записалось после отката
            isset($item) && $item->resetChanges();
            $order->resetChanges();
            throw $e;
        }

        return $order;
    }

    // Деньги считаем в копейках, чтобы не копить ошибки float
    public static function toCents($amount): int
    {
        return (int)round((float)$amount * 100);
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

    public function canPay(): bool
    {
        return $this->isExists() && $this->status === self::STATUS_NEW && $this->total_amount > 0;
    }

    // ---- IShopOrderModel ----

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getTotalAmount(): float
    {
        return $this->total_amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getPayItems(): array
    {
        return array_map(fn(ShopOrderItemModel $item) => [
            'name'        => $item->name,
            'vendor_code' => $item->vendor_code,
            'price'       => $item->price,
            'quantity'    => $item->quantity,
            'amount'      => $item->amount,
        ], $this->getItems());
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
        if (!$order->isExists() || $order->status !== self::STATUS_NEW) {
            return;
        }
        $order->status    = self::STATUS_PAID;
        $order->paid_time = time();
        $order->flush();

        $order->notify(
            "Заказ №{$order->id} оплачен",
            "<p>Здравствуйте, {$order->customer_name}!</p><p>Мы получили оплату заказа №{$order->id} на сумму " . OutputFormats::amount($order->total_amount, 2, Config('shop')['currency_sign']) . ".</p>",
            "<p>Покупатель {$order->customer_name} ({$order->customer_email}, {$order->customer_phone}) оплатил заказ №{$order->id}.</p>"
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

    // Письма покупателю и менеджерам. Ошибка отправки не должна ломать обработку платежа
    private function notify(string $subject, string $customerHtml, string $managerHtml): void
    {
        $orderLink = $this->url();
        try {
            if (IsValidEmail($this->customer_email)) {
                SendMail($this->customer_email, $subject, $customerHtml . "<p>Заказ: <a href=\"{$orderLink}\">{$orderLink}</a></p>");
            }
            $managers = Config('shop')['manager_emails'];
            if ($managers) {
                $adminLink = SiteRoot("_admin/shop/order&id={$this->id}");
                SendMail($managers, $subject, $managerHtml . "<p>Заказ в админке: <a href=\"{$adminLink}\">{$adminLink}</a></p>");
            }
        } catch (Throwable $e) {
            ToLog("Shop order #{$this->id}: can't send email: " . $e->getMessage());
        }
    }
}
