<div class="container-fluid">
    <div class="row">
        <div class="col">

            <h1 class="my-4">Создать новую страницу</h1>
            <form action="<?= GetCurUrl() ?>" method="post" class="form-horizontal">
                <input type="hidden" name="is_create" value="1"/>
                <?= $msg ?>
                <div class="row g-3 align-items-center">
                    <div class="col-2 text-end">
                        <label for="i-name" class="col-form-label">URI будущей страницы</label>
                    </div>
                    <div class="col-10">
                        <input type="text" name="name" id="i-name" class="form-control" placeholder="about-us" value="<?= Post('name') ?>" autocomplete="off" autofocus required/>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-2"></div>
                    <div class="col-10">
                        <p class="text-muted">Пример названия: <strong>about-us</strong> или <strong>books/my-first-book</strong></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-2"></div>
                    <div class="col-10">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-plus-lg me-2"></i>Создать страницу</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>