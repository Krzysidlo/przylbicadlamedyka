<?php

use classes\User;

//var_dump($user->getAddress()->location);
//die();

?>
<section class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 mt-5">
            <form action="/ajax/register/address">
                <div class="row">
                    <div class="col-12 mt-4 mb-3">
                        <h4 class="title">Twoje dane</h4>
                    </div>
                    <div class="form-group col-6">
                        <label for="firstName">
                            Imię
                        </label>
                        <input type="text" name="firstName" id="firstName" placeholder="Imię" required
                               class="form-control" readonly
                               title='Pole "Imię" jest wymagane'
                               value="<?= $user->firstName; ?>">
                    </div>
                    <div class="form-group col-6">
                        <label for="lastName">
                            Nazwisko
                        </label>
                        <input type="text" name="lastName" id="lastName" placeholder="Nazwisko" required
                               class="form-control" readonly
                               title='Pole "Nazwisko" jest wymagane'
                               value="<?= $user->lastName; ?>">
                    </div>
                    <div class="form-group col-6">
                        <label for="tel">
                            Numer telefonu
                        </label>
                        <input type="text" name="tel" id="tel" placeholder="Numer telefonu" required
                               class="form-control" readonly
                               value="<?= $user->tel; ?>">
                    </div>
                    <div class="form-group col-6">
                        <label for="email">
                            E-mail
                        </label>
                        <input type="email" name="email" id="email" placeholder="E-mail" required
                               class="form-control locked" readonly
                               value="<?= $user->email; ?>">
                    </div>
                </div>
                <div class="row">
                    <?php if (USER_PRV === User::USER_PRODUCER) { ?>
                        <div class="form-group col-12 mb-4">
                            <label for="pinName">
                                Nazwa Producenta
                            </label>
                            <input type="text" name="pinName" id="pinName" placeholder="Nazwa" required
                                   class="form-control validate" readonly
                                   title='Pole "Nazwa" jest wymagane'
                                   value="<?= $user->getAddress()->pin_name ?? ""; ?>">
                        </div>
                    <?php } ?>
                    <div class="form-group col-6">
                        <label for="city">
                            Miasto
                        </label>
                        <input type="text" name="city" id="city" placeholder="Miasto" required
                               class="form-control address" readonly
                               title='Pole "Miasto" jest wymagane'
                               value="<?= $user->getAddress()->city; ?>">
                    </div>
                    <div class="form-group col-6">
                        <label for="street">
                            Ulica
                        </label>
                        <input type="text" name="street" id="street" placeholder="Ulica" required
                               class="form-control address" readonly
                               title='Pole "Ulica" jest wymagane'
                               value="<?= $user->getAddress()->street; ?>">
                    </div>
                    <div class="form-group col-6 mt-2">
                        <label for="building">
                            Numer Budynku
                        </label>
                        <input type="text" name="building" id="building" placeholder="Numer Budynku" required
                               class="form-control address" readonly
                               title='Pole "Numer Budynku" jest wymagane'
                               value="<?= $user->getAddress()->building; ?>">
                    </div>
                    <div class="form-group col-6 mt-2">
                        <label for="flat">
                            Numer Lokalu
                        </label>
                        <input type="text" name="flat" id="flat" placeholder="Numer Lokalu"
                               class="form-control validate>" readonly
                               value="<?= $user->getAddress()->flat; ?>">
                    </div>
                    <div class="col-12 mt-4">
                        <h4 class="title">Twoja lokalizacja</h4>
                    </div>
                    <div class="col-12 mt-4">
                        <input type="hidden" name="location" value="<?= $user->getAddress()->location; ?>">
                        <div id="addressMap"></div>
                        <div class="load">
                            <img src="<?= IMG_URL; ?>/loading.gif" alt="loading">
                        </div>
                    </div>
                    <div class="col-12 mt-3 mb-5">
                        <button class="btn btn-red right edit mx-0">Edytuj</button>
                        <button type="submit" class="btn btn-red right mx-0">Zapisz</button>
                        <button class="btn btn-white right cancel mr-3">Anuluj</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
