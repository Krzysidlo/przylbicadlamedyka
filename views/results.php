<?php

use classes\Functions as fs;

$this->titile = fs::t("My results");

$results = $results ?? [];
$sum = (!empty($results) ? array_pop($results) : 0);
$userInfo = $userInfo ?? "";
?>
<section class="container events">
<?php if (empty($results)) { ?>
    <div class="row">
        <div class="col-12 mt-5">
            <h1 class="text-center mb-3"><?= fs::t("Currently there are no finished events in competition") . " " . fs::getCompName(); ?></h1>
            <h2 class="text-center"><?= fs::t("If you think this is an error please contact page administrator"); ?></h2>
        </div>
    </div>
<?php } else { ?>
    <div class="row">
        <div class="col-12 mt-5">
            <h1 class="text-center mb-3"><?= $userInfo; ?></h1>
        </div>
        <div class="col-12 mt-5">
            <table class="table table-hover table-responsive-sm<?= DARK_THEME ? " table-dark" : ""; ?>">
                <thead>
                <tr>
                    <th><?= fs::t("Date"); ?></th>
                    <th><?= fs::t("Match"); ?></th>
                    <th><?= fs::t("Result"); ?></th>
                    <th><?= fs::t("Prediction"); ?></th>
                    <th><?= fs::t("Points"); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $result) { ?>
                    <tr>
                        <td><?= $result->date; ?></td>
                        <td><?= $result->teams; ?></td>
                        <td><?= $result->result; ?></td>
                        <td><?= $result->prediction; ?></td>
                        <td><?= $result->score; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                    <td><?= $sum; ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php } ?>
</section>
