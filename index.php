<?php

use classes\Functions as fs;

require_once __DIR__ . "/config/config.php";
global $pageClass;

$content = $pageClass->content();
?>
<!DOCTYPE html>
<html lang="<?= fs::$lang; ?>">
<head>
    <?= $pageClass->head(); ?>
</head>
<?php
$parallax = fs::getOption('parallax');
$parallaxUrl  = fs::getOption('parallaxUrl');

$hasParallax = (($parallax && $pageClass->view === MAIN_VIEW) || $pageClass->view === "error");

$bgImage = ($pageClass->bgImage ?? false);
?>
<body class="<?= $pageClass->view; ?> no_scroll<?= $hasParallax ? " hasParallax" : ""; ?>"<?= $bgImage ? " style=\"background-image: url('{$bgImage}')\"" : ""; ?><?= LOGGED_IN ? " data-logged='true'" : ""; ?>>
<div class="mask">
</div>
<div id="preloader">
    <div class="imgWrapper">
        <img src="<?= IMG_URL; ?>/logo.svg">
    </div>
</div>

<?= $pageClass->menu(); ?>

<?php if ($pageClass->view === MAIN_VIEW) { ?>
    <section class="container-fluid mainInfo<?= $parallax ? " parallax" : ""; ?>" style="<?= $parallax ? ($parallaxUrl ? "background-image: url('{$parallaxUrl}')" : "background-image: url(" . COMP_URL . "/" . COMPETITION_ID . ".jpg);") : ""; ?>">
        <div class="row">
            <div class="col-12">
                <h1><?= fs::getCompName(); ?></h1>
            </div>
        </div>
        <?php if ($parallax) { ?>
            <div class="downArrow">
                <img class="animated pulse infinite" alt="" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDMyIDMyIiBoZWlnaHQ9IjMycHgiIGlkPSLQodC70L7QuV8xIiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAzMiAzMiIgd2lkdGg9IjMycHgiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPjxwYXRoIGQ9Ik0yNC4yODUsMTEuMjg0TDE2LDE5LjU3MWwtOC4yODUtOC4yODhjLTAuMzk1LTAuMzk1LTEuMDM0LTAuMzk1LTEuNDI5LDAgIGMtMC4zOTQsMC4zOTUtMC4zOTQsMS4wMzUsMCwxLjQzbDguOTk5LDkuMDAybDAsMGwwLDBjMC4zOTQsMC4zOTUsMS4wMzQsMC4zOTUsMS40MjgsMGw4Ljk5OS05LjAwMiAgYzAuMzk0LTAuMzk1LDAuMzk0LTEuMDM2LDAtMS40MzFDMjUuMzE5LDEwLjg4OSwyNC42NzksMTAuODg5LDI0LjI4NSwxMS4yODR6IiBmaWxsPSIjMTIxMzEzIiBpZD0iRXhwYW5kX01vcmUiLz48Zy8+PGcvPjxnLz48Zy8+PGcvPjxnLz48L3N2Zz4="/>
            </div>
        <?php } ?>
    </section>
<?php } ?>

<main data-page="<?= $pageClass->view; ?>">
    <?= $content; ?>
    <?php if (LOGGED_IN) { ?>
        <div class="ccm" style="display: none">
            <div class="srp5 active not-visible"></div>
        </div>
    <?php } ?>
    <div id="hiddenPicTrigger" class="srp1 active"></div>
</main>

<?= $pageClass->foot(); ?>
</body>
</html>