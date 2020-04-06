<?php

use classes\User;
use classes\Frozen;
use classes\Hosmag;

$user = new User;

$compact = " compact";
$compact = (isset($_COOKIE['leftMenu']) && $_COOKIE['leftMenu'] === "null" ? "" : $compact);
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
        <button class="btn btn-white my-0 actions" data-toggle="modal" data-target="#bascinetModal">
            Zgłoś gotowe przyłbice
        </button>
        <button class="btn btn-outline-white my-0 actions" data-toggle="modal" data-target="#materialModal">
            Zgłoś zapotrzebowanie
        </button>
    <?php }
    if (!$user->noAddress() && USER_PRV === User::USER_DRIVER) { ?>
        <button class="btn btn-outline-white my-0 actions" data-toggle="modal" data-target="#driverModal">
            Zgłoś dostarczenie / odbiór
        </button>
    <?php }
    if (!$user->noAddress() && USER_PRV === User::USER_NO_CONFIRM) { ?>
        <span class="navbar-brand actions">Aby móc wykonywać akcje proszę potwierdzić adres e-mail</span>
    <?php } ?>
</nav>

<nav class="navbar fixed-left<?= $compact; ?>">
    <?php if (!$user->noAddress()) { ?>
        <ul class="navbar-nav">
            <li class="nav-item<?= $this->menu === "index" ? " active" : ""; ?>">
                <a href="/" class="nav-link preload">
                    <span class="material-icons">assignment</span>
                    <span>Aktywność</span>
                </a>
            </li>
            <?php if (USER_PRV !== User::USER_NO_CONFIRM) { ?>
            <li class="nav-item<?= $this->menu === "map" ? " active" : ""; ?>">
                <a href="/map" class="nav-link preload">
                    <span class="material-icons">map</span>
                    <span>Mapa</span>
                </a>
            </li>
            <?php } ?>
            <li class="nav-item<?= $this->menu === "settings" ? " active" : ""; ?>">
                <a href="/settings" class="nav-link preload">
                    <span class="material-icons">build</span>
                    <span>Ustawienia</span>
                </a>
            </li>
            <?php if ($user->getPrivilege() === User::USER_DRIVER) {
                $trips = Frozen::count(USER_ID, "trips");
                $trips += Hosmag::count(USER_ID, "trips"); ?>
                <li class="nav-item<?= $this->menu === "trips" ? " active" : ""; ?>">
                    <a href="/trips" class="nav-link preload">
                        <span class="material-icons">commute</span>
                        <span>Planowane przejazdy (<?= $trips; ?>)</span>
                    </a>
                </li>
            <?php }
            if (USER_PRV === User::USER_PRODUCER) { ?>
                <li class="nav-item actions">
                    <a href="#" class="btn btn-white nav-link" data-toggle="modal" data-target="#bascinetModal">
                        Zgłoś gotowe przyłbice
                    </a>
                </li>
                <li class="nav-item actions">
                    <a href="#" class="btn btn-red nav-link" data-toggle="modal" data-target="#materialModal">
                        Zgłoś zapotrzebowanie
                    </a>
                </li>
            <?php }
            if (USER_PRV === User::USER_DRIVER) { ?>
                <li class="nav-item actions">
                    <a href="#" class="btn btn-red nav-link" data-toggle="modal" data-target="#driverModal">
                        Zgłoś dostarczenie / odbiór
                    </a>
                </li>
            <?php }
            if (USER_PRV === User::USER_NO_CONFIRM) { ?>
                <li class="nav-item actions">
                    <span class="nav-link">Aby móc wykonywać akcje proszę potwierdzić adres e-mail</span>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
    <ul class="navbar-nav navbar-bottom">
        <li class="nav-item">
            <a href="/logout" class="nav-link preload">
                <span class="material-icons">person_outline</span>
                <span>Wyloguj się</span>
            </a>
        </li>
    </ul>
</nav>