<?php

use classes\User;
use classes\Functions as fs;

$this->title = "Zarejestruj się";
?>

<section class="container">
    <?php if (!empty($message)) { ?>
    <div class="row">
        <div class="col-12 alert alert-<?= $alert; ?>"><?= $message; ?></div>
    </div>
    <?php } ?>
    <div class="row">
        <div class="col-12 col-md-6 offset-md-3">
            <h1 class="text-center">
                <img class="img-circle img-medium" src="<?= IMG_URL; ?>/logo.svg" alt="logo"> <?= PAGE_NAME; ?>
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
                    <form action="/ajax/register/register" method="post">
                        <div class="form-group">
                            <label for="email"
                                   data-error="It doesn't look like correct e-mail address">
                                E-mail
                            </label>
                            <input type="email" name="email" id="email" required
                                   class="form-control validate<?= !empty($invalid['email']) && $invalid['email'] ? " invalid" : ""; ?>"
                                   title="It doesn't look like correct e-mail address"
                                   value="<?= !empty($this->get('email')) ? $this->get('email') : ""; ?>">
                        </div>
                        <div class="form-group">
                            <label for="name" data-error="Minimum name length is 3">
                                Nazwa użytkownika
                            </label>
                            <input type="text" name="name" id="name" pattern=".{3,}" required
                                   class="form-control validate<?= !empty($invalid['name']) && $invalid['name'] ? " invalid" : ""; ?>"
                                   title="Minimum name length is 3"
                                   value="<?= !empty($this->get('name')) ? $this->get('name') : ""; ?>">
                        </div>
                        <div class="form-group">
                            <label for="password" data-error="Minimum password length is 8">
                                Hasło
                            </label>
                            <input type="password" name="password" id="password" pattern=".{8,}" required
                                   class="form-control validate<?= !empty($invalid['password']) && $invalid['password'] ? " invalid" : ""; ?>"
                                   title="Minimum password length is 8"
                                   value="<?= !empty($this->get('password')) ? $this->get('password') : ""; ?>">
                        </div>
                        <div class="form-group">
                            <label for="r-password" data-error="Minimum password length is 8">
                                Powtórz hasło
                            </label>
                            <input type="password" name="r-password" id="r-password" pattern=".{8,}" required
                                   class="form-control validate<?= !empty($invalid['r-password']) && $invalid['r-password'] ? " invalid" : ""; ?>"
                                   title="Minimum password length is 8"
                                   value="<?= !empty($this->get('r-password')) ? $this->get('r-password') : ""; ?>">
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
                                   data-error="It doesn't look like correct e-mail address">
                                E-mail
                            </label>
                            <input type="email" name="lemail" id="lemail" required
                                   class="form-control validate<?= !empty($invalid['email']) && $invalid['email'] ? " invalid" : ""; ?>"
                                   title="It doesn't look like correct e-mail address"
                                   value="<?= !empty($this->get('email')) ? $this->get('email') : ""; ?>">
                        </div>
                        <div class="form-group">
                            <label for="lpassword" data-error="Minimum password length is 8">
                                Hasło
                            </label>
                            <input type="password" name="lpassword" id="lpassword" pattern=".{8,}" required
                                   class="form-control validate<?= !empty($invalid['lpassword']) && $invalid['lpassword'] && !$easyLogIn ? " invalid" : ""; ?>"
                                   title="Minimum password length is 8"
                                   value="<?= !empty($this->get('lpassword')) && !$easyLogIn ? $this->get('lpassword') : ""; ?>">
                        </div>
                        <div class="md-form pb-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="lremember"
                                       id="lremember" <?= !empty($_POST['lremember']) && !$easyLogIn ? "checked" : ""; ?>>
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
