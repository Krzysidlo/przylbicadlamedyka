<?php use classes\User;

$frozen ??= [];

if (USER_PRV === User::USER_PRODUCER) { ?>
    <div class="modal fade custom-modal functionModal" id="bascinetModal" tabindex="-1" role="dialog"
         aria-labelledby="bascinetLabel" aria-hidden="true">
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
                               class="form-control" title='Pole "Ilość" jest wymagane'>
                    </div>
                    <div class="form-group">
                        <label for="bascinetComments">
                            Jeżeli jest coś o czym powinien wiedzieć kierowca lub obsługa, opisz to poniżej
                        </label>
                        <textarea name="comments" id="bascinetComments" placeholder="Uwagi"
                                  class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-red">Zapisz</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade custom-modal functionModal" id="materialModal" tabindex="-1" role="dialog"
         aria-labelledby="bascinetLabel" aria-hidden="true">
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
                            Ile materiału potrzebujesz? <span class="quantity">50</span>
                        </label>
                        <input type="range" name="material" id="material" class="custom-range" value="50" min="50"
                               max="500" step="50" required>
                    </div>
                    <div class="form-group">
                        <label for="materialComments">
                            Jeżeli jest coś o czym powinien wiedzieć kierowca lub obsługa, opisz to poniżej
                        </label>
                        <textarea name="comments" id="materialComments" placeholder="Uwagi"
                                  class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-red">Zapisz</button>
                </div>
            </form>
        </div>
    </div>
<?php }

if (USER_PRV === User::USER_DRIVER) { ?>
    <div class="modal fade custom-modal" id="driverModal" tabindex="-1" role="dialog"
         aria-labelledby="driverLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="driverLabel">Zgłoś zapotrzebowanie</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php foreach ($activities as $html) {
                        echo $html;
                    } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Anuluj</button>
                </div>
            </div>
        </div>
    </div>
<?php }
