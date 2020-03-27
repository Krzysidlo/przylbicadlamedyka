<?php

use classes\Functions as fs;

$this->title = fs::t("Notifications");

list($new, $notifications) = fs::getNotifications();
?>

<section class="container">
    <?php foreach ($notifications as $notification) { ?>
        <div class="row">
            <?php if ($notification['href'] !== NULL) { ?>
            <a href="<?= $notification['href'] ?>" class="col-12 col-md-6 offset-md-3 col-xl-4 offset-xl-4 holder">
                <?php } else { ?>
                <div class="col-12 col-md-6 offset-md-3 col-xl-4 offset-xl-4 holder">
                    <?php } ?>
                    <div class="notification"><?= $notification['content']; ?></div>
                    <?php if ($notification['href'] === NULL) { ?>
                </div>
                <?php } else { ?>
            </a>
        <?php } ?>
        </div>
    <?php } ?>
</section>