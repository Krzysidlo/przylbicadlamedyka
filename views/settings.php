<?php

use classes\Functions as fs;

$this->title = fs::t("Settings");

?>
<section class="container mainInfo">
    <div class="row">
        <div class="col-12">
            <h1><?= fs::t("Settings"); ?></h1>
        </div>
    </div>
</section>

<section class="container form">
    <div class="row">
        <form action="/ajax/save_settings.php" method="post" class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <input type="hidden" name="darkTheme" value="false">
            <input type="hidden" name="gravatar" value="false">
            <input type="hidden" name="pushNotifications" value="false">
            <input type="hidden" name="parallax" value="false">
            <div class="form-group">
                <label class="row">
					<span class="col-8 text-input-label">
                        <?= fs::t("User name"); ?>
						<hr class="d-sm-none divider">
					</span>
                    <span class="col-4">
						<input type="text" name="changed_name" value="<?= USER_NAME; ?>" class="form-control">
					</span>
                </label>
            </div>
            <hr class="d-sm-none">
            <div class="form-group">
                <label class="row">
					<span class="col-12 col-xl-4 text-input-label">
                        <?= fs::t("User page address"); ?>
					</span>
                    <span class="col-12 col-xl-8 login-input">
                        <?= ROOT_URL . "/user/"; ?>
                        <input type="text" name="login" value="<?= $user->login; ?>">
					</span>
                </label>
            </div>
            <hr class="d-sm-none">
            <div class="switch primary-switch mb-3">
                <label class="row">
                    <span class="col-8">
                        <?= fs::t("Dark theme"); ?>
						<hr class="d-sm-none divider">
                    </span>
                    <span class="col-4">
                        <input type="checkbox" name="darkTheme" <?= $user->getOption('darkTheme') ? "checked" : ""; ?>>
                        <span class="lever"></span>
                    </span>
                </label>
            </div>
            <hr class="d-sm-none">
            <div class="switch primary-switch mb-3">
                <label class="row">
                    <span class="col-8">
                        <?= fs::t("PUSH notifications"); ?>
						<hr class="d-sm-none divider">
                    </span>
                    <span class="col-4">
                        <input type="checkbox"
                               name="pushNotifications" <?= $user->getOption('pushNotifications') ? "checked" : ""; ?>>
                        <span class="lever"></span>
                    </span>
                </label>
            </div>
            <hr class="d-sm-none">
            <div class="switch primary-switch mb-3">
                <label class="row">
                    <span class="col-8">
                        <?= fs::t("Parallax (Picture on events page)"); ?>
						<hr class="d-sm-none divider">
                    </span>
                    <span class="col-4">
                        <input type="checkbox" name="parallax" <?= $user->getOption('parallax') ? "checked" : ""; ?>>
                        <span class="lever"></span>
                    </span>
                </label>
            </div>
            <div class="md-form form-group<?= $user->getOption('parallax') ? "" : " hidden"; ?>">
                <i class="fa fa-image prefix"></i>
                <input type="text" class="form-control" name="parallaxUrl" id="parallaxUrl"
                       value="<?= $user->getOption('parallaxUrl') ?: ""; ?>">
                <label for="parallaxUrl"><?= fs::t("Parallax image URL"); ?></label>
            </div>
            <hr class="d-sm-none">
            <div class="switch primary-switch mb-3">
                <label class="row gravatar">
                    <span class="col-8">
                        <img src="<?= fs::getGravatar($user->email); ?>"
                             class="img-responsive img-circle img-settings" alt="gravatar">
						<span class="text"><?= fs::t("Use your gravatar"); ?></span>
						<hr class="d-sm-none divider">
                    </span>
                    <span class="col-4">
                        <input type="checkbox" name="gravatar" <?= $user->getOption('gravatar') ? "checked" : ""; ?>>
                        <span class="lever"></span>
                    </span>
                </label>
            </div>
            <div class="avatarParent"<?= $user->getOption('gravatar') ? " style='display:none;'" : ""; ?>>
                <div class="form-group">
                    <?= fs::t('Or'); ?>
                </div>
                <div class="form-group" data-drag="<?= fs::t("Drop image here"); ?>">
                    <input type="file" accept="image/*" name="avatar" id="avatar" class="hidden"
                           data-tmf="<?= fs::t("You can add only one picture as your profile image"); ?>"
                           data-default="<?= USR_URL; ?>/default.png">
                    <input type="hidden" name="save_avatar"
                           value="<?= $user->getOption('avatar') !== "default.png" ?: ""; ?>">
                    <label for="avatar">
                        <?= $avatar; ?>
                        <?= fs::t("Click here or drag an image"); ?>
                    </label>
                    <button type="button"
                            class="removePic close<?= $user->getOption('avatar') === "default.png" ? " hidden" : ""; ?>">
                        <span>&times;</span>
                    </button>
                </div>
            </div>
            <hr class="d-sm-none">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-secondary" id="changePassword">
                        <?= $user->pswdExists ? fs::t("Change password") : fs::t("Create password"); ?> <i
                                class="fas fa-key"></i>
                    </button>
                </div>
            </div>
            <hr class="d-sm-none">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#groupsModal">
                        <?= fs::t("My groups"); ?> <i class="fas fa-users"></i>
                    </button>
                </div>
            </div>
            <hr>
            <input type="submit" class="btn btn-primary" name="saveSettings" value="<?= fs::t("Save"); ?>">
        </form>
    </div>
