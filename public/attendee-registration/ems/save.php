<?php
	
	require 'vendor/autoload.php';

	use Endroid\QrCode\Builder\Builder;
	use Endroid\QrCode\Encoding\Encoding;
	use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
	use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
	
	$message = '';
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		include('config.php'); 
		
		// Get form data
		$firstnameInput = $_POST['firstnameInput'];
		$lastnameInput = $_POST['lastnameInput'];
		$emailInput = $_POST['emailInput'];
		$companyInput = $_POST['companyInput'];
		$designationInput = $_POST['designationInput'];
		$contactInput = $_POST['contactInput'];
		
		// API call here
		
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
			"first_name": "'.$firstnameInput.'",
			"last_name": "'.$lastnameInput.'",
			"email": "'.$emailInput.'",
			"company": "'.$companyInput.'",
			"designation": "'.$designationInput.'",
			"contact": "'.$contactInput.'"
		}',
		));

		$response = curl_exec($curl);

		curl_close($curl);
		
		//$response = file_get_contents('data.json');
		$response = json_decode($response, true);

		$vcard_link =  isset($response['vcardlink']) ? $response['vcardlink'] : '-';
		//create qr code

			$result = Builder::create()
				->data($vcard_link)
				->encoding(new Encoding('UTF-8'))
				->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
				->size(300)
				->margin(10)
				->roundBlockSizeMode(new RoundBlockSizeModeMargin())
				->build();

			$qrcode_filename = bin2hex(random_bytes(9)).'.png';
			$result->saveToFile(__DIR__ . '/qrcode/' . $qrcode_filename);
			
		//create qr code

		// Prepare and bind
		$stmt = $conn->prepare("INSERT INTO registration (firstname, lastname, email, phone, company, designation, vcard_link, qrcode_filename, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
		
		$stmt->bind_param("sssssssss", $firstnameInput, $lastnameInput, $emailInput, $contactInput, $companyInput, $designationInput, $vcard_link, $qrcode_filename, date('Y-m-d H:i:s'));
		
		// Execute the query
		if ($stmt->execute()) {
			$message =  "New record created successfully";
			} else {
			$message =  "Error: " . $stmt->error;
		}
		
		// Close the statement and connection
		$stmt->close();
		$conn->close();
		
		header("Location: register.php?message=".$message);
	}
	header("Location: register.php?message=".$message);
	
?>
