<div class="container">
    <div class="row">
        <div class="col">

            <div class="registration-form">
                <h1 class="my-4">Регистрация</h1>
                <form action="<?= GetCurUrl() ?>" method="post">
                    <input type="hidden" name="is_register" value="1">
                    <?= $msg ?>
                    <div class="form-floating input-with-bottom-line-only mb-3">
                        <input type="text" class="form-control" name="first_name" value="<?= Post('first_name') ?>" id="floatingName" placeholder="Имя" required="required">
                        <label for="floatingName">Имя <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-floating input-with-bottom-line-only mb-3">
                        <input type="text" class="form-control" name="surname" value="<?= Post('surname') ?>" id="floatingLastName" placeholder="Фамилия" required="required">
                        <label for="floatingLastName">Фамилия <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-floating input-with-bottom-line-only mb-3">
                        <input type="email" class="form-control" name="email" value="<?= Post('email') ?>" id="floatingLogin" placeholder="E-mail" required="required">
                        <label for="floatingLogin">E-mail <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-floating input-with-bottom-line-only mb-3">
                        <input type="text" class="form-control phone-mask" name="phone" value="<?= Post('phone') ?>" id="floatingPhone" placeholder="Телефон">
                        <label for="floatingPhone">Телефон</label>
                    </div>
                    <div class="form-check form-switch form-switch-lg mb-5">
                        <input class="form-check-input" type="checkbox" name="im_agree_with_offers" id="flexCheckAgreeWithOffer" role="switch">
                        <label class="form-check-label ms-3 mt-2" for="flexCheckAgreeWithOffer">
                            Я согласен с <a href="<?= SiteRoot('user_agreements') ?>">пользовательским соглашением</a>
                        </label>
                    </div>
                    <div class="text-end mb-4">
                        <button type="submit" class="btn btn-primary btn-lg btn-min-width ps-4 pe-4 rounded-pill">
                            Зарегистрироваться<i class="ms-2 bi bi-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>