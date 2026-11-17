<div class="user-settings">
    <h1 class="page-title">Настройки</h1>

    <section class="user-settings-card">
        <h2>Вход</h2>
        <p class="text-secondary mb-2">
            Логин: <strong class="text-body"><?= htmlspecialchars((string)$g_user->login) ?></strong><br>
            E-mail: <strong class="text-body"><?= htmlspecialchars((string)$g_user->email) ?></strong>
        </p>
        <p class="text-secondary mb-0">Паролей нет: и на сайте, и в приложении вы входите по логину, а мы отправляем на почту одноразовый код.</p>
    </section>
</div>
