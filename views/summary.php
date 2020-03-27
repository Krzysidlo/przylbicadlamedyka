<?php

use classes\Functions as fs;
use classes\User;

$this->titile = fs::t("Summary");

$events   = $events ?? [];
?>
<section class="container events">
<?php if (empty($events)) { ?>
    <div class="row">
        <div class="col-12 mt-5">
            <h1 class="text-center mb-3"><?= fs::t("Currently there are no events in competition") . " " . fs::getCompName(); ?></h1>
            <h2 class="text-center"><?= fs::t("If you think this is an error please contact page administrator"); ?></h2>
        </div>
    </div>
<?php } else { ?>
    <div class="row">
        <div class="col-6 col-lg-2 offset-lg-1 mb-3">
            <label for="changeGroup"><?= fs::t("Group"); ?></label>
            <select class="browser-default custom-select" id="changeGroup">
                <?php foreach ($groups as $groupsID => $group) { ?>
                <option value="<?= $groupsID ?>"<?= $curGroupsID == $groupsID ? " selected" : "" ?>><?= $group->name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
<?php
    $currentDay = "";
    $firstEventID = current($events)->id;
    $lastEventID = end($events)->id;
    foreach ($events as $eventsID => $event) {
        $newDay = ($currentDay !== $event->displayDate);
        $currentDay = $event->displayDate;
        if ($newDay) {
            if ($event->id !== $firstEventID) { ?>
        </div>
    </div>
            <?php } ?>
    <div class="row">
        <div class="col-12 col-lg-10 col-centered">
            <div class="title"><?= $event->displayDate; ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-lg-10 col-centered eventsBox">
        <?php } ?>
            <form action="/ajax/save_score.php" method="post"  id="<?= $eventsID; ?>"
                  class="row event"
                  data-date="<?= $event->date; ?>" data-id="<?= $eventsID; ?>"
                  data-status="<?= $event->status; ?>">
                <div class="col-3 col-md-2">
                    <div class="row">
                        <div class="col-6 jokTime">
                        </div>
                        <div class="col-6 jokTime">
                            <div class="title" data-timed="<?= $event->localDate->format("H:i"); ?>"
                                 data-inplay="<?= fs::t("Playing"); ?>" data-finished="<?= fs::t("Finished"); ?>">
                               <?= IS_ROOT && $event->status !== "TIMED" ? $event->displayStatus : ""; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col team">
                    <div class="row">
                        <div class="col-6">
                            <label class="input">
                                <img src="<?= IMG_URL; ?>/blank.gif" title="<?= fs::t($event->homeTeamName); ?>"
                                     class="flag flag-<?= fs::getCountryCode($event->homeTeamName, true); ?>"
                                     alt="<?= $event->homeTeamName; ?>"/>
                                <span data-value="<?= fs::t($event->homeTeamName); ?>"></span>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="input">
                                <img src="<?= IMG_URL; ?>/blank.gif" title="<?= fs::t($event->awayTeamName); ?>"
                                     class="flag flag-<?= fs::getCountryCode($event->awayTeamName, true); ?>"
                                     alt="<?= $event->awayTeamName; ?>"/>
                                <span data-value="<?= fs::t($event->awayTeamName); ?>"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-9 offset-3 col-md-3 offset-md-0 team inputs">
                    <div class="row">
                        <div class="col-6 col-md">
                            <h3 class="score homeTeam"><?= $event->result->homeTeam['DISP'] ?? ""; ?></h3>
                            <?php if ($event->result->homeTeam['DISP'] !== NULL) { ?>
                            <span class="score divider"></span>
                            <?php } ?>
                        </div>
                        <div class="col-6 col-md">
                            <h3 class="score awayTeam"><?= $event->result->awayTeam['DISP'] ?? ""; ?></h3>
                        </div>
                    </div>
                </div>
                <?php if (!empty($event->getUsersBets())) {
                    foreach ($event->getUsersBets() as $usersID => $userBet) {
                        try {
                            $user = new User($usersID);
                        } catch (Exception $e) {
                            fs::log("Error: " . $e->getMessage());
                            continue;
                        }
                        $userLogin = $usersID === USER_ID ? fs::t("You") : $user->name; ?>
                <div class="col-12 prediction summary bet<?= $userBet->success; ?>" data-showall="<?= fs::t("Show all"); ?>">
                    <div class="row">
                        <div class="col-5 offset-1 col-md-4 offset-md-6 text-left text">
                            <a href="/user/<?= $user->login; ?>" class="preload">
                                <img src="<?= $user->getAvatar(); ?>"
                                     class="img-responsive img-circle img-small"
                                     alt="avatar"> <?= $userLogin; ?>
                            </a>
                        </div>
                        <div class="col-3 offset-1 col-md-1 offset-md-0 col-md-centered">
                            <div class="row">
                                <div class="col"><?= $userBet->homeTeam; ?></div>
                                <div class="pred divider"></div>
                                <div class="col"><?= $userBet->awayTeam; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                    <?php } if (count($event->getUsersBets()) > 5) { ?>
                <div class="col-12 prediction summary showAll bet">
                    <span class="show"><?= fs::t("Show all"); ?> <i class="fas fa-chevron-down"></i></span>
                    <span class="hide"><?= fs::t("Hide"); ?> <i class="fas fa-chevron-up"></i></span>
                </div>
                    <?php }
                } ?>
            </form>
        <?php if ($event->id === $lastEventID) { ?>
        </div>
    </div>
        <?php }
    }
} ?>
</section>
