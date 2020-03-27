<?php

use classes\Functions as fs;

$events   = $events ?? [];
$eventDay = $eventDay ?? 1;

echo $this->pagination('top');
?>
<section class="container events">
<?php if (empty($events)) { ?>
    <div class="row">
        <div class="col-12 mt-5">
            <h1 class="text-center mb-3"><?= fs::t("Currently there are no events in competition") . " " . fs::getCompName(); ?></h1>
            <h2 class="text-center"><?= fs::t("If you think this is an error please contact page administrator"); ?></h2>
        </div>
    </div>
<?php } else {
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
                  class="row event<?= $event->prediction->joker ? " joker" : ""; ?>"
                  data-date="<?= $event->date; ?>" data-id="<?= $eventsID; ?>"
                  data-status="<?= $event->status; ?>">
                <i class="fas fa-sync score_load"></i>
                <i class="fas fa-check score_save"></i>
                <i class="fas fa-times score_err"></i>
                <input type="hidden" name="eventDate" value="<?= $event->date; ?>">
                <input type="hidden" name="eventDay" value="<?= $eventDay; ?>">
                <div class="col-2 order-1 col-md-1">
                    <div class="title" data-timed="<?= $event->localDate->format("H:i"); ?>"
                         data-inplay="<?= fs::t("Playing"); ?>" data-finished="<?= fs::t("Finished"); ?>">
                       <?= IS_ROOT && $event->status !== "TIMED" ? $event->displayStatus : ""; ?>
                    </div>
                </div>
                <div class="col order-2 team">
                    <div class="row">
                        <div class="col-6">
                            <label for="homeTeam<?= $eventsID; ?>" class="input">
                                <img src="<?= IMG_URL; ?>/blank.gif" title="<?= fs::t($event->homeTeamName); ?>"
                                     class="flag flag-<?= fs::getCountryCode($event->homeTeamName, true); ?>"
                                     alt="<?= $event->homeTeamName; ?>"/>
                                <span data-value="<?= fs::t($event->homeTeamName); ?>"></span>
                            </label>
                        </div>
                        <div class="col-6">
                            <label for="awayTeam<?= $eventsID; ?>" class="input">
                                <img src="<?= IMG_URL; ?>/blank.gif" title="<?= fs::t($event->awayTeamName); ?>"
                                     class="flag flag-<?= fs::getCountryCode($event->awayTeamName, true); ?>"
                                     alt="<?= $event->awayTeamName; ?>"/>
                                <span data-value="<?= fs::t($event->awayTeamName); ?>"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-2 order-3 col-md-1 order-md-4">
                    <?php if ($event->status === "TIMED" || $event->prediction->joker) { ?>
                        <?php if ($event->status !== "TIMED") { ?>
                            <div class="chooseJoker"><i class="fas fa-hat-wizard"></i></div>
                        <?php } else { ?>
                            <a href="#" title="<?= fs::t("Set joker for this event"); ?>" class="chooseJoker"
                               data-warning="<?= fs::t("Joker already used in this matchday"); ?>">
                                <i class="fas fa-hat-wizard"></i>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="col-8 offset-2 order-4 col-md-3 offset-md-0 order-md-3 team inputs">
                    <div class="row">
                        <div class="col-6 col-md">
                            <input type="tel" id="homeTeam<?= $eventsID; ?>" name="homeTeam" autocomplete="off"
                                   class="score homeTeam" <?= isset($event->prediction->homeTeam) ? "value='{$event->prediction->homeTeam}'" : ""; ?>>
                            <h3 class="score homeTeam"><?= $event->result->homeTeam['DISP'] ?? 0; ?></h3>
                            <span class="score divider"></span>
                        </div>
                        <div class="col-6 col-md">
                            <input type="tel" id="awayTeam<?= $eventsID; ?>" name="awayTeam" autocomplete="off"
                                   class="score awayTeam" <?= isset($event->prediction->awayTeam) ? "value='{$event->prediction->awayTeam}'" : ""; ?>>
                            <h3 class="score awayTeam"><?= $event->result->awayTeam['DISP'] ?? 0; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 order-5 prediction<?= $event->prediction->bet ? " bet" : ""; ?><?= $event->prediction->success ? " success" : ""; ?>">
                    <div class="row">
                        <div class="col-5 offset-1 col-md-2 offset-md-8 text">
                            <?= fs::t("Prediction"); ?>:
                        </div>
                        <div class="col-3 offset-1 col-md-1 offset-md-0 col-md-centered">
                            <div class="row">
                                <div class="col-6 scoreHomeTeam"><?= $event->prediction->homeTeam ?? ""; ?></div>
                                <div class="pred divider"></div>
                                <div class="col-6 scoreAwayTeam"><?= $event->prediction->awayTeam ?? ""; ?></div>
                            </div>
                        </div>
                        <?php if ($event->status === "FINISHED" && $event->prediction->score !== NULL) { ?>
                        <div class="col-5 offset-1 col-md-2 offset-md-8 text">
                            <?= fs::t("Points"); ?>:
                        </div>
                        <div class="col-5 col-md-2 score" data-id="modalExplain<?= USER_ID . $eventsID; ?>">
                            <?= $event->prediction->score; ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </form>
        <?php if ($event->id === $lastEventID) { ?>
        </div>
    </div>
        <?php }
    }
} ?>
</section>

<?= $this->pagination('bottom'); ?>
