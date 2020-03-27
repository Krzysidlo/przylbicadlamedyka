<?php

use classes\Functions as fs;

$this->title = fs::t("Choose a competition");
?>

<section class="container events">
    <div class="row">
        <?php foreach ($competitions as $id => $comp) { ?>
            <form action="" method="post"
                  class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-0 col-lg-4 col-centered eventBox">
                <button type="submit" class="event preload">
                    <input type="hidden" name="competition" value="<?= $id; ?>">
                    <div class="col-12">
                        <div class="title"><h3><?= $comp['name']; ?></h3></div>
                    </div>
                    <div class="col-12">
                        <img src="<?= $comp['picture']; ?>" class="img-responsive" alt="">
                    </div>
                </button>
            </form>
        <?php } ?>
    </div>
</section>