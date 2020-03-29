<?php

use classes\User;
use classes\Functions as fs;

$user = new User;
?>

<nav class="navbar navbar-dark fixed-top navbar-light bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <div class="animated-icon"><span></span><span></span><span></span></div>
    </button>
    <span class="navbar-brand mr-auto"><?= PAGE_NAME; ?></span>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item<?= $this->menu === "index" ? " active" : ""; ?>">
                <a class="nav-link preload" href="/">
                    <?= "Strona główna" . ($this->menu === "index" ? " <span class='sr-only'>(current)</span>" : ""); ?>
                </a>
            </li>
        </ul>
    </div>
</nav>
