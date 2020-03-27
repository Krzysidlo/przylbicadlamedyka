<?php

use classes\Functions as fs;

$return = [
    'success' => false,
    'message' => fs::t('There was an unexpected error'),
    'alert'   => 'danger',
];

$break = false;

if (empty($_POST['forgotPassword'])) {
    $break = true;
}
if (!$break && empty($_POST['femail'])) {
    $return['message'] = fs::t("Please provide an e-mail address");
    $return['alert']   = "warning";
    $break             = true;
}
if (!$break) {
    $to = filter_var($_POST['femail'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $return['message'] = fs::t("This e-mail address is incorrect");
        $return['alert']   = "warning";
        $break             = true;
    }
}

$user = fs::getUser($to);
if (!$break && !$user) {
    $return['message'] = fs::t("User with this e-mail address does not exist");
    $return['alert']   = "warning";
    $break             = true;
}

if ($user['type'] !== 'normal') {
    $return['message'] = fs::t("Account was created by Google or Facebook") . ". " . fs::t("You can not reset your password") . ".";
    $return['alert']   = "warning";
    $break             = true;
}

if (!$break) {
    $hash = md5($to . time() . rand(1000, 9999));
    if (!fs::setOption('reset-password', $hash, $to)) {
        $break = true;
    }
}

if (!$break) {
    $subject = PAGE_NAME . " - " . fs::t("Reset your password");

    $text = fs::t("To reset your password click this link or copy it to your web browser");
    $link = ROOT_URL . "/reset/" . $hash;

    // Message
    $message = <<< HTML
	<html>
	<head>
		<title>{$subject}</title>
	</head>
	<body>
		<p>{$text}</p>
		<a href="{$link}">{$link}</a>
	</body>
	</html>
HTML;

    $email = EMAIL;
    // To send HTML mail, the Content-type header must be set
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
    // Additional headers
    $headers[] = "To: {$to}";
    $headers[] = "From: " . PAGE_NAME . "<no-reply@cotyp.pl>";
    $headers[] = "Reply-To: {$email}";

    // Mail it
    if (mail($to, $subject, $message, implode("\r\n", $headers))) {
        $return = [
            'success' => true,
            'message' => fs::t('Message has been sent to your e-mail address'),
            'alert'   => 'success',
        ];
    }
}


if (empty($_GET['ajax'])) {
    header("Location: /register");
} else {
    echo json_encode($return);
}
exit(0);