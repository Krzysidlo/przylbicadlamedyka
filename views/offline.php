<?php

use classes\Functions as fs;

http_response_code(200);
?>
<section class="container-fluid errorInfo" style="background-image: url(<?= IMG_URL; ?>/offline.jpg);">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center">Wygląda na to, że nie masz dostępu do internetu</h2>
        </div>
    </div>
</section>