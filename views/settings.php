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
        <form action="/ajax/settings/save" method="post" class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <div class="form-group">
                <label class="row">
					<span class="col-7 text-input-label">
                        Imię
						<hr class="d-sm-none divider">
					</span>
                    <span class="col-5">
						<input type="text" name="firstname" value="<?= $user->firstName; ?>" class="form-control">
					</span>
                </label>
            </div>
            <div class="form-group">
                <label class="row">
					<span class="col-7 text-input-label">
                        Nazwisko
						<hr class="d-sm-none divider">
					</span>
                    <span class="col-5">
						<input type="text" name="lastname" value="<?= $user->lastName; ?>" class="form-control">
					</span>
                </label>
            </div>
            <hr class="d-sm-none">
            <div class="form-group">
                <label class="row">
					<span class="col-7 text-input-label">
                        Numer telefonu
						<hr class="d-sm-none divider">
					</span>
                    <span class="col-5">
                        <input type="tel" name="tel" pattern=".{9,}" value="<?= $user->tel; ?>" class="form-control">
                    </span>
                </label>
            </div>
            <hr class="d-sm-none">
            <div class="form-group">
                <label for="addressFinder">
                    Adres
                </label>
                <input type="text" id="addressFinder" class="form-control" placeholder="Wyszukaj adres na mapie">
                <input type="hidden" name="address" id="address" required
                       value="<?= $user->address; ?>">
                <div id="addressMap" class="mt-4"></div>
            </div>
            <hr class="d-sm-none">
            <div class="form-group">
                <label class="row">
					<span class="col-7 text-input-label">
                        Obecne hasło
						<hr class="d-sm-none divider">
					</span>
                    <span class="col-5">
						<input type="password" name="password" class="form-control">
					</span>
                </label>
            </div>
            <div class="form-group">
                <label class="row">
					<span class="col-7 text-input-label">
                        Nowe hasło
						<hr class="d-sm-none divider">
					</span>
                    <span class="col-5">
						<input type="password" name="npassword" class="form-control">
					</span>
                </label>
            </div>
            <div class="form-group">
                <label class="row">
					<span class="col-7 text-input-label">
                        Powtórz nowe hasło
						<hr class="d-sm-none divider">
					</span>
                    <span class="col-5">
						<input type="password" name="rnpassword" class="form-control">
					</span>
                </label>
            </div>
            <hr>
            <input type="submit" class="btn btn-primary" name="saveSettings" value="Zapisz">
        </form>
    </div>
</section>