<?php
require_once(__DIR__ . "/cf_config.php");
require_once(__DIR__ . "/cf_helper.php");

$phone = empty($_POST['phone']) ? '' : $_POST['phone'];

if (empty($phone)) {
    $response = array(
        'message'   => "Номер телефону порожній",
        'success'   => false,
    );
} else {
    $verificationCode = mt_rand(100000, 999999);
    
    session_start();
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['callback_phone'] = $phone;
    
    $method = 'send';
    
    $params = '{
           "sender": "' .CF_SENDER. '",
           "recipients": [
              "' .$phone. '"
           ],
           "sms": {
              "text": "' .$verificationCode. '"
           }
      }';
    
    $result = turboSmsApiRequest(CF_TOKEN, $method, $params);
    
    if (!empty($result->response_result[0]->response_status) && $result->response_result[0]->response_status == 'OK') {
        $_SESSION['message_id'] = $result->response_result[0]->message_id;
        $response = array(
            'success'     => true,
        );        
    } else {
        $success = false;
        $message = $result->response_status;
        
        $response = array(
            'message'  => $result->response_status,
            'success'  => false,
        );           
    }
}

header('Content-Type: application/json');

echo json_encode($response);
