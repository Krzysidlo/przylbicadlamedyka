<?php

use classes\User;

?>
<section class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 mt-3">
            <p>Cześć! Dziękujemy za dołączenie do akcji, na początek ustalmy jak będziesz nam pomagał.</p>
        </div>
        <div class="col-12 col-md-8 mt-2">
            <form action="/ajax/register/address" class="row address">
                <input type="radio" class="hidden" name="type" id="producer" value="<?= User::USER_PRODUCER; ?>"
                       checked>
                <label for="producer" class="type col-6">
                    <?= file_get_contents(IMG_DIR . "/producer.svg"); ?>
                    <p>Jesetm producentem</p>
                </label>
                <input type="radio" class="hidden" name="type" id="driver" value="<?= User::USER_DRIVER; ?>">
                <label for="driver" class="type col-6">
                    <?= file_get_contents(IMG_DIR . "/driver.svg"); ?>
                    <p>Jestem kierowcą</p>
                </label>
                <div class="col-12 mt-5 mb-2">
                    <p>Pod jakim adresem będziemy mogli Cię znaleźć?</p>
                </div>
                <div class="form-group col-12 mb-4">
                    <input type="text" name="pinName" id="pinName" placeholder="Nazwa" required
                           class="form-control validate<?= !empty($invalid['pinName']) && $invalid['pinName'] ? " invalid" : ""; ?>"
                           title='Pole "Nazwa" jest wymagane'
                           value="<?= !empty($this->get('pinName')) ? $this->get('pinName') : ""; ?>">
                </div>
                <div class="form-group col-6">
                    <input type="text" name="city" placeholder="Miasto" required
                           class="form-control address validate<?= !empty($invalid['city']) && $invalid['city'] ? " invalid" : ""; ?>"
                           title='Pole "Miasto" jest wymagane'
                           value="<?= !empty($this->get('city')) ? $this->get('city') : ""; ?>">
                </div>
                <div class="form-group col-6">
                    <input type="text" name="street" placeholder="Ulica" required
                           class="form-control address validate<?= !empty($invalid['street']) && $invalid['street'] ? " invalid" : ""; ?>"
                           title='Pole "Ulica" jest wymagane'
                           value="<?= !empty($this->get('street')) ? $this->get('street') : ""; ?>">
                </div>
                <div class="form-group col-6 mt-2">
                    <input type="text" name="building" placeholder="Numer Budynku" required
                           class="form-control address validate<?= !empty($invalid['building']) && $invalid['building'] ? " invalid" : ""; ?>"
                           title='Pole "Numer Budynku" jest wymagane'
                           value="<?= !empty($this->get('building')) ? $this->get('building') : ""; ?>">
                </div>
                <div class="form-group col-6 mt-2">
                    <input type="text" name="flat" placeholder="Numer Lokalu"
                           class="form-control validate<?= !empty($invalid['flat']) && $invalid['flat'] ? " invalid" : ""; ?>"
                           title=''
                           value="<?= !empty($this->get('flat')) ? $this->get('flat') : ""; ?>">
                </div>
                <div class="col-12 mt-4">
                    <p>Zaznacz na mapie punkt w którym jest Twoja lokalizacja</p>
                </div>
                <div class="col-12 mt-4">
                    <input type="hidden" name="location">
                    <div id="addressMap"></div>
                    <div class="load">
                        <img src="<?= IMG_URL; ?>/loading.gif" alt="loading">
                    </div>
                </div>
                <div class="col-12 mt-3 mb-5">
                    <button type="submit" class="btn btn-red right">Zapisz</button>
                </div>
            </form>
        </div>
    </div>
</section>
