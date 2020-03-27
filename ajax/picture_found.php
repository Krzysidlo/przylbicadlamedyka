<?php

use classes\User;
use classes\Functions as fs;

if (empty($_GET['ajax'])) {
    header("Location: /");
} else {
    $return = [
        'success' => false,
        'modal'   => false,
        'modalId' => false,
    ];

    $user = new User;

    $picturesFound = $user->getOption('picturesFound');
    if (!empty($_POST['number'])) {
        if (!in_array($_POST['number'], $picturesFound)) {
            $picturesFound[] = $_POST['number'];
        }

        $user->setOption('picturesFound', $picturesFound);
    }

    $congrats = fs::t("Congratulations");
    $close    = fs::t('Close');

    $text1 = $text2 = $text3 = $text4 = "";

    if (count($picturesFound) == 4) {
        if (!$user->getOption('foundAll')) {
            if ($user->setOption('foundAll', true)) {
                $modalId  = "foundAll";
                $btnClass = (DARK_THEME ? "btn-dark" : "btn-white");
                $text3    = fs::t("You have found all of the hidden pictures") . ".";
                $text4    = fs::t("Or not") . "...";

                $return = [
                    'success' => 'foundAll',
                ];
            }
        }
    }

    if ($return['success']) {
        $modal = <<< HTML
			<div class="modal fade" id="{$modalId}" tabindex="-1" role="dialog"
			     aria-labelledby="{$modalId}Label" aria-hidden="true">
			    <div class="modal-dialog" role="document">
			        <div class="modal-content">
			            <div class="modal-header">
			                <h5 class="modal-title" id="{$modalId}Label">{$congrats}!</h5>
			                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			                    <span aria-hidden="true">&times;</span>
			                </button>
			            </div>
			            <div class="modal-body">
			                <h6>{$text1}</h6>
			                <h6>{$text2}</h6>
			                <h6>{$text3}</h6>
			                <h6>{$text4}</h6>
			            </div>
			            <div class="modal-footer">
			                <button type="button" class="btn {$btnClass}" data-dismiss="modal">
			                    {$close}
			                </button>
			            </div>
			        </div>
			    </div>
			</div>
HTML;

        $return['modal']   = $modal;
        $return['modalId'] = $modalId;
    }

    echo json_encode($return);
}

exit(0);