</section>
<div class="modal fade" id="source" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="option capture col-6" data-text="Zrób zdjęcie kamerą"></div>
                    <div class="option upload col-6" data-text="Prześlij zdjęcie z dysku"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="camera" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <canvas class="snapshot hidden"></canvas>
                <div class="row">
                    <div class="box col-12">
                        <div class="picture-container">
                            <video class="camera_stream"></video>
                            <div class="overlay"></div>
                        </div>
                    </div>
                    <div class="box col-12 hidden">
                        <div class="picture-container">
                            <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt="new-picture"
                                 class="img-responsive preview">
                            <div class="overlay"></div>
                        </div>
                    </div>
                    <div class="buttonContainer col-12 pb-2">
                        <div class="btns float-left">
                            <button type="button" class="btn btn-warning ml-0"
                                    data-dismiss="modal"><?= fs::t("Cancel"); ?></button>
                        </div>
                        <div class="btns float-right">
                            <button type="button"
                                    class="btn btn-primary capture mr-0"><?= fs::t("Take a picture"); ?></button>
                            <button type="button"
                                    class="btn btn-white again mr-2"><?= fs::t("Take another picture"); ?></button>
                            <button type="button" class="btn btn-success save mr-0"><?= fs::t("Save"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog cascading-modal modal-avatar modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <img src="<?= $user->getAvatar(); ?>" alt="avatar" class="img-responsive img-circle img-login">
            </div>
            <div class="modal-body text-center mb-1">
                <h5 class="mt-1 mb-2"><?= $user->pswdExists ? fs::t("Change password") : fs::t("Create password"); ?></h5>
                <form action="/ajax/register/chgpswd" method="post">
                    <?php if ($user->pswdExists) { ?>
                        <div class="md-form ml-0 mr-0">
                            <input type="password" id="cpassword" name="cpassword"
                                   class="form-control form-control-sm validate ml-0" pattern=".{8,}"
                                   title="<?= fs::t("Minimum password length is 8"); ?>" required>
                            <label for="cpassword" class="ml-0">
                                <?= fs::t("Current password"); ?>
                            </label>
                        </div>
                    <?php } ?>
                    <div class="md-form ml-0 mr-0">
                        <input type="password" id="password" name="password"
                               class="form-control form-control-sm validate ml-0" pattern=".{8,}"
                               title="<?= fs::t("Minimum password length is 8"); ?>" required>
                        <label for="password" class="ml-0">
                            <?= fs::t("New password"); ?>
                        </label>
                    </div>
                    <div class="md-form ml-0 mr-0">
                        <input type="password" id="rpassword" name="rpassword"
                               class="form-control form-control-sm validate ml-0" pattern=".{8,}"
                               title="<?= fs::t("Minimum password length is 8"); ?>" required>
                        <label for="rpassword" class="ml-0">
                            <?= fs::t("Confirm new password"); ?>
                        </label>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-warning mt-1"
                                data-dismiss="modal"><?= fs::t("Cancel"); ?></button>
                        <button type="submit" name="changePassword" class="btn btn-info mt-1">
                            <?= fs::t("Send"); ?> <i class="fas fa-sign-in ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="groupsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-notify modal-info" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <p class="heading lead"><?= fs::t("My groups"); ?></p>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center mb-1">
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= fs::t("Group name"); ?></th>
                        <th><?= fs::t("Group code"); ?></th>
                        <th colspan="2"><?= fs::t("Action"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($groups as $id => $group) { ?>
                        <tr data-id="<?= $id; ?>">
                            <td><?= $group->name; ?></td>
                            <td><?= $group->code; ?></td>
                            <td><?php if ($id !== 1) { ?>
                                    <button class="btn btn-warning btn-sm leave"><?= fs::t("Leave"); ?></button><?php } ?>
                            </td>
                            <td><?php if ($group->createdBy == USER_ID && $id !== 1) { ?>
                                    <button class="btn btn-danger btn-sm delete"><i class="fas fa-trash"></i>
                                    </button><?php } ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="form">
                        <td>
                            <div class="md-form ml-0 mr-0">
                                <input type="text" id="groupName" class="form-control form-control-sm">
                                <label for="groupName" class="ml-0">
                                    <?= fs::t("Group name"); ?>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="md-form ml-0 mr-0">
                                <input type="text" id="groupCode" class="form-control form-control-sm">
                                <label for="groupCode" class="ml-0">
                                    <?= fs::t("Group code"); ?>
                                </label>
                            </div>
                        </td>
                        <td colspan="2">
                            <button class="btn btn-info btn-sm" id="createJoin" data-new="<?= fs::t("Create"); ?>"
                                    data-join="<?= fs::t("Join"); ?>">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn <?= DARK_THEME ? "btn-dark" : "btn-white"; ?>"
                        data-dismiss="modal"><?= fs::t('Close'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
