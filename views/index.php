<?php
$userPrv    ??= User::USER_NO_CONFIRM;
$activities ??= [];
$bascinet   ??= 0;
$material   ??= 0;
$ready      ??= 0;
$delivered  ??= 0;
$deliveredM ??= 0;
$deliveredB ??= 0;

use classes\User; ?>
<section class="container">
    <div class="row justify-content-center">
        <?php if (USER_PRV === User::USER_NO_CONFIRM) { ?>
            <div class="col-12 col-md-8 mt-5">
                <div class="alert alert-danger">Adres e-mail nie został potwierdzony. Nie można obecnie edytować danych, ani wykonywać żadnych akcji.
                    Jeżeli e-mail z linkiem do potwierdzenia nie dotarł, proszę kliklnąć
                    <a href="/ajax/register/sendConfirm" id="sendConfirm">tutaj</a>, aby wsłać ją ponownie.
                </div>
            </div>
        <?php } ?>
        <div class="col-12 col-md-8 mt-4 numbers">
            <div class="row">
                <?php if ($userPrv === User::USER_PRODUCER) { ?>
                <div class="col-6 col-md-4 numberBox">
                    <div class="number"><?= $material; ?></div>
                    <div class="description">Zgłoszone zapotrzebowanie</div>
                </div>
                <div class="col-6 col-md-4 numberBox">
                    <div class="number"><?= $ready; ?></div>
                    <div class="description">Czekające na odbiór</div>
                </div>
                <div class="col-6 col-md-4 numberBox">
                    <div class="number"><?= $delivered; ?></div>
                    <div class="description">Przekazane przyłbice</div>
                </div>
                <?php } else if ($userPrv === User::USER_DRIVER) { ?>
                    <div class="col-6 col-md-3 numberBox">
                        <div class="number"><?= $material; ?></div>
                        <div class="description">Posiadany materiał</div>
                    </div>
                    <div class="col-6 col-md-3 numberBox">
                        <div class="number"><?= $bascinet; ?></div>
                        <div class="description">Posiadane przyłbice</div>
                    </div>
                    <div class="col-6 col-md-3 numberBox">
                        <div class="number"><?= $deliveredM; ?></div>
                        <div class="description">Przekazany materiał</div>
                    </div>
                    <div class="col-6 col-md-3 numberBox">
                        <div class="number"><?= $deliveredB; ?></div>
                        <div class="description">Przekazane przyłbice</div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <hr>
        </div>
        <div class="col-12 col-md-8 mb-5 feed">
            <h4 class="title mt-3 mb-4">Aktywność</h4>
            <?php foreach ($activities as $html) {
                echo $html;
            } ?>
        </div>
    </div>
</section>
