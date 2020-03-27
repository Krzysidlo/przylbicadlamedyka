<?php

use classes\User;
use classes\Functions as fs;

$this->title = fs::t("Log in");
?>

<section class="container">
    <?php if (!empty($message)) { ?>
    <div class="row">
        <div class="col-12 alert alert-<?= $alert; ?>"><?= $message; ?></div>
    </div>
    <?php } ?>
    <div class="row">
        <div class="col-12 col-md-6 offset-md-3">
            <h1 class="text-center">
                <img class="img-circle img-medium" src="<?= IMG_URL; ?>/logo.svg" alt="logo"> CoTyp
            </h1>
            <h4 class="mt-3 text-center">Zaloguj się za pomocą:</h4>
            <?php if (!empty($googleLoginURL)) { ?>
            <div class="row mt-4">
                <div class="col-12 col-md-8 offset-md-2">
                    <a href="<?= $googleLoginURL; ?>" role="button" class="btn btn-white google">
                        <img src="<?= IMG_URL; ?>/google.png" class="img-responsive" alt="logo_ggle"> Google
                    </a>
                </div>
            </div>
            <?php }
            if (!empty($facebookLoginURL)) { ?>
            <div class="row mt-4 mb-1">
                <div class="col-12 col-md-8 offset-md-2">
                    <a href="<?= htmlspecialchars($facebookLoginURL); ?>" role="button"
                       class="btn btn-indigo facebook">
                        <img src="<?= IMG_URL; ?>/facebook.png" class="img-responsive" alt="logo_fb"> Facebook
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12 col-md-6 offset-md-3 logRegForm">
            <ul class="nav nav-tabs" id="logRegTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#register" role="tab"
                       aria-controls="register"
                       aria-selected="true"><?= fs::t("Sign up"); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#login" role="tab"
                       aria-controls="login"
                       aria-selected="false"><?= fs::t("Log in"); ?></a>
                </li>
            </ul>
            <div class="tab-content row" id="tabs">
                <div class="tab-pane fade show active col-12" id="register" role="tabpanel"
                     aria-labelledby="register-tab">
                    <form action="/ajax/register/register" method="post">
                        <div class="form-group">
                            <label for="email"
                                   data-error="<?= fs::t("It doesn't look like correct e-mail address"); ?>">
<!--                                <i class="fa fa-envelope prefix"></i>-->
                                <?= fs::t("Email"); ?>
                            </label>
                            <input type="email" name="email" id="email" required
                                   class="form-control validate<?= !empty($invalid['email']) && $invalid['email'] ? " invalid" : ""; ?>"
                                   title="<?= fs::t("It doesn't look like correct e-mail address"); ?>"
                                   value="<?= !empty($this->get('email')) ? $this->get('email') : ""; ?>">
                        </div>
                        <div class="form-group">
                            <label for="name" data-error="<?= fs::t("Minimum name length is 3"); ?>">
<!--                                <i class="fa fa-user prefix"></i>-->
                                <?= fs::t("User name"); ?>
                            </label>
                            <input type="text" name="name" id="name" pattern=".{3,}" required
                                   class="form-control validate<?= !empty($invalid['name']) && $invalid['name'] ? " invalid" : ""; ?>"
                                   title="<?= fs::t("Minimum name length is 3"); ?>"
                                   value="<?= !empty($this->get('name')) ? $this->get('name') : ""; ?>">
                        </div>
                        <div class="form-group">
                            <label for="password" data-error="<?= fs::t("Minimum password length is 8"); ?>">
<!--                                <i class="fa fa-lock prefix"></i>-->
                                <?= fs::t("Password"); ?>
                            </label>
                            <input type="password" name="password" id="password" pattern=".{8,}" required
                                   class="form-control validate<?= !empty($invalid['password']) && $invalid['password'] ? " invalid" : ""; ?>"
                                   title="<?= fs::t("Minimum password length is 8"); ?>"
                                   value="<?= !empty($this->get('password')) ? $this->get('password') : ""; ?>">
                        </div>
                        <div class="form-group">
                            <label for="r-password" data-error="<?= fs::t("Minimum password length is 8"); ?>">
<!--                                <i class="fa fa-exclamation-triangle prefix"></i>-->
                                <?= fs::t("Confirm password"); ?>
                            </label>
                            <input type="password" name="r-password" id="r-password" pattern=".{8,}" required
                                   class="form-control validate<?= !empty($invalid['r-password']) && $invalid['r-password'] ? " invalid" : ""; ?>"
                                   title="<?= fs::t("Minimum password length is 8"); ?>"
                                   value="<?= !empty($this->get('r-password')) ? $this->get('r-password') : ""; ?>">
                        </div>
                        <div class="col-12 text-center">
                            <button class="btn btn-primary" type="submit"><?= fs::t("Sign up"); ?></button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade col-12" id="login" role="tabpanel" aria-labelledby="login-tab">
                    <form action="/ajax/register/login" method="post">
                        <div class="form-group">
                            <label for="lemail"
                                   data-error="<?= fs::t("It doesn't look like correct e-mail address"); ?>">
<!--                                <i class="fa fa-envelope prefix"></i>-->
                                <?= fs::t("Email"); ?>
                            </label>
                            <input type="email" name="lemail" id="lemail" required
                                   class="form-control validate<?= !empty($invalid['email']) && $invalid['email'] ? " invalid" : ""; ?>"
                                   title="<?= fs::t("It doesn't look like correct e-mail address"); ?>"
                                   value="<?= !empty($this->get('email')) ? $this->get('email') : ""; ?>">
                        </div>
                        <div class="form-group">
                            <label for="lpassword" data-error="<?= fs::t("Minimum password length is 8"); ?>">
<!--                                <i class="fa fa-lock prefix"></i>-->
                                <?= fs::t("Password"); ?>
                            </label>
                            <input type="password" name="lpassword" id="lpassword" pattern=".{8,}" required
                                   class="form-control validate<?= !empty($invalid['lpassword']) && $invalid['lpassword'] && !$easyLogIn ? " invalid" : ""; ?>"
                                   title="<?= fs::t("Minimum password length is 8"); ?>"
                                   value="<?= !empty($this->get('lpassword')) && !$easyLogIn ? $this->get('lpassword') : ""; ?>">
                        </div>
                        <div class="md-form pb-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="lremember"
                                       id="lremember" <?= !empty($_POST['lremember']) && !$easyLogIn ? "checked" : ""; ?>>
                                <label for="lremember" class="form-check-label"><?= fs::t("Keep me signed"); ?></label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="leasy"
                                       id="leasy" <?= !empty($this->get('lremember')) && !$easyLogIn ? "checked" : ""; ?>>
                                <label for="leasy" class="form-check-label"><?= fs::t("Easy log in"); ?></label>
                            </div>
                        </div>
                        <div class="md-form">
                            <?= fs::t("Forgot password"); ?>?
                            <a href="#" data-toggle="modal"
                               data-target="#forgotPassword"><?= fs::t("Click here"); ?></a>
                        </div>
                        <input type="hidden" name="submitLogin" value="true">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <?= fs::t("Log in"); ?> <i class="fas fa-sign-in-alt ml-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($easyLogIn) {
    try {
        $user      = new User($easyLogIn);
        $avatarURL = $user->getAvatar();
        $name      = $user->name; ?>
<div class="modal fade" id="easyLogin" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog cascading-modal modal-avatar modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <img src="<?= $avatarURL; ?>" alt="avatar" class="img-responsive img-circle img-login">
            </div>
            <div class="modal-body text-center mb-1">
                <h5 class="mt-1 mb-2"><?= $name; ?></h5>
                <?php if ($name !== $easyLogIn) { ?>
                <p class="mt-1 mb-2">(<?= $easyLogIn; ?>)</p>
                <?php } ?>
                <form action="/ajax/register/login" method="post">
                    <div class="md-form ml-0 mr-0">
                        <input type="hidden" name="lemail" value="<?= $easyLogIn; ?>">
                        <input type="hidden" name="leasy" value="on">
                        <input type="password" id="mpassword" name="lpassword" pattern=".{8,}" required
                               class="form-control form-control-sm validate ml-0<?= !empty($invalid['lpassword']) && $invalid['lpassword'] ? " invalid" : ""; ?>"
                               title="<?= fs::t("Minimum password length is 8"); ?>"
                               value="<?= !empty($this->get('lpassword')) ? $this->get('lpassword') : ""; ?>">
                        <label for="mpassword"<?= !empty($message) ? " data-error='" . $message . "'" : ""; ?>
                               class="ml-0">
                            <?= fs::t("Enter password"); ?>
                        </label>
                    </div>
                    <div class="md-form ml-0 mr-0 clearfix">
                        <input type="checkbox" class="form-check-input" name="lremember"
                               id="mremember" <?= !empty($this->get('lremember')) ? "checked" : ""; ?>>
                        <label for="mremember" class="form-check-label"><?= fs::t("Keep me signed"); ?></label>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" name="submitLogin" value="submitLogin"
                                class="btn btn-primary mt-1">
                            <?= fs::t("Sign in"); ?> <i class="fas fa-sign-in-alt ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <?php } catch (Exception $e) {
        fs::log("Error: " . $e->getMessage());
    }
} ?>

<div class="modal fade" id="forgotPassword" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-notify modal-info modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <p class="heading lead"><?= fs::t("Reset your password"); ?></p>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center mb-1">
                <form action="/ajax/reset_password.php" method="post">
                    <div class="md-form ml-0 mr-0">
                        <input type="email" id="femail" name="femail" class="form-control form-control-sm validate ml-0"
                               pattern=".{8,}" required>
                        <label for="femail" class="ml-0">
                            <?= fs::t("Enter e-mail"); ?>
                        </label>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" name="forgotPassword" class="btn btn-primary mt-1">
                            <?= fs::t("Send"); ?> <i class="fas fa-sign-in-alt ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
