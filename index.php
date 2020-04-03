<?php

require_once __DIR__ . "/config/config.php";
global $pageClass;

$content = $pageClass->content();

use classes\User; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <?= $pageClass->head(); ?>
</head>
<?php
$dataLogged = "";
if (LOGGED_IN) {
    $dataLogged = "data-logged='true'";
}
?>
<body class="<?= $pageClass->view; ?> no_scroll" <?= $dataLogged; ?>>
<div id="preloader">
    <div class="imgWrapper">
        <img src="<?= IMG_URL; ?>/logo.png">
    </div>
</div>
<?= $pageClass->menu(); ?>

<main data-page="<?= $pageClass->view; ?>">
    <?php if (LOGGED_IN && USER_PRV === User::USER_NO_CONFIRM) { ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 mt-5">
                    <div class="alert alert-danger">Adres e-mail nie został potwierdzony. Nie można obecnie edytować danych, ani wykonywać żadnych akcji.
                        Jeżeli e-mail z linkiem do potwierdzenia nie dotarł, proszę kliklnąć
                        <a href="/ajax/register/sendConfirm" id="sendConfirm">tutaj</a>, aby wsłać ją ponownie.
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?= $content; ?>
</main>

<?= $pageClass->modals(); ?>

<?= $pageClass->foot(); ?>
</body>
</html>