<?php

use classes\Functions as fs;

$this->title = fs::t("Check for errors");
?>

    <section class="container mainInfo">
        <div class="row">
            <div class="col-12">
                <h1><?= fs::t("Check for errors"); ?></h1>
            </div>
        </div>
    </section>

<?php if (!empty($eventErrors)) { ?>
    <section class="container rank">
        <table class="table table-hover<?= DARK_THEME ? " table-dark" : ""; ?>">
            <thead>
            <tr>
                <th scope="col"><?= fs::t("Name"); ?></th>
                <th scope="col"><?= fs::t("User ID"); ?></th>
                <th scope="col"><?= fs::t("Match ID"); ?></th>
                <th scope="col"><?= fs::t("Match date"); ?></th>
                <th scope="col"><?= fs::t("Created date"); ?></th>
                <th scope="col"><?= fs::t("Updated date"); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($eventErrors as $error) { ?>
                <tr>
                    <?php foreach ($error as $value) { ?>
                        <td><?= $value; ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </section>
<?php }
if (!empty($cronErrors)) { ?>
    <section class="container rank">
        <table class="table table-hover<?= DARK_THEME ? " table-dark" : ""; ?>">
            <thead>
            <tr>
                <th scope="col"><?= fs::t("Name"); ?></th>
                <th scope="col"><?= fs::t("Value"); ?></th>
                <th scope="col"><?= fs::t("Time"); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($cronErrors as $error) { ?>
                <tr>
                    <?php foreach ($error as $value) { ?>
                        <td><?= $value; ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </section>
<?php }
if (empty($eventErrors) && empty($cronErrors)) { ?>
    <section class="container mainInfo">
        <div class="row">
            <div class="col-12">
                <h2><?= fs::t("There are no errors at that moment"); ?></h2>
            </div>
        </div>
    </section>
<?php }