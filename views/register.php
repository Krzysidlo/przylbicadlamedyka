<?php

$this->title = "Zarejestruj się";
?>

<section class="container">
    <div class="row">
        <div class="col-12 col-md-6 offset-md-3">
            <h1 class="text-center">
                <img class="img-circle img-medium" src="<?= IMG_URL; ?>/logo.png" alt="logo"> <?= PAGE_NAME; ?>
            </h1>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12 col-md-6 offset-md-3 logRegForm">
            <ul class="nav nav-tabs" id="logRegTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#register" role="tab"
                       aria-controls="register"
                       aria-selected="true">Zarejestruj się</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#login" role="tab"
                       aria-controls="login"
                       aria-selected="false">Zaloguj się</a>
                </li>
            </ul>
            <div class="tab-content row" id="tabs">
                <div class="tab-pane fade show active col-12" id="register" role="tabpanel"
                     aria-labelledby="register-tab">
                    <form action="/ajax/register/register" method="post" class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="firstName" data-error="Imię musi się składać z minimum 3 znaków">
                                Imię
                            </label>
                            <input type="text" name="firstname" id="firstName" pattern=".{3,}" required
                                   class="form-control validate<?= !empty($invalid['firstname']) && $invalid['firstname'] ? " invalid" : ""; ?>"
                                   title="Imię musi się składać z minimum 3 znaków"
                                   value="<?= !empty($this->get('firstname')) ? $this->get('firstname') : ""; ?>">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="lastName" data-error="Nazwisko musi się składać z minimum 3 znaków">
                                Nazwisko
                            </label>
                            <input type="text" name="lastname" id="lastName" pattern=".{3,}" required
                                   class="form-control validate<?= !empty($invalid['lastname']) && $invalid['lastname'] ? " invalid" : ""; ?>"
                                   title="Nazwisko musi się składać z minimum 3 znaków"
                                   value="<?= !empty($this->get('lastname')) ? $this->get('lastname') : ""; ?>">
                        </div>
                        <div class="form-group col-12">
                            <label for="email" data-error="Wygląda na to, że adres e-mail jest niepoprawny">
                                E-mail
                            </label>
                            <input type="email" name="email" id="email" required
                                   class="form-control validate<?= !empty($invalid['email']) && $invalid['email'] ? " invalid" : ""; ?>"
                                   title="Wygląda na to, że adres e-mail jest niepoprawny"
                                   value="<?= !empty($this->get('email')) ? $this->get('email') : ""; ?>">
                        </div>
                        <div class="form-group col-12">
                            <label for="email" data-error="Długośc numeru powinna wynosić minimu 9 znaków">
                                Numer telefonu
                            </label>
                            <input type="tel" name="tel" id="tel" pattern=".{9,}" required
                                   <?php //data-inputmask="'mask': '+99 999 999 999'" ?>
                                   class="form-control validate<?= !empty($invalid['tel']) && $invalid['tel'] ? " invalid" : ""; ?>"
                                   title="Długośc numeru powinna wynosić minimu 9 znaków"
                                   value="<?= !empty($this->get('tel')) ? $this->get('tel') : ""; ?>">
                        </div>
                        <div class="form-group col-12">
                            <label for="addressFinder" data-error="Wygląda na to, że adres e-mail jest niepoprawny">
                                Adres
                            </label>
                            <input type="text" id="addressFinder" class="form-control">
                            <input type="hidden" name="address" id="address" required
                                   value="<?= !empty($this->get('address')) ? $this->get('address') : ""; ?>">
                            <div id="addressMap" class="mt-4"></div>
                        </div>
                        <div class="form-group col-12">
                            <label for="password" data-error="Hasło powinno mieć przynajmniej 8 znaków">
                                Hasło
                            </label>
                            <input type="password" name="password" id="password" pattern=".{8,}" required
                                   class="form-control validate<?= !empty($invalid['password']) && $invalid['password'] ? " invalid" : ""; ?>"
                                   title="Hasło powinno mieć przynajmniej 8 znaków"
                                   value="<?= !empty($this->get('password')) ? $this->get('password') : ""; ?>">
                        </div>
                        <div class="form-group col-12">
                            <label for="r-password" data-error="Hasło powinno mieć przynajmniej 8 znaków">
                                Powtórz hasło
                            </label>
                            <input type="password" name="r-password" id="r-password" pattern=".{8,}" required
                                   class="form-control validate<?= !empty($invalid['r-password']) && $invalid['r-password'] ? " invalid" : ""; ?>"
                                   title="Hasło powinno mieć przynajmniej 8 znaków"
                                   value="<?= !empty($this->get('r-password')) ? $this->get('r-password') : ""; ?>">
                        </div>
                        <div class="mt-2 mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="no-quarantine" required
                                       id="no-quarantine" <?= !empty($this->get('no-quarantine')) ? "checked" : ""; ?>>
                                <label for="no-quarantine" class="form-check-label">
                                    Potwierdzam, że nie odbywam kwarantanny
                                </label>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button class="btn btn-primary" type="submit">Zarejestruj się</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade col-12" id="login" role="tabpanel" aria-labelledby="login-tab">
                    <form action="/ajax/register/login" method="post">
                        <div class="form-group">
                            <label for="lemail"
                                   data-error="Wygląda na to, że adres e-mail jest niepoprawny">
                                E-mail
                            </label>
                            <input type="email" name="lemail" id="lemail" required
                                   class="form-control validate<?= !empty($invalid['email']) && $invalid['email'] ? " invalid" : ""; ?>"
                                   title="Wygląda na to, że adres e-mail jest niepoprawny"
                                   value="<?= !empty($this->get('email')) ? $this->get('email') : ""; ?>">
                        </div>
                        <div class="form-group">
                            <label for="lpassword" data-error="Hasło powinno mieć przynajmniej 8 znaków">
                                Hasło
                            </label>
                            <input type="password" name="lpassword" id="lpassword" pattern=".{8,}" required
                                   class="form-control validate<?= !empty($invalid['lpassword']) && $invalid['lpassword'] && !$easyLogIn ? " invalid" : ""; ?>"
                                   title="Hasło powinno mieć przynajmniej 8 znaków"
                                   value="<?= !empty($this->get('lpassword')) ? $this->get('lpassword') : ""; ?>">
                        </div>
                        <div class="md-form pb-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="lremember"
                                       id="lremember" <?= !empty($this->get('lremember')) ? "checked" : ""; ?>>
                                <label for="lremember" class="form-check-label">Pozostań zalogowany</label>
                            </div>
                        </div>
                        <div class="md-form">
                            Zapomniałeś hasła?
                            <a href="#" data-toggle="modal"
                               data-target="#forgotPassword">Kliknij tutaj</a>
                        </div>
                        <input type="hidden" name="submitLogin" value="true">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                Zaloguj się <i class="fas fa-sign-in-alt ml-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="forgotPassword" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-notify modal-info modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <p class="heading lead">Zresetuj hasło</p>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center mb-1">
                <form action="/ajax/reset_password.php" method="post">
                    <div class="md-form ml-0 mr-0">
                        <input type="email" id="femail" name="femail" class="form-control form-control-sm validate ml-0"
                               pattern=".{8,}" required>
                        <label for="femail" class="ml-0">
                            Podaj adres e-mail
                        </label>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" name="forgotPassword" class="btn btn-primary mt-1">
                            Wyślij <i class="fas fa-sign-in-alt ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
