<?php

use classes\Functions as fs;

$this->title = "Ustawienia";

?>
<section class="container mainInfo">
    <div class="row">
        <div class="col-12">
            <h1>Ustawienia</h1>
        </div>
    </div>
</section>

<section class="container form">
    <div class="row">
        <form action="/ajax/save_settings.php" method="post" class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <input type="hidden" name="darkTheme" value="false">
            <input type="hidden" name="gravatar" value="false">
            <input type="hidden" name="pushNotifications" value="false">
            <input type="hidden" name="parallax" value="false">
            <div class="form-group">
                <label class="row">
					<span class="col-8 text-input-label">
                        Nazwa użytkownika
						<hr class="d-sm-none divider">
					</span>
                    <span class="col-4">
						<input type="text" name="changed_name" value="<?= USER_NAME; ?>" class="form-control">
					</span>
                </label>
            </div>
            <hr class="d-sm-none">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-secondary" id="changeAddress">
                        Zmień adres&nbsp;&nbsp;&nbsp;<i class="fas fa-map-marked-alt"></i>
                    </button>
                </div>
            </div>
            <hr class="d-sm-none">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-secondary" id="changePassword">
                        Zmień hasło&nbsp;&nbsp;&nbsp;<i class="fas fa-key"></i>
                    </button>
                </div>
            </div>
            <hr>
            <input type="submit" class="btn btn-primary" name="saveSettings" value="Zapisz">
        </form>
    </div>
</section>

<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog cascading-modal modal-avatar modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center mb-1">
                <h5 class="mt-1 mb-2">Zmień hasło</h5>
                <form action="/ajax/register/chgpswd" method="post">
                    <div class="md-form ml-0 mr-0">
                        <input type="password" id="cpassword" name="cpassword"
                               class="form-control form-control-sm validate ml-0" pattern=".{8,}"
                               title="Minimum password length is 8>" required>
                        <label for="cpassword" class="ml-0">
                            Obecne hasło
                        </label>
                    </div>
                    <div class="md-form ml-0 mr-0">
                        <input type="password" id="password" name="password"
                               class="form-control form-control-sm validate ml-0" pattern=".{8,}"
                               title="Minimum password length is 8" required>
                        <label for="password" class="ml-0">
                            Nowe hasło
                        </label>
                    </div>
                    <div class="md-form ml-0 mr-0">
                        <input type="password" id="rpassword" name="rpassword"
                               class="form-control form-control-sm validate ml-0" pattern=".{8,}"
                               title="Minimum password length is 8" required>
                        <label for="rpassword" class="ml-0">
                            Powtórz nowe hasło
                        </label>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-warning mt-1"
                                data-dismiss="modal">Anuluj</button>
                        <button type="submit" name="changePassword" class="btn btn-info mt-1">
                            Zapisz <i class="fas fa-sign-in ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
