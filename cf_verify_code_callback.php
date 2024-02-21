<?php
require_once(__DIR__ . "/cf_config.php");
require_once(__DIR__ . "/cf_helper.php");

session_start();

if (empty($_POST['code'])) {
    $success = false;
    $message = 'Значення коду перевірки порожнє';
} else {
    
    $verification_code = $_POST['code'];
    
    if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] == $verification_code && !empty($_SESSION['callback_phone'])) {
            $success = true;
            $message = 'Перевірку успішно пройдено';
            
            $email_send_status = SendPhoneToEmail(CF_EMAIL_ADDRESS_TO, CF_EMAIL_ADDRESS_FROM, $phone, $_SESSION['callback_phone']);
            
            unset($_SESSION['verification_code']);
            unset($_SESSION['callback_phone']);
    } else {
         $success = false;
         $message = 'Перевірку не пройдено';
    }
}

header('Content-Type: application/json');

$response = array(
    'message'           => $message,
    'success'           => $success,
    'email_send_status' => $email_send_status
);

echo json_encode($response);