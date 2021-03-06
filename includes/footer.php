<?php

?>

    <div id="loading" class="hidden">
        <div class="spin"></div>
    </div>

    <div class="toastContainer"></div>
    <footer>
        Strona stworzona przez <a href="https://kjaniszewski.pl" target="_blank">Krzysztof Janiszewski</a>
        2020
    </footer>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
          integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha256-YLGeXaapI0/5IgZopewRJcFXomhRMlYYjugPLSyNjTY=" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/css/mdb.min.css"
          integrity="sha256-VSBoWx3wZz4Z6YAMGq1toMWvJN3G1w7KH+b61PGwppA=" crossorigin="anonymous"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/dataTables.bootstrap4.css"
          integrity="sha256-WwAfhb7lVhl1iOpheVulhivZXFmNL6PlUjOCzRBWEl8=" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css"
          integrity="sha256-FdatTf20PQr/rWg+cAKfl6j4/IY3oohFAJ7gVC3M34E=" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
          integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
          crossorigin=""/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css"
          integrity="sha256-FAOaXTpl90/K8cXmSdsskbQN3nKYulhCpPbcFzGTWKI=" crossorigin="anonymous"/>

    <script type="text/javascript">
        const JS_URL = '<?= JS_URL; ?>',
            IMG_URL = '<?= IMG_URL; ?>';
    </script>

<?php if (LOGGED_IN) { ?>
    <script type="text/javascript">
        const USER_NAME = '<?= USER_NAME; ?>',
            USER_PRV = parseInt(<?= USER_PRV; ?>);
    </script>
<?php } ?>

<?php if (DEV_MODE) { ?>
    <link rel="stylesheet" href="<?= CSS_URL; ?>/styles.css">

    <script type="text/javascript" src="<?= JS_URL; ?>/index.js" defer></script>
<?php } else { ?>
    <link rel="stylesheet" class="theme" href="<?= CSS_URL; ?>/styles.min.css">

    <script type="text/javascript" src="<?= JS_URL; ?>/index.min.js" defer></script>
<?php }
