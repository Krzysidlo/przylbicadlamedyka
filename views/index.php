<?php
$activities ??= [];
$material   ??= 0;
$ready      ??= 0;
$delivered  ??= 0;
?>
<section class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 offset-md-2 mt-4 numbers">
            <div class="row">
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
            </div>
        </div>
        <div class="col-12 col-md-8 offset-md-2">
            <hr>
        </div>
        <div class="col-12 col-md-8 offset-md-2 feed">
            <h4 class="title mt-3 mb-4">Aktywność</h4>
            <?php foreach ($activities as $html) {
                echo $html;
            } ?>
        </div>
    </div>
</section>
