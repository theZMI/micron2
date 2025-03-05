<h1 class="my-4">Session manager</h1>
<p>При использовании SessionManager не будет происходить блокировки скрипта целиком, а только на время работы с сессией.</p>
<div class="row">
    <div class="col-6">
        [code=Php]
        $paramName = 'session_param_1';
        if (SessionManager::has($paramName)) {
            echo "SESSION PARAM: " . SessionManager::get($paramName);
        } else {
            SessionManager::set($paramName, uniqid());
        }
        [/code]
    </div>
    <div class="col-6">
        <?php
        $paramName = 'session_param_1';
        if (SessionManager::has($paramName)) {
            echo "Session data: " . SessionManager::get($paramName);
        } else {
            SessionManager::set($paramName, uniqid());
            echo "Data saved into session. Update page.";
        }
        ?>
    </div>
</div>