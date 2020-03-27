<?php
$this->title = "Strona w trakcie przebudowy";
?>
<section class="container-fluid errorInfo" style="background-image: url(<?= IMG_URL; ?>/construction.jpg);">
    <div class="row">
        <div class="col">
            <h1 class="text-center">Strona w trakcie przebudowy. Przepraszamy za utrudnienia.</h1>
            <?php if (!DB_CONN) { ?>
                <br><h2>Błąd połączenia z bazą danych</h2>
            <?php } ?>
        </div>
    </div>
</section>