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
            <div class="col-12 col-xl-7 rightContainer">
                <h1 class="text-center">
                    <img class="img-logo" src="<?= IMG_URL; ?>/logo.png" alt="logo"> <?= PAGE_NAME; ?>
                </h1>
            </div>
        </div>
    </div>
</section>
