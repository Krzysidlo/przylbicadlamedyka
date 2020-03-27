<?php
use classes\Functions as fs;
use classes\User;

if (!empty($competitions)) { ?>
<section class="container competitions">
    <div class="row">
        <?php foreach ($competitions as $id => $comp) { ?>
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-0 col-lg-4 col-centered eventBox">
                <a href="/admin/points/<?= $id; ?>" class="event">
                    <div class="col-12">
                        <div class="title"><h3><?= $comp['name']; ?></h3></div>
                    </div>
                    <div class="col-12">
                        <img src="<?= $comp['picture']; ?>" class="img-responsive" alt="">
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
</section>
<?php } else if (!empty($events)) { ?>
<section class="container events" data-comp="<?= $competitionsID; ?>">
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary" id="recalculate">Wyzeruj ranking i przelicz punkty</button>
        </div>
    </div>
    <div class="row">
        <?php foreach ($events as $eventID => $event) {
            $diabled = ($event->status !== "FINISHED" ? " disabled" : ""); ?>
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-0 col-lg-4 col-centered eventBox">
                <div class="col-12">
                    <div class="title"><?= $event->displayDate; ?></div>
                </div>
                <div class="row event" data-status="<?= $event->status; ?>">
                    <div class="col-12">
                        <div class="title" data-timed="<?= $event->localDate->format("H:i"); ?>"
                             data-inplay="<?= fs::t("Playing"); ?>" data-finished="<?= fs::t("Finished"); ?>">
                            <?= $event->status !== "TIMED" ? $event->displayStatus : ""; ?>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 team">
                        <label>
                            <img src="<?= IMG_URL; ?>/blank.gif" title="<?= fs::t($event->homeTeamName); ?>"
                                 class="flag flag-<?= fs::getCountryCode($event->homeTeamName, true); ?>"
                                 alt="<?= $event->homeTeamName; ?>"/>
                            <span><?= fs::t($event->homeTeamName); ?></span>
                        </label>
                    </div>
                    <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 team">
                        <label>
                            <img src="<?= IMG_URL; ?>/blank.gif" title="<?= fs::t($event->awayTeamName); ?>"
                                 class="flag flag-<?= fs::getCountryCode($event->awayTeamName, true); ?>"
                                 alt="<?= $event->awayTeamName; ?>"/>
                            <span><?= fs::t($event->awayTeamName); ?></span>
                        </label>
                    </div>
                    <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 team">
                        <h3 class="score homeTeam"><?= $event->result->homeTeam['FT'] ?? 0; ?></h3>
                    </div>
                    <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 team">
                        <h3 class="score awayTeam"><?= $event->result->awayTeam['FT'] ?? 0; ?></h3>
                    </div>
                    <?php if (!empty($event->results)) {
                        foreach ($event->results as $usersID => $result) {
                            $user = new User($usersID);
                            $userLogin = ($usersID === USER_ID ? fs::t("You") : $user->name); ?>
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-5 text">
                                        <img src="<?= $user->getAvatar(); ?>"
                                             class="img-responsive img-circle img-small"
                                             alt="avatar"> <?= $userLogin; ?>:
                                    </div>
                                    <div class="col"><?= $result['home_team']; ?></div>
                                    <div class="col"><?= $result['away_team']; ?></div>
                                    <div class="col"><?= $result['score']; ?></div>
                                 </div>
                            </div>
                        <?php }
                    } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
<?php } ?>