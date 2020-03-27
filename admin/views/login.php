<?php

use classes\Functions as fs;

if (LOGGED_IN) {
    header("Location: /admin");
}
?>
<section class="container login">
    <div class="row">
        <?php if (!empty($message)) { ?>
            <div class="col-12 alert alert-<?= $alert; ?>"><?= $message; ?></div>
        <?php } ?>
        <div class="col-12 col-md-6 offset-md-3 login">
            <form action="" method="post">
                <p class="h4 text-center mb-4">Log in</p>
                <div class="md-form pb-3">
                    <i class="fa fa-user prefix"></i>
                    <input type="email" name="email" id="email" required
                           class="form-control validate<?= !empty($invalid['email']) && $invalid['email'] ? " invalid" : ""; ?>"
                           value="<?= !empty($_POST['email']) ? $_POST['email'] : ""; ?>">
                    <label for="email">E-mail</label>
                </div>
                <div class="md-form">
                    <i class="fa fa-lock prefix"></i>
                    <input type="password" name="password" id="password" pattern=".{8,}" required
                           class="form-control validate<?= !empty($invalid['password']) && $invalid['password'] ? " invalid" : ""; ?>"
                           title="Minimum password length is 8"
                           value="<?= !empty($_POST['password']) ? $_POST['password'] : ""; ?>">
                    <label for="password"
                           data-error="Minimum password length is 8">Password</label>
                </div>
                <div class="md-form pb-3 ml-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember"
                               id="remember" <?= !empty($_POST['remember']) ? "checked" : ""; ?>>
                        <label for="remember" class="form-check-label">Keep me signed</label>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary" name="submitLogin" value="Log in">
            </form>
        </div>
    </div>
</section>