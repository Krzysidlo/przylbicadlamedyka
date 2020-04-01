<section class="container-fluid">
    <div class="row">
        <div class="col-5 pinky">
            <div class="col-7 offset-5 leftContainer register">
                <div class="row">
                    <div class="col-12">
                        <h3 class="title">Rejestracja</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p>Cześć! Dziękujemy za dołączenie do akcji, na początek ustalmy jak będziesz nam pomagał.</p>
                    </div>
                </div>
                <form action="/ajax/register/register" class="row">
                    <div class="form-group col-12 col-md-6">
                        <input type="text" name="firstname" pattern=".{3,}" required placeholder="Imię"
                               class="form-control validate<?= !empty($invalid['firstname']) && $invalid['firstname'] ? " invalid" : ""; ?>"
                               title="Imię musi się składać z minimum 3 znaków"
                               value="<?= !empty($this->get('firstname')) ? $this->get('firstname') : ""; ?>">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <input type="text" name="lastname" pattern=".{3,}" required placeholder="Nazwisko"
                               class="form-control validate<?= !empty($invalid['lastname']) && $invalid['lastname'] ? " invalid" : ""; ?>"
                               title="Nazwisko musi się składać z minimum 3 znaków"
                               value="<?= !empty($this->get('lastname')) ? $this->get('lastname') : ""; ?>">
                    </div>
                    <div class="form-group col-12">
                        <input type="email" name="email" required placeholder="E-mail"
                               class="form-control validate<?= !empty($invalid['email']) && $invalid['email'] ? " invalid" : ""; ?>"
                               title="Wygląda na to, że adres e-mail jest niepoprawny"
                               value="<?= !empty($this->get('email')) ? $this->get('email') : ""; ?>">
                    </div>
                    <div class="form-group col-12">
                        <input type="tel" name="tel" pattern=".{9,}" required placeholder="Numer telefonu"
                            <?php //data-inputmask="'mask': '+99 999 999 999'" ?>
                               class="form-control validate<?= !empty($invalid['tel']) && $invalid['tel'] ? " invalid" : ""; ?>"
                               title="Długośc numeru powinna wynosić minimu 9 znaków"
                               value="<?= !empty($this->get('tel')) ? $this->get('tel') : ""; ?>">
                    </div>
                    <div class="form-group col-12">
                        <input type="password" name="password" pattern=".{8,}" required placeholder="Hasło"
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
                    <div class="form-check mt-2 mb-3 col-12">
                        <input type="checkbox" class="form-check-input" name="no-quarantine" required
                               id="no-quarantine" <?= !empty($this->get('no-quarantine')) ? "checked" : ""; ?>>
                        <label for="no-quarantine" class="form-check-label">
                            Potwierdzam, że nie odbywam kwarantanny
                        </label>
                    </div>
                    <div class="form-check mb-3 col-12">
                        <input type="checkbox" class="form-check-input" name="regulations" required
                               id="regulations" <?= !empty($this->get('regulations')) ? "checked" : ""; ?>>
                        <label for="regulations" class="form-check-label">
                            Zapoznałem się z <a href="/regulations" target="_blank">regulaminem</a>
                        </label>
                    </div>
                    <div class="col-12 text-center">
                        <button class="btn btn-red" type="submit">Zarejestruj się</button>
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-12">
                        <p>Masz już konto? <a href="#" class="chngView" data-view="login">Przejdź do logowania</a></p>
                    </div>
                </form>
            </div>
            <div class="col-7 offset-5 leftContainer login">
                <div class="row">
                    <div class="col-12">
                        <h3 class="title">Logowanie</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p>Cześć! Dziękujemy za dołączenie do akcji, na początek ustalmy jak będziesz nam pomagał.</p>
                    </div>
                </div>
                <form action="/ajax/register/login" class="row">
                    <div class="form-group col-12">
                        <input type="email" name="lemail" required placeholder="E-mail"
                               class="form-control validate<?= !empty($invalid['lemail']) && $invalid['lemail'] ? " invalid" : ""; ?>"
                               title="Wygląda na to, że adres e-mail jest niepoprawny"
                               value="<?= !empty($this->get('lemail')) ? $this->get('lemail') : ""; ?>">
                    </div>
                    <div class="form-group col-12">
                        <input type="password" name="lpassword" pattern=".{8,}" required placeholder="Hasło"
                               class="form-control validate<?= !empty($invalid['lpassword']) && $invalid['lpassword'] ? " invalid" : ""; ?>"
                               title="Hasło powinno mieć przynajmniej 8 znaków"
                               value="<?= !empty($this->get('lpassword')) ? $this->get('lpassword') : ""; ?>">
                        <a href="#" class="chngView right mt-2" data-view="forgot">Zapomniałem hasła</a>
                    </div>
                    <div class="form-check mt-2 mb-3 col-12">
                        <input type="checkbox" class="form-check-input" name="lremember"
                               id="lremember" <?= !empty($this->get('lremember')) ? "checked" : ""; ?>>
                        <label for="lremember" class="form-check-label">
                            Pozostań zalogowany
                        </label>
                    </div>
                    <div class="col-12 text-center">
                        <button class="btn btn-red" type="submit">Zaloguj się</button>
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-12">
                        <p>Nie masz jeszcze konta? <a href="#" class="chngView" data-view="register">Przejdź do rejestracji</a></p>
                    </div>
                </form>
            </div>
            <div class="col-7 offset-5 leftContainer forgot">
                <div class="row">
                    <div class="col-12">
                        <h3 class="title">Odzyskiwanie hasła</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p>Cześć! Dziękujemy za dołączenie do akcji, na początek ustalmy jak będziesz nam pomagał.</p>
                    </div>
                </div>
                <form action="/ajax/register/forgot" class="row">
                    <div class="form-group col-12">
                        <input type="email" name="femail" required placeholder="E-mail"
                               class="form-control validate<?= !empty($invalid['femail']) && $invalid['femail'] ? " invalid" : ""; ?>"
                               title="Wygląda na to, że adres e-mail jest niepoprawny"
                               value="<?= !empty($this->get('femail')) ? $this->get('femail') : ""; ?>">
                    </div>
                    <div class="col-12 text-center">
                        <button class="btn btn-red" type="submit">Restartuj</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-7">
            <div class="col-7 rightContainer">
                <h1 class="text-center">
                    <img class="img-logo" src="<?= IMG_URL; ?>/logo.png" alt="logo"> <?= PAGE_NAME; ?>
                </h1>
            </div>
        </div>
    </div>
</section>
