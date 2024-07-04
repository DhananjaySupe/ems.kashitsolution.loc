<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.bharatcard.me',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "apikey": "GyT7p2KsEZXjtnQVLtnHaavOg4ud3j2V",
    "first_name": "Shashank",
    "last_name": "Patil",
    "email": "marshalmicro@gmail.com",
    "company": "KASH IT Solutions Ltd",
    "designation": "Director",
    "contact": "9921364242"
}',
));

$response = curl_exec($curl);

curl_close($curl);
$response;

echo '<pre>';
echo json_encode(json_decode($response), JSON_PRETTY_PRINT);
echo '</pre>';


//$properUrl = str_replace('\\', '', $url);