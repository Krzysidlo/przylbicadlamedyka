<?php

use classes\Functions as fs;
use classes\User;

$user = new User;
$avatarUrl = $user->getAvatar();
$avatar    = "<img src='{$avatarUrl}' class='img-responsive img-circle img-menu' alt='avatar'>&nbsp;&nbsp;";
?>

<nav class="navbar navbar-expand-xl scrolling-navbar fixed-top<?= DARK_THEME ? " navbar-dark bg-dark" : " navbar-light bg-light"; ?>">
    <a class="navbar-brand" href="/"><img src="<?= IMG_URL; ?>/logo.svg" class="img-nav" alt=""></a>
    <a class="navbar-brand"
       href="/"><?= PAGE_NAME; ?><?= $user->getOption('competition') ? " - " . fs::getCompName() : ""; ?></a>
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
            <li class="nav-item<?= $this->menu === $user->login ? " active" : ""; ?>">
                <a class="nav-link preload" href="/user/<?= $user->login; ?>">
                    <?= fs::t("My results") . ($this->menu === $user->login ? " <span class='sr-only'>(current)</span>" : ""); ?>
                </a>
            </li>
            <li class="nav-item<?= $this->menu === "ranking" ? " active" : ""; ?>">
                <a class="nav-link preload" href="/ranking">
                    <?= fs::t("Ranking") . ($this->menu === "ranking" ? " <span class='sr-only'>(current)</span>" : ""); ?>
                </a>
            </li>
            <li class="nav-item<?= $this->menu === "rules" ? " active" : ""; ?>">
                <a class="nav-link preload" href="/rules">
                    <?= fs::t("Rules") . ($this->menu === "rules" ? " <span class='sr-only'>(current)</span>" : ""); ?>
                </a>
            </li>
            <?php if (USER_PRV >= 3) { ?>
                <li class="nav-item<?= $this->menu === "summary" ? " active" : ""; ?>">
                    <a class="nav-link preload" href="/summary">
                        <?= fs::t("Summary") . ($this->menu === "summary" ? " <span class='sr-only'>(current)</span>" : ""); ?>
                    </a>
                </li>
            <?php } ?>
            <li class="nav-item">
                <a class="srp2 active nav-link" href="#">&nbsp;&nbsp;</a>
            </li>
        </ul>
        <ul class="navbar-nav mr-right">
            <li class="nav-item dropdown profile<?= in_array($this->view, ["settings", "check"]) ? " active" : ""; ?>">
                <a class="nav-link dropdown-toggle" href="/<?= USER_PRV >= 2 ? "settings" : ""; ?>" id="profile"
                   role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $avatar . " " . USER_NAME; ?>
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
                                <a class="dropdown-item preload" href="/admin/settings">
                                    <?= fs::t("Admin"); ?>
                                </a>
                            <?php } ?>
                        <?php } ?>
                        <div class="dropdown-divider"></div>
                    <?php } ?>
                    <a class="dropdown-item preload" href="/logout"><?= fs::t("Logout"); ?></a>
                    <a class="srp3 active dropdown-item not-visible" href="#">&nbsp;&nbsp;</a>
                </div>
            </li>
            <?php list($new, $notifications) = fs::getNotifications(10);
            $count = count($notifications); ?>
            <li class="nav-item dropdown notifications<?= $this->view === "notifications" ? " active" : ""; ?>">
                <a class="nav-link dropdown-toggle" href="/notifications    " id="notifications" role="button"
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
            <?php /* Zmiana języka - obecnie nie widzę potrzeby
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="language" role="button"
				   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?= strtoupper(fs::$lang); ?>
				</a>
				<div class="dropdown-menu" aria-labelledby="language">
					<?php foreach (fs::$langArr as $item) {
						if ($item !== fs::$lang) { ?>
							<a class="dropdown-item preload changeLang" href="#" data-lang="<?= $item; ?>">
								<?= strtoupper($item); ?>
							</a>
						<?php }
					} ?>
				</div>
			</li>
            */ ?>
            <li class="nav-item">
                <div class="nav-link fb-like" data-href="https://www.facebook.com/cotypuj" data-layout="button_count"
                     data-action="like" data-size="large" data-show-faces="true" data-share="false"></div>
            </li>
        </ul>
    </div>
</nav>
