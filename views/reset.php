<?php

use classes\Functions as fs;

$this->title = "Reset hasła";
?>

<section class="container loginRegister">
    <div class="row">
        <div class="col-12 col-md-6 offset-md-3">
            <?php if (!empty($message)) { ?>
                <div class="col-12 alert alert-<?= $alert; ?>"><?= $message; ?></div>
            <?php } ?>
            <form action="" method="post">
                <p class="h4 text-center mb-4"><?= $this->title; ?></p>
                <div class="md-form pb-3">
                    <i class="fa fa-lock prefix"></i>
                    <input type="password" name="newPassword" id="newPassword" pattern=".{8,}" required
                           class="form-control validate<?= !empty($invalid['newPassword']) && $invalid['newPassword'] ? " invalid" : ""; ?>"
                           title="Minimum password length is 8>"
                           value="<?= !empty($this->get('newPassword')) ? $this->get('newPassword') : ""; ?>">
                    <label for="newPassword" data-error="Minimum password length is 8">Nowe hasło</label>
                </div>
                <div class="md-form">
                    <i class="fa fa-exclamation-triangle prefix"></i>
                    <input type="password" name="r-newPassword" id="r-newPassword" pattern=".{8,}" required
                           class="form-control validate<?= !empty($invalid['r-newPassword']) && $invalid['r-newPassword'] ? " invalid" : ""; ?>"
                           title="Minimum password length is 8"
                           value="<?= !empty($this->get('r-newPassword')) ? $this->get('r-newPassword') : ""; ?>">
                    <label for="r-newPassword" data-error="Minimum password length is 8">Powtórz nowe hasło</label>
                </div>
                <input type="submit" class="btn btn-primary" name="resetPassword" value="Zapisz">
            </form>
        </div>
    </div>
</section>