<?php

use classes\User;

require_once __DIR__ . "/config/config.php";
global $pageClass;

$content = $pageClass->content();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <?= $pageClass->head(); ?>
</head>
<body class="<?= $pageClass->view; ?> no_scroll">
<div id="preloader">
    <div class="imgWrapper">
        <img src="<?= IMG_URL; ?>/logo.png">
    </div>
</div>
<?= $pageClass->menu(); ?>

<main data-page="<?= $pageClass->view; ?>">
    <?= $content; ?>
</main>

<?php if (USER_PRV === User::USER_PRODUCER) { ?>
    <div class="modal fade custom-modal functionModal" id="bascinetModal" tabindex="-1" role="dialog" aria-labelledby="bascinetLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="/ajax/map/newRequest" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bascinetLabel">Zgłoś gotowe przyłbice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bascinet">
                            Wybierz liczbę gotowych przyłbic do Odbioru
                        </label>
                        <input type="text" name="bascinet" id="bascinet" placeholder="Ilość" required
                               class="form-control"
                               title='Pole "Ilość" jest wymagane'>
                    </div>
                    <div class="form-group">
                        <label for="comments">
                            Jeżeli jest coś o czym powinien wiedzieć kierowca lub obsługa, opisz to poniżej
                        </label>
                        <textarea name="comments" id="comments" placeholder="Uwagi" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-red">Zapisz</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade custom-modal functionModal" id="materialModal" tabindex="-1" role="dialog" aria-labelledby="bascinetLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="/ajax/map/newRequest" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bascinetLabel">Zgłoś zapotrzebowanie</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="material">
                            Ile materiału potrzebujesz?
                        </label>
                        <input type="text" name="material" id="material" placeholder="Ilość" required
                               class="form-control"
                               title='Pole "Ilość" jest wymagane'>
                    </div>
                    <div class="form-group">
                        <label for="comments">
                            Jeżeli jest coś o czym powinien wiedzieć kierowca lub obsługa, opisz to poniżej
                        </label>
                        <textarea name="comments" id="comments" placeholder="Uwagi" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-red">Zapisz</button>
                </div>
            </form>
        </div>
    </div>
<?php } ?>

<?= $pageClass->foot(); ?>
</body>
</html>