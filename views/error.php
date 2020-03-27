<?php

use classes\Functions as fs;

http_response_code(404);
?>
<section class="container-fluid errorInfo" style="background-image: url(<?= IMG_URL; ?>/404.jpg);">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center"><?= fs::t("Error 404"); ?></h1>
            <br>
            <h2><?= fs::t("The page you are looking for does not exist"); ?>!</h2>
            <br>
            <h2><a href="/" class="preload"><?= fs::t("Go back to main page"); ?></a></h2>
            <?php if (fs::$mysqli->connect_error) { ?>
                <br><h2><?= fs::t("There was an unexpected error while connecting to a database"); ?></h2>
            <?php } ?>
        </div>
    </div>
</section>