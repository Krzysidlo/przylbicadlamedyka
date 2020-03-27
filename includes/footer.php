<?php

use classes\Functions as fs;

?>
    <div class="modal fade" id="cheatingModal" tabindex="-1" role="dialog"
         aria-labelledby="cheatingModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="cheatingModalLabel"><?= fs::t("You sneaky bastard"); ?>!</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5><?= fs::t("That is not a way to find a hidden picture"); ?>.</h5>
                    <h5><?= fs::t("Don't ever do that again"); ?>!</h5>
                    <img class="img-responsive" src="<?= IMG_URL; ?>/blank.gif" alt="sneaky image"
                         data-src="<?= SRP_URL; ?>/sneaky" data-ext=".jpg">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn <?= DARK_THEME ? "btn-dark" : "btn-white"; ?>"
                            data-dismiss="modal"><?= fs::t('Close'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php
if (LOGGED_IN) { ?>

    <div class="modal fade" id="surpriseModal" tabindex="-1" role="dialog"
         aria-labelledby="surpriseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="surpriseModalLabel"><?= fs::t("Congratulations"); ?>!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><?= fs::t("You have found a hidden picture") . ". " . fs::t("Yes, there are more"); ?>.</p>
                    <p><?= fs::t("Don't tell anyone about that") . ". " . fs::t("Let them find it by their own"); ?>
                        .</p>
                    <img class="img-responsive" src="<?= IMG_URL; ?>/blank.gif" alt="hidden object"
                         data-srp="<?= SRP_URL; ?>/surprise" data-ext=".jpg">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn <?= DARK_THEME ? "btn-dark" : "btn-white"; ?>"
                            data-dismiss="modal"><?= fs::t('Close'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

    <div id="loading" class="hidden">
        <div class="spin"></div>
    </div>

    <div class="toastContainer"></div>
    <footer>
        <?= fs::t("Website created by"); ?> <a href="https://kjaniszewski.pl" target="_blank">Krzysztof Janiszewski</a> 2020
    </footer>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
          integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha256-YLGeXaapI0/5IgZopewRJcFXomhRMlYYjugPLSyNjTY=" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/css/mdb.min.css"
          integrity="sha256-VSBoWx3wZz4Z6YAMGq1toMWvJN3G1w7KH+b61PGwppA=" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/dataTables.bootstrap4.css"
          integrity="sha256-WwAfhb7lVhl1iOpheVulhivZXFmNL6PlUjOCzRBWEl8=" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css"
          integrity="sha256-FdatTf20PQr/rWg+cAKfl6j4/IY3oohFAJ7gVC3M34E=" crossorigin="anonymous" />

    <script type="text/javascript">
        var JS_URL = '<?= JS_URL; ?>',
            IMG_URL = '<?= IMG_URL; ?>',
            MSC_URL = '<?= MSC_URL; ?>';
    </script>

<?php if (LOGGED_IN) { ?>
    <script type="text/javascript">
        var USER_NAME = '<?= USER_NAME; ?>',
            USER_PRV = '<?= USER_PRV; ?>';
    </script>
<?php } ?>

<?php if (DEV_MODE) { ?>
    <link rel="stylesheet" class="theme" href="<?= DARK_THEME ? CSS_URL . "/dark.css" : CSS_URL . "/light.css"; ?>"
          data-dark="<?= CSS_URL . "/dark.css"; ?>" data-light="<?= CSS_URL . "/light.css"; ?>">

    <script type="text/javascript" src="<?= JS_URL; ?>/index.js" defer></script>
<?php } else { ?>
    <link rel="stylesheet" class="theme"
          href="<?= DARK_THEME ? CSS_URL . "/dark.min.css" : CSS_URL . "/light.min.css"; ?>"
          data-dark="<?= CSS_URL . "/dark.min.css"; ?>" data-light="<?= CSS_URL . "/light.min.css"; ?>">

    <script type="text/javascript" src="<?= JS_URL; ?>/index.min.js" defer></script>
<?php } ?>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/pl_PL/sdk.js#xfbml=1&version=v6.0&appId=211253152858745&autoLogAppEvents=1"></script>
<?php if (LOGGED_IN && fs::getOption('greyscale')) { ?>
    <style>
        html {
            filter: gray;
            filter: grayscale(100%);
            -moz-filter: grayscale(100%);
            -webkit-filter: grayscale(100%);
        }
    </style>
<?php }
