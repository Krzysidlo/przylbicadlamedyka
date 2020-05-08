<?php
$magazines ??= [];
$hospitals ??= [];
?>
    <section class="container pins">
        <div class="row">
            <div class="col-12 mt-5">
                <h1 class="text-center mb-3">Witaj w panelu admina</h1>
            </div>
            <div class="col-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="magazines-tab" data-toggle="tab" href="#magazines" role="tab"
                           aria-controls="home"
                           aria-selected="true">Magazyny</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="hospitals-tab" data-toggle="tab" href="#hospitals" role="tab"
                           aria-controls="profile"
                           aria-selected="false">Szpitale</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active mt-4 row" id="magazines" role="tabpanel"
                         aria-labelledby="magazines-tab">
                        <table class="table magazines">
                            <thead>
                            <tr>
                                <th>Nazwa</th>
                                <th>Opis</th>
                                <th>Posiadane materiały</th>
                                <th>Akcja</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($magazines as $magazine) { ?>
                                <tr data-id="<?= $magazine->id; ?>">
                                    <td>
                                        <input type="text" class="form-control name" value="<?= $magazine->name; ?>"
                                               data-value="<?= $magazine->name; ?>" disabled>
                                    </td>
                                    <td>
                                        <textarea class="form-control description"
                                                  data-value="<?= $magazine->description; ?>"
                                                  disabled><?= $magazine->description; ?></textarea>
                                    </td>
                                    <td>
                                        <input type="number" step="50" class="form-control material"
                                               value="<?= $magazine->material; ?>"
                                               data-value="<?= $magazine->material; ?>" disabled>
                                    </td>
                                    <td class="action">
                                        <button class="btn btn-info edit">Edytuj</button>
                                        <button class="btn btn-warning cancel hidden">Anuluj</button>
                                        <button class="btn btn-success submit hidden">Zapisz</button>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade mt-4" id="hospitals" role="tabpanel" aria-labelledby="hospitals-tab">

                    </div>
                </div>
            </div>
        </div>
    </section>
    <pre>
<?php
//var_dump($magazines);
//var_dump($hospitals);
//die();