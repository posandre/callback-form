<?php
function turboSmsApiRequest($token, $method, $params) {
    $curl = curl_init();
    
    $args = [
      CURLOPT_URL               => "https://api.turbosms.ua/message/{$method}.json?token={$token}",
      CURLOPT_RETURNTRANSFER    => true,
      CURLOPT_ENCODING          => '',
      CURLOPT_MAXREDIRS         => 10,
      CURLOPT_TIMEOUT           => 0,
      CURLOPT_FOLLOWLOCATION    => true,
      CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST     => 'POST',
      CURLOPT_HTTPHEADER        => [
        'Content-Type: application/json'
      ],   
      CURLOPT_POSTFIELDS =>$params,
    ];
    
    curl_setopt_array($curl, $args);
    
    return json_decode(curl_exec($curl));    
}

function SendPhoneToEmail($email_to, $email_from, $phone) {
    $subject = "Callback form";
    $message = "<h2>Зворотний дзвінок на номер:</h2> <p><b>{$phone}</b></p>";
    
    $headers = "From: {$email_from}\r\n";
    $headers .= "Reply-To: {$email_from}\r\n";
    $headers .= "Content-Type: text/html\r\n";
    
    return mail($email_to, $subject, $message, $headers);
}