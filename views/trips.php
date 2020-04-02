<?php
?>
<section class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 mt-4 feed">
            <h4 class="title mt-3 mb-4">Planowane przejazdy</h4>
            <?php foreach ($activities as $html) {
                echo $html;
            } ?>
        </div>
    </div>
</section>
