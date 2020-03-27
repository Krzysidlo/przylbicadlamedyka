<?php

use classes\Functions as fs;

$this->title = fs::t("Settings");
?>
<section class="container mainInfo">
    <div class="row">
        <div class="col-12">
            <h1><?= fs::t("Settings"); ?></h1>
        </div>
    </div>
</section>

<section class="container form">
    <div class="row">
        <form action="/admin/ajax/save_settings.php" method="post" class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <input type="hidden" name="page[CONST_MODE]" value="false">
            <h4 class="mb-4 text-center"><?= fs::t("Page settings"); ?></h4>
            <div class="switch primary-switch mb-3">
                <label class="row">
			    <span class="col-8">
					<?= fs::t("Construction mode"); ?>
					<hr class="d-sm-none divider">
				</span>
                    <span class="col-4">
				    <input type="checkbox"
                           name="page[CONST_MODE]" <?= fs::getOption('CONST_MODE', "page") ? "checked" : ""; ?>>
					<span class="lever"></span>
				</span>
                </label>
            </div>
            <hr class="d-sm-none">
            <div class="form-group">
                <label class="row">
            <span class="col-4 text-left text-input-label">
                <?= fs::t("E-mail address"); ?>
                <hr class="d-sm-none divider">
            </span>
                    <span class="col-8">
                <input type="email" name="page[EMAIL]" value="<?= EMAIL; ?>" class="form-control">
            </span>
                </label>
            </div>
            <hr class="d-sm-none">
            <input type="submit" class="btn btn-primary" name="saveSettings" value="<?= fs::t("Save"); ?>">
        </form>
    </div>
</section>

