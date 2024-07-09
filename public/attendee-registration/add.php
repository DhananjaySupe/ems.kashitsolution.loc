<!DOCTYPE html>
<?php include('menu-top.php') ?>
	<?php
		require 'vendor/autoload.php';
		
		use Endroid\QrCode\Builder\Builder;
		use Endroid\QrCode\Encoding\Encoding;
		use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
		use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
		

		$message = '';
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// Check if a file was uploaded without errors
			if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == 0) {
				$fileName = $_FILES["csvFile"]["name"];
				$fileTmpName = $_FILES["csvFile"]["tmp_name"];

				// Validate file extension
				$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
				if ($fileExtension != "csv") {
					$message = "Error: Only CSV files are allowed to be uploaded.";
					exit;
				} else {
					// Validate file size (max 1MB)
					$maxFileSize = 1 * 1024 * 1024; // 1MB in bytes
					if ($_FILES["csvFile"]["size"] > $maxFileSize) {
						$message = "Error: File size exceeds the limit of 1MB.";
						exit;
					} else {
						// Validate MIME type to ensure it's actually a CSV file
						$mimeTypes = array('text/csv', 'text/plain', 'application/csv', 'text/comma-separated-values', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext', 'application/octet-stream', 'application/txt');
						if (!in_array($_FILES["csvFile"]["type"], $mimeTypes)) {
							$message = "Error: Invalid file MIME type. Only CSV files are allowed.";
							exit;
						} else {
							// Move uploaded file to desired directory
							$uploadDir = "uploads/";
							$uploadPath = $uploadDir . $fileName;
							if (move_uploaded_file($fileTmpName, $uploadPath)) {
								// Now you can process the CSV file, e.g., read and display contents
								// Example: Read CSV file and display each row
								$file = fopen($uploadPath, "r");
								$rowNumber = 0;
								while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
									// Display each row
									if($rowNumber != 0){
										$post['firstnameInput'] = $data[0];
										$post['lastnameInput'] =  $data[1];
										$post['emailInput'] = $data[2];
										$post['companyInput'] = $data[3];
										$post['designationInput'] = $data[4];
										$post['contactInput'] = $data[5];
										$post['usernameInput'] = $data[6];
										$post['passwordInput'] = $data[7];
										
										createUser($post, $conn, (isset($_GET['user_id']) ? $_GET['user_id'] : 0));
										//$message = implode(", ", $data) . "<br>";
										$message = "Success: File Uploaded.";
									}
									$rowNumber++;
								}
								fclose($file);
							} else {
								$message = "Error uploading file.";
							}
						}
					}
				}
			} else {
				$message = createUser($_POST, $conn, (isset($_GET['user_id']) ? $_GET['user_id'] : 0));
			}
		}
		
		function createUser($post, $conn, $organizer_id) {
			$successmessage = $errormessage = $vcard_link = $qrcode_filename = $salt = '';
			$enabled = '1';
			$roles = 'a:1:{i:0;s:13:"ROLE_ATTENDEE";}';
			$validate = true;
			// Get form data
			$firstnameInput = $post['firstnameInput'];
			$lastnameInput = $post['lastnameInput'];
			$emailInput = $post['emailInput'];
			$companyInput = $post['companyInput'];
			$designationInput = $post['designationInput'];
			$contactInput = $post['contactInput'];
			$usernameInput = $post['usernameInput'];
			$passwordInput = $post['passwordInput'];
			if($validate && !$firstnameInput){
				$validate = false; $errormessage = 'first name should not be blank.'; 
			} else if($validate && !$lastnameInput){
				$validate = false; $errormessage = 'last name should not be blank.';
			} else if($validate && !$emailInput){
				$validate = false; $errormessage = 'email should not be blank.';
			} else if($validate && !$companyInput){
				$validate = false; $errormessage = 'company should not be blank.';
			} else if($validate && !$designationInput){
				$validate = false; $errormessage = 'designation should not be blank.';
			} else if($validate && !$usernameInput){
				$validate = false; $errormessage = 'username should not be blank.';
			} else if($validate && !$passwordInput){
				$validate = false; $errormessage = 'password should not be blank.';
			} else if($validate && strlen($contactInput) != 10){
				$validate = false; $errormessage = 'contact should not be blank and must be 10 digit.';
			}
			if($validate){
				$usernameInputcanonical = $emailInputcanonical = '';
				// Example SELECT query
				$sql = "SELECT id, username, email FROM eventic_user where username = '".$usernameInput."' OR email = '".$emailInput."'";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$errormessage = 'Duplicate Username or email.';
				} else {
					$usernameInputcanonical = strtolower($usernameInput);
					$emailInputcanonical = strtolower($emailInput);
					
					// API call here --------------------------------------------------------------
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
					
					$vcard_link =  isset($response['vcardlink']) ? $response['vcardlink'] : (isset($response['user']['vcard_link']) ? $response['user']['vcard_link'] : '-' );
					//create qr code Start
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
					//create qr code End
					
					//Whatsapp  --------------------------------------------------------------
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://api.dovesoft.io//REST/directApi/message',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
						"to": "91'.$contactInput.'",
						"type": "template",
						"template": {
							"language": {
								"policy": "deterministic",
								"code": "en"
							},
							"name": "bharatcard1",
							"components": [
								{
									"type": "body",
									"parameters": [
										{
											"type": "text",
											"text": "*!!Congratulation!!*"
										},
										{
											"type": "text",
											"text": '.$vcard_link.'
										},
										{
											"type": "text",
											"text": "NA"
										},
										{
											"type": "text",
											"text": "NA"
										},
										{
											"type": "text",
											"text": "Bharat Card"
										}
									]
								}
							]
						}
					}',
					CURLOPT_HTTPHEADER => array(
						'key: 5c89e49b82XX',
						'wabaNumber: 919363085550',
						'Content-Type: application/json'
					),
					));
					
					$response = curl_exec($curl);
					curl_close($curl);
					
					//Send Email --------------------------------------------------------------
					$subject = "Registration Done";
					$body = "Hello ".$firstnameInput.",
					
					Url: ".BASE_URL."
					Username: ".$usernameInput." 
					Password: ".$passwordInput." 
					";
					
					$headers = "From: info@kashitsolution.com";
					$headers .= "Reply-To: support@kashitsolution.com";
					$headers .= "Content-Type: text/plain; charset=UTF-8";
					
					// Attempt to send email
					mail($emailInput, $subject, $body, $headers);
					//-----------------------------------------------------------------------------------------

					$password = password_hash($passwordInput, PASSWORD_DEFAULT);

					// Prepare and bind
					$stmt = $conn->prepare("INSERT INTO eventic_user (username, username_canonical, email, email_canonical, enabled, salt, password, roles, firstname, lastname, slug, created_at, updated_at, by_organizer_id, vcard_link, qrcode, contact_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					
					$stmt->bind_param("sssssssssssssssss", $usernameInput, $usernameInputcanonical, $emailInput, $emailInputcanonical, $enabled, $salt, $password, $roles, $firstnameInput, $lastnameInput, $usernameInputcanonical, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $organizer_id, $vcard_link, $qrcode_filename, $contactInput);
					
					// Execute the query
					if ($stmt->execute()) {
						$successmessage =  "New record created successfully";
						} else {
						$errormessage =  "Error: " . $stmt->error;
					}
					
					// Close the statement and connection
					//$stmt->close();
					//$conn->close();
				}
			}
			return ($errormessage ? $errormessage : ($successmessage ? $successmessage : ''));
		}
	?>

	<section class="section-pagetop bg-gray">
		<div class="container clearfix">
			<h4 class="title-page dark b float-xl-left mb-0">Add Attendee</h4>
			<nav class="float-xl-right mt-2 mt-xl-0">
				<ol class="breadcrumb text-white">
					<li class="breadcrumb-item"><a href="/en" class="dark"><i class="fas fa-home"></i></a></li>
					<li class="breadcrumb-item"><a href="/en/dashboard" class="dark">Dashboard</a></li>
				</ol>
			</nav>
		</div>
	</section>
	<?php if($message) { ?>
		<section class="section-pagetop">
			<div class="container clearfix">
				<h4 class="title-page float-xl-left mb-0">
					<?= '!!!  '.$message ?></h4>
			</div>
		</section>
	<?php } ?>
	<section class="section-content padding-y bg-white">
		<div class="container">
			<div class="row">
			<?php include('navigation.php') ?>
				<div class="col-lg-9 mt-4 mt-lg-0">
				<div class="card box">
					<div class="card-body">
						<form name="point_of_sale" method="post" novalidate="novalidate" action="add.php?user_id=<?= (isset($_GET['user_id']) ? $_GET['user_id'] : 0) ?>" enctype="multipart/form-data">
							<label for="csvFile">Choose file:</label>
							<input type="file" id="csvFile" name="csvFile" accept=".csv">
							<button type="submit" id="point_of_sale_save" name="point_of_sale[save]" class="btn btn-primary btn">Upload</button>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="templates/templates.csv">Download Templates </a>
						</form>
					</div>
				</div>	
					<div class="card box">
						<div class="card-body">
							<form name="point_of_sale" method="post" novalidate="novalidate" action="add.php?user_id=<?= (isset($_GET['user_id']) ? $_GET['user_id'] : 0) ?>">
								<div id="point_of_sale">
									<div class="form-group">
										<label for="firstnameInput" class="required">First Name</label>
										<input type="text" id="firstnameInput" name="firstnameInput" required="required" class="form-control" value="Amit"/>
									</div>
									<div class="form-group">
										<label for="lastnameInput" class="required">Last Name</label>
										<input type="text" id="lastnameInput" name="lastnameInput" required="required" class="form-control" value="Patil"/>
									</div>
									<div class="form-group">
										<label for="emailInput" class="required">Email</label>
										<input type="email" id="emailInput" name="emailInput" required="required" class="form-control" value="amit@example.com"/>
									</div>
									<div class="form-group">
										<label for="companyInput" class="required">Company</label>
										<input type="text" id="companyInput" name="companyInput" required="required" class="form-control" value="NA"/>
									</div>
									<div class="form-group">
										<label for="designationInput" class="required">Designation</label>
										<input type="text" id="designationInput" name="designationInput" required="required" class="form-control" value="NA"/>
									</div>
									<div class="form-group">
										<label for="contactInput" class="required">Contact</label>
										<input type="number" id="contactInput" name="contactInput" required="required" class="form-control" value="0000000000"/>
									</div>
									<div class="form-group">
										<label for="usernameInput" class="required">Username</label>
										<input type="text" id="usernameInput" name="usernameInput" required="required" class="form-control" value="amit"/>
									</div>
									<div class="form-group">
										<label for="passwordInput" class="required">Password</label>
										<input type="password" id="passwordInput" name="passwordInput" required="required" class="form-control" value="123"/>
									</div>
									<div class="form-group">
										<button type="submit" id="point_of_sale_save" name="point_of_sale[save]" class="btn btn-primary btn">Save</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php include('menu-bottom.php') ?>		