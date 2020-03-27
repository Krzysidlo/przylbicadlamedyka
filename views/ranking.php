<?php

use classes\Functions as fs;

$this->title = fs::t("Ranking");

$users       ??= [];
$groups      ??= [];
$curGroupsID ??= 1;
?>
<section class="container mainInfo">
    <div class="row">
        <div class="col-12">
            <h1><?= fs::t("Ranking") . " - " . fs::getCompName(); ?></h1>
        </div>
    </div>
</section>

<section class="container rank">
    <div class="row">
        <div class="col-6 col-md-2 mb-3">
            <label for="changeGroup"><?= fs::t("Group"); ?></label>
            <select class="browser-default custom-select" id="changeGroup">
                <?php foreach ($groups as $groupsID => $group) { ?>
                    <option value="<?= $groupsID ?>"<?= $curGroupsID == $groupsID ? " selected" : "" ?>><?= $group->name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <table class="table table-hover table-responsive-sm<?= DARK_THEME ? " table-dark" : ""; ?>">
        <thead>
        <tr>
            <th scope="col"><?= fs::t("Rank"); ?></th>
            <th scope="col"><?= fs::t("User"); ?></th>
            <th scope="col"><?= fs::t("Score"); ?></th>
            <th scope="col"><?= fs::t("Last five events"); ?></th>
            <th scope="col"><?= fs::t("Perfect score"); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $usersID => $user) {
            $userLogin = $usersID === USER_ID ? fs::t("You") : $user->name; ?>
            <tr>
                <td scope="row"><?= $user->ranking['position']; ?></td>
                <td><a href="/user/<?= $user->login; ?>"
                       class="preload"><?= $user->ranking['avatar']; ?> <?= $userLogin; ?></a></td>
                <td><?= $user->ranking['display_score']; ?></td>
                <td><?= $user->ranking['last_five']; ?></td>
                <td><?= $user->ranking['perfect']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</section>