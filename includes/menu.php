<?php

use classes\User;

$user = new User;

$show = (($_COOKIE['leftMenu'] ?? "null") === "null" ? "" : " small");
?>

    <nav class="navbar fixed-top navbar-dark bg-red">
        <?php if (!$user->noAddress()) { ?>
            <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <div class="animated-icon"><span></span><span></span><span></span></div>
            </button>
        <?php } ?>
        <span class="navbar-brand <?= $user->noAddress() ? "mr-auto" : "mx-auto"; ?>"><?= PAGE_NAME; ?></span>

        <?php if (!$user->noAddress() && USER_PRV === User::USER_PRODUCER) { ?>
            <button class="btn btn-white my-0" data-toggle="modal" data-target="#bascinetModal">Zgłoś gotowe przyłbice</button>
            <button class="btn btn-outline-white my-0" data-toggle="modal" data-target="#materialModal">Zgłoś zapotrzebowanie</button>
        <?php }
        if (!$user->noAddress() && USER_PRV === User::USER_DRIVER) { ?>
            <button class="btn btn-white my-0" data-toggle="modal" data-target="#deliveredModal">Zgłoś dostarczenie</button>
            <button class="btn btn-outline-white my-0" data-toggle="modal" data-target="#collectionModal">Zgłoś odbiór</button>
        <?php }
        if (!$user->noAddress() && USER_PRV === User::USER_NO_CONFIRM) { ?>
            <span class="navbar-brand">Aby móc wykonywać akcje proszę potwierdzić adres e-mail</span>
        <?php } ?>
    </nav>

<?php if (!$user->noAddress()) { ?>
    <nav class="navbar fixed-left<?= $show; ?>">
        <ul class="navbar-nav">
            <li class="nav-item<?= $this->menu === "index" ? " active" : ""; ?>">
                <a href="/" class="nav-link">
                    <span class="material-icons">assignment</span>
                    <span>Aktywność</span>
                </a>
            </li>
            <li class="nav-item<?= $this->menu === "map" ? " active" : ""; ?>">
                <a href="/map" class="nav-link">
                    <span class="material-icons">map</span>
                    <span>Mapa</span>
                </a>
            </li>
            <li class="nav-item<?= $this->menu === "settings" ? " active" : ""; ?>">
                <a href="/settings" class="nav-link">
                    <span class="material-icons">build</span>
                    <span>Ustawienia</span>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav navbar-bottom">
            <li class="nav-item">
                <a href="/logout" class="nav-link">
                    <span class="material-icons">person_outline</span>
                    <span>Wyloguj się</span>
                </a>
            </li>
        </ul>
    </nav>
<?php }