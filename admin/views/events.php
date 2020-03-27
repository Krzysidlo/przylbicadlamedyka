<?php

use classes\Functions as fs;

?>

<section class="container events">
    <div class="row">
        <?php if (empty($events)) { ?>
            <div class="col-12">
                <h1 class="text-center mb-3"><?= fs::t("Currently there are no events in competition") . " " . fs::getCompName($competitionsID); ?></h1>
                <h2 class="text-center"><?= fs::t("If you think this is an error please contact page administrator"); ?></h2>
            </div>
        <?php } else {
            foreach ($events as $eventsID => $event) { ?>
                <div id="<?= $eventsID; ?>" class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-0 col-lg-4 col-centered eventBox">
                    <div class="col-12">
                        <div class="title"><?= $event->displayDate; ?></div>
                    </div>
                    <form action="/admin/ajax/change_score.php" method="post" class="row event"
                          data-date="<?= $event->date; ?>" data-id="<?= $eventsID; ?>"
                          data-status="<?= $event->status; ?>">
                        <i class="fas fa-sync score_load"></i>
                        <i class="fas fa-check score_save"></i>
                        <i class="fas fa-times score_err"></i>
                        <input type="hidden" name="eventDate" value="<?= $event->date; ?>">
                        <input type="hidden" name="eventDay" value="<?= $eventDay; ?>">
                        <div class="col-12">
                            <div class="title">
                                <?= $event->displayStatus; ?>
                            </div>
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 team">
                            <label for="homeTeam<?= $eventsID; ?>" class="<?= $event->label; ?>">
                                <img src="<?= IMG_URL; ?>/blank.gif" title="<?= fs::t($event->homeTeamName); ?>"
                                     class="flag flag-<?= fs::getCountryCode($event->homeTeamName, true); ?>"
                                     alt="<?= $event->homeTeamName; ?>"/>
                                <span><?= fs::t($event->homeTeamName); ?></span>
                            </label>
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 team">
                            <label for="awayTeam<?= $eventsID; ?>" class="<?= $event->label; ?>">
                                <img src="<?= IMG_URL; ?>/blank.gif" title="<?= fs::t($event->awayTeamName); ?>"
                                     class="flag flag-<?= fs::getCountryCode($event->awayTeamName, true); ?>"
                                     alt="<?= $event->awayTeamName; ?>"/>
                                <span><?= fs::t($event->awayTeamName); ?></span>
                            </label>
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 team">
                            <input type="tel" id="homeTeam<?= $eventsID; ?>" name="homeTeam" class="score homeTeam" value="<?= $event->result->homeTeam['FT'] ?? "" ?>">
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 team">
                            <input type="tel" id="awayTeam<?= $eventsID; ?>" name="awayTeam" class="score awayTeam" value="<?= $event->result->awayTeam['FT'] ?? "" ?>">
                        </div>
                        <div class="col-12 submitBtn">
                            <input type="submit" id="saveScore<?= $eventsID; ?>" class="btn btn-primary" value="Zapisz">
                        </div>
                    </form>
                </div>
            <?php }
        } ?>
    </div>
</section>