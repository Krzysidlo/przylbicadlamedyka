<section class="container-fluid">
    <div class="row">
        <div class="col-12 order-2 col-md-5 order-md-1 pinky">
            <div class="col-12 col-xl-7 offset-xl-5 leftContainer">
                <div class="row">
                    <div class="col-12">
                        <h3 class="title">Odzyskiwanie hasła</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p>Po ustawieniu nowego hasła zostaniesz przekierowany do ekranu logowania.</p>
                    </div>
                </div>
                <form action="/ajax/register/resetPassword" class="row">
                    <input type="hidden" name="user_id" value="<?= $user->id; ?>">
                    <div class="form-group col-12">
                        <input type="password" name="password" pattern=".{8,}" required placeholder="Nowe hasło"
                               class="form-control validate<?= !empty($invalid['password']) && $invalid['password'] ? " invalid" : ""; ?>"
                               title="Hasło powinno mieć przynajmniej 8 znaków"
                               value="<?= !empty($this->get('password')) ? $this->get('password') : ""; ?>">
                    </div>
                    <div class="form-group col-12">
                        <input type="password" name="r-password" pattern=".{8,}" required placeholder="Powtórz hasło"
                               class="form-control validate<?= !empty($invalid['r-password']) && $invalid['r-password'] ? " invalid" : ""; ?>"
                               title="Hasło powinno mieć przynajmniej 8 znaków"
                               value="<?= !empty($this->get('r-password')) ? $this->get('r-password') : ""; ?>">
                    </div>
                    <div class="col-12 text-center">
                        <button class="btn btn-red mx-0" type="submit">Zapisz</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 order-1 col-md-7 order-md-2">
            <div class="col-12 col-xl-7 offset-xl-1 rightContainer">
                <h1>
                    <img class="img-logo" src="<?= IMG_URL; ?>/logo.png" alt="logo"> <?= PAGE_NAME; ?>
                </h1>
                <h4 class="mt-5 mb-3">O akcji!</h4>
                <p class="mb-4">Type someLorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur et sapien scelerisque, ullamcorper lacus quis, accumsan urna. Sed semper risus non massa mattis iaculis. Nulla ut dolor vitae purus mollis rhoncus eu non eros.</p>
                <h4>Wspólnie stworzyliśmy</h4>
                <p class="number"><span>3425</span> Przyłbic</p>
                <h4 class="mb-3">Jak możesz nam pomóc?</h4>
                <p>Type someLorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur et sapien scelerisque, ullamcorper lacus quis, accumsan urna. Sed semper risus non massa mattis iaculis. Nulla ut dolor vitae purus mollis rhoncus eu non eros. </p>
            </div>
        </div>
    </div>
</section>
