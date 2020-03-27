<?php

use classes\Functions as fs;

?>

<section class="container events">
    <div class="row">
        <?php foreach ($competitions as $id => $comp) { ?>
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-0 col-lg-4 col-centered eventBox">
                <a href="/admin/events/<?= $id; ?>" class="event">
                    <div class="col-12">
                        <div class="title"><h3><?= $comp['name']; ?></h3></div>
                    </div>
                    <div class="col-12">
                        <img src="<?= $comp['picture']; ?>" class="img-responsive" alt="">
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
</section>