<?php
use classes\Functions as fs;
?>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="HandheldFriendly" content="true"/>
<meta name="title" content="<?= PAGE_NAME; ?>">
<meta name="description" content="CoTyp.pl">
<meta name="theme-color" content="#FFF">
<?php if ($this->view === "error") { ?>
    <meta property="og:title" content="Błąd 404"/>
    <meta property="og:description" content="Typie! Takiej strony nie ma!"/>
    <meta property="og:image" content="<?= IMG_URL; ?>/404.jpg"/>
    <meta property="og:image:alt" content="Błąd 404"/>
    <meta property="og:image:width" content="1920"/>
    <meta property="og:image:height" content="1200"/>
<?php } else { ?>
    <meta property="og:title" content="<?= PAGE_NAME; ?>"/>
    <meta property="og:description" content="Wpadaj! Typuj! Wygrywaj! Wielka przygoda właśnie się zaczyna!"/>
    <meta property="og:image" content="<?= IMG_URL; ?>/background.jpg"/>
    <meta property="og:image:alt" content="CoTyp"/>
    <meta property="og:image:width" content="1915"/>
    <meta property="og:image:height" content="1078"/>
<?php } ?>
<meta property="og:url" content="<?= CURRENT_URL; ?>"/>
<meta property="og:type" content="website"/>
<meta property="fb:app_id" content="211253152858745"/>

<title><?= !empty($this->title) ? $this->title . " - " : ""; ?><?= PAGE_NAME; ?></title>

<link rel="shortcut icon" type="image/png" href="<?= IMG_URL; ?>/favicon.png"/>
<link rel="manifest" href="<?= INC_URL; ?>/manifest.json">

<style>#preloader{position:fixed;width:100%;height:100%;top:0;left:0;background:#FFF;z-index:1060;}#preloader .imgWrapper {position:absolute;top:50%;left:50%;width:40%;height:auto;max-width:60px;max-height:60px;transform:translate(-50%, -80%);}#preloader .imgWrapper img {width:100%;height:100%;animation:pulsing 2s ease-in-out infinite;}@keyframes pulsing{0%{transform: scale3d(1, 1, 1);}50%{transform: scale3d(1.4, 1.4, 1.4);}100%{transform: scale3d(1, 1, 1);}}</style>
<noscript><style>#preloader {display:none!important;}</style></noscript>
<?php if (LOGGED_IN && DARK_THEME) { ?>
    <style>#preloader{background:#000;}#preloader .imgWrapper img{filter: invert(100%);opacity:.9;</style>
<?php } ?>

<style>.no_scroll {overflow:hidden!important;}</style>
<noscript><style>.no_scroll{overflow: auto !important}</style></noscript>
