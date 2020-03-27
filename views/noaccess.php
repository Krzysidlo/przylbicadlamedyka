<?php

use classes\Functions as fs;

if (!LOGGED_IN || USER_PRV >= 1) {
    header("Location: /");
    exit(0);
}

$this->title = fs::t("Red card");
?>
<section class="container-fluid errorInfo" style="background-image: url(<?= IMG_URL; ?>/noaccess.jpg);">
    <div class="row">
        <div class="col-12">
            <h1 class="h1-responsive text-center"><?= $this->title; ?></h1>
            <br>
            <h2 class="h2-responsive"><?= fs::t("For your outrages behavior you got the red card"); ?>!</h2>
        </div>
    </div>
</section>