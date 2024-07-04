<?php
	// Initialize cURL session
	$ch = curl_init();
	
	// Set cURL options
	curl_setopt($ch, CURLOPT_URL, 'https://backend.aisensy.com/campaign/t1/api/v2');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    "apiKey" => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY2NzA2MDkwYTJiZGUzMGMwZWFmMjJkZSIsIm5hbWUiOiJLQVNIIElUIFNvbHV0aW9ucyIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2NjcwNjA4ZmEyYmRlMzBjMGVhZjIyZDkiLCJhY3RpdmVQbGFuIjoiQkFTSUNfTU9OVEhMWSIsImlhdCI6MTcxODY0MDc4NH0.1KZ4bn1SQ7J5WwoNCe3rJNoPKCjvZMaFA8-Ij9C0fgQ",
    "campaignName" => "Event",
    "destination" => "",
    "userName" => "Dhananjay Supe",
    "templateParams" => [],
    "source" => "api",
    "media" => array(
	"url" => "https://kashitsolution.com/ems/qrcode/ddad2f0889a438202c.png",
	"filename" => "qr_code"
    ),
    "buttons" => [],
    "carouselCards" => [],
    "location" => array()
	)));
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
	));
	
	// Execute cURL session
	$response = curl_exec($ch);
	
	// Check for cURL errors
	if(curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	
	// Close cURL session
	curl_close($ch);
	
	// Display API response
	echo $response;
?>
