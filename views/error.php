<?php

use classes\Functions as fs;

http_response_code(404);
?>
<section class="container-fluid errorInfo" style="background-image: url(<?= IMG_URL; ?>/404.jpg);">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center">Błąd 404</h1>
            <br>
            <h2>Strona której szukasz nie istnieje.</h2>
            <br>
            <h2><a href="/" class="preload">Wróć na stronę główną</a></h2>
            <?php if (fs::$mysqli->connect_error) { ?>
                <br><h2>Wystąpił błąd przy łączeniu z bazą danych</h2>
            <?php } ?>
        </div>
    </div>
</section>