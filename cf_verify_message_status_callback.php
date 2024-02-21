<?php
require_once(__DIR__ . "/cf_config.php");

session_start();

if (empty($_SESSION['message_id'])) {
    
    $response = array(
        'success'   => false,            
        'message'   => "message_id не задано",
    );      
    
} else {
    
    $params = '{
           "messages": [
            "' .$_SESSION['message_id']. '"
           ]
      }';
    
    $result = turboSmsApiRequest(CF_TOKEN, 'status', $params);
    
    if (!empty($result->response_result[0]->response_status) && $result->response_result[0]->response_status == 'OK') {
        $search = [
            'Queued', 
            'Accepted', 
            'Sent', 
            'Delivered', 
            'Read', 
            'Expired',
            'Undelivered', 
            'Rejected', 
            'Unknown',
            'Failed', 
            'Cancelled'
        ];
        
        $replace = [
            'Взято в обробку',
            'Надіслано в мобільну мережу',
            'Надіслано',
            'Доставлено',
            'Прочитано',
            'Сплив термін відправки',
            'Не доставлено',
            'Відхилено',
            'Невідомий статус',
            'Неможливо відправити',
            'Відправка скасована'
        ];
        
        $stop_states = [
            'Expired', 
            'Undelivered', 
            'Rejected', 
            'Failed', 
            'Cancelled'
        ];
        

        $response = array(
            'success'   => 'true',
            'stop_check' => in_array($result->response_result[0]->status, $stop_states),
            'message'   => str_replace($search, $replace, $result->response_result[0]->status)
        );        
        
    } else {
        $response = array(
            'success'   => false,            
            'message'   => $result->response_status,
        );        
    }
}

header('Content-Type: application/json');

echo json_encode($response);
