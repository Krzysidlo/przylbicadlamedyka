<?php

use classes\User;
use classes\Functions as fs;

$user = new User;
?>

<nav class="navbar navbar-expand-xl scrolling-navbar fixed-top navbar-light bg-light">
    <a class="navbar-brand" href="/"><img src="<?= IMG_URL; ?>/logo.svg" class="img-nav" alt=""></a>
    <a class="navbar-brand" href="/"><?= PAGE_NAME; ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <div class="animated-icon"><span></span><span></span><span></span></div>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item<?= $this->menu === "index" ? " active" : ""; ?>">
                <a class="nav-link preload" href="/">
                    <?= fs::t("My bets") . ($this->menu === "index" ? " <span class='sr-only'>(current)</span>" : ""); ?>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav mr-right">
            <li class="nav-item dropdown profile<?= in_array($this->view, ["settings"]) ? " active" : ""; ?>">
                <a class="nav-link dropdown-toggle" href="/<?= USER_PRV >= 2 ? "settings" : ""; ?>" id="profile"
                   role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= USER_NAME; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="profile">
                    <?php if (USER_PRV >= 2) { ?>
                        <a class="dropdown-item preload<?= $this->view === "settings" ? " active" : ""; ?>"
                           href="/settings">
                            <?= fs::t("Settings"); ?>
                        </a>
                        <?php if (USER_PRV >= 4) { ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preload<?= $this->view === "check" ? " active" : ""; ?>"
                               href="/check">
                                <?= fs::t("Check for errors"); ?>
                            </a>
                            <?php if (IS_ROOT) { ?>
                                <a class="dropdown-item preload" href="/admin/">
                                    <?= fs::t("Admin"); ?>
                                </a>
                            <?php } ?>
                        <?php } ?>
                        <div class="dropdown-divider"></div>
                    <?php } ?>
                    <a class="dropdown-item preload" href="/logout"><?= fs::t("Logout"); ?></a>
                </div>
            </li>
            <?php [$new, $notifications] = fs::getNotifications(10);
            $count = count($notifications); ?>
            <li class="nav-item dropdown notifications<?= $this->view === "notifications" ? " active" : ""; ?>">
                <a class="nav-link dropdown-toggle" href="/notifications" id="notifications" role="button"
                   data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false" title="<?= fs::t("Notifications"); ?>">
                    <i class="fas fa-bell"></i> <span class="num<?= $new > 0 ? " new" : ""; ?>"><?= $new; ?></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="notifications">
                    <?php foreach ($notifications as $notification) { ?>
                        <a class="dropdown-item<?= !is_null($notification['href']) ? " preload" : ""; ?><?= $notification['new'] ? " active" : ""; ?>"
                           href="<?= !is_null($notification['href']) ? $notification['href'] : "#"; ?>">
                            <?= $notification['content']; ?>
                        </a>
                    <?php }
                    if ($count >= 10) { ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preload" href="/notifications">
                            <?= fs::t("See all"); ?>
                        </a>
                    <?php } ?>
                </div>
            </li>
        </ul>
    </div>
</nav>
