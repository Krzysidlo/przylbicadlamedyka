<?php

require_once __DIR__ . "/config/config.php";
global $pageClass;

$content = $pageClass->content();
?>
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
    <?= $content; ?>
</main>

<?= $pageClass->modals(); ?>

<?= $pageClass->foot(); ?>
</body>
</html>