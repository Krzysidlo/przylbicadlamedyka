<?php

use classes\Functions as fs;

$user = $users ?? [];
?>
<section class="container mainInfo">
    <div class="row">
        <div class="col-12">
            <h1>Privileges</h1>
        </div>
    </div>
</section>

<section class="container">
    <div class="row">
        <form action="/ajax/save_privileges.php" method="post" class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 setPrivileges">
            <div class="form-group">
                <label class="row">
                    <span class="col-4">
                        Użytkownik
                    </span>
                    <span class="col-8">
                        <select class="form-control" name="usersID" id="usersID">
                            <option value="" data-priv="-1"></option>
                            <?php foreach ($users as $usersID => $user) { ?>
                                <option value="<?= $usersID; ?>" data-priv="<?= $user->getPrivilege(); ?>"><?= $user->name . " (" . $user->email . ")"; ?> (<?= $user->getPrivilege(); ?>)</option>
                            <?php } ?>
                        </select>
                    </span>
                </label>
            </div>
            <div class="form-group">
                <label class="row">
                    <span class="col-4">
                        Uprawnienie
                    </span>
                    <span class="col-8">
                        <select class="form-control" name="level" id="level">
                            <option value=""></option>
                            <?php foreach (fs::$privilegeRoles as $level => $name) { ?>
                                <option value="<?= $level; ?>"><?= $level; ?> <?= $name; ?></option>
                            <?php } ?>
                        </select>
                    </span>
                </label>
            </div>
            <input type="submit" class="btn btn-primary" name="savePrivileges" value="Zapisz">
        </form>
    </div>
</section>