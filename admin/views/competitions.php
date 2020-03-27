<section class="container events">
    <div class="row">
        <div class="col">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#competitionsList">Wyświetl listę turniejów</button>
            <button type="button" class="btn btn-secondary updateCompetitionsList">Zaktualizuj listę turniejów</button>
        </div>
    </div>
    <div class="row">
        <?php foreach ($competitions as $id => $comp) { ?>
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-0 col-lg-4 col-centered eventBox">
                <div class="event" data-id="<?= $id; ?>">
                    <div class="col-12">
                        <div class="title">
                            <input type="text" value="<?= $comp['name']; ?>" readonly>
                        </div>
                        <i class="fas fa-pencil-alt" data-name="name"></i>
                    </div>
                    <div class="col-12">
                        <form class="hidden changePicture">
                            <input type="file" accept="image/*" name="picture" class="hidden">
                        </form>
                        <img src="<?= $comp['picture']; ?>" class="img-responsive" alt="">
                        <i class="fas fa-pencil-alt" data-name="img"></i>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-0 col-lg-4 col-centered eventBox">
            <div class="event" data-name="new">
                <i class="fas fa-plus"></i>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="competitionsList" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header z-depth-1 deep-purple accent-4 white-text">
                <h5 class="modal-title text-center w-100" id="competitionsListLabel">Lista turniejów</h5>
                <button type="button" class="close white-text" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover table-responsive-sm table-bordered m-0">
                    <thead>
                    <tr>
                        <th scope="col">Nazwa</th>
                        <th scope="col">Od</th>
                        <th scope="col">Do</th>
                        <th scope="col">Feed ID</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($availableCompetitions as $feedID => $compInfo) { ?>
                        <tr>
                            <td scope="row"><?= $compInfo['name']; ?></td>
                            <td><?= $compInfo['start_date']; ?></td>
                            <td><?= $compInfo['end_date']; ?></td>
                            <td><?= $feedID; ?></td>
                        </tr>
                    <?php } ?>
        </tbody>
    </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newCompetition" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header z-depth-1 deep-purple accent-4 white-text">
                <h5 class="modal-title text-center w-100" id="newCompetitionLabel">Nowy turniej</h5>
                <button type="button" class="close white-text" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center mb-1">
                <form action="/admin/ajax/competition.php" method="post" id="newCompetitionForm">
                    <div class="md-form ml-0 mr-0">
                        <input type="text" name="name" id="name" class="form-control form-control-sm ml-0" required>
                        <label for="name" class="ml-0">Nazwa</label>
                    </div>
                    <div class="md-form ml-0 mr-0">
                        <input type="text" id="id" name="id" class="form-control form-control-sm ml-0" required>
                        <label for="id" class="ml-0">Feed ID</label>
                    </div>
                    <input type="file" accept="image/*" name="picture">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning mt-1" data-dismiss="modal">Anuluj</button>
                <button type="submit" class="btn btn-info mt-1" form="newCompetitionForm">Zapisz</button>
            </div>
        </div>
    </div>
</div>
