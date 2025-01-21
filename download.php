<?php
error_reporting(0);
include ('functions.php');

if (is_user_logged_in()) {
    global $user;

    if (isadmin() || $user->getUsermanager() == 1 || $user->getUserrepository() == 1) {
        $files = array(
            'cus_emails' => HTTP_CUSTOMER_EMAILS
        );

        if (array_key_exists($_GET['opt'], $files)) {
            $file = $files[$_GET['opt']];
            header("Content-Description: File Transfer");
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . basename($file));

            readfile ($file);
            exit();
        }

    } else {
    	redirectToIndex();
    }
} else {
    redirectToIndex();
}