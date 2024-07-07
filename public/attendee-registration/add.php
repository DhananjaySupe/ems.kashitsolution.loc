<!DOCTYPE html>
<?php include('config.php');  ?>
<?php
	
	require 'vendor/autoload.php';
	
	use Endroid\QrCode\Builder\Builder;
	use Endroid\QrCode\Encoding\Encoding;
	use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
	use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
	
	$successmessage = $errormessage = $organizer_id = $vcard_link = $qrcode_filename = $salt = '';
	$enabled = '1';
	$roles = 'a:1:{i:0;s:13:"ROLE_ATTENDEE";}';
	$validate = true;
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Get form data
		$firstnameInput = $_POST['firstnameInput'];
		$lastnameInput = $_POST['lastnameInput'];
		$emailInput = $_POST['emailInput'];
		$companyInput = $_POST['companyInput'];
		$designationInput = $_POST['designationInput'];
		$contactInput = $_POST['contactInput'];
		$usernameInput = $_POST['usernameInput'];
		$passwordInput = $_POST['passwordInput'];
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
		} else if($validate && count($contactInput) != 10){
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
				
				$vcard_link =  isset($response['vcardlink']) ? $response['vcardlink'] : '-';
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
				$stmt->close();
				$conn->close();
			}
		}
	}
?>
<?php include('menu-top.php') ?>
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
	<section class="section-content padding-y bg-white">
		<div class="container">
			<div class="row">
				<aside class="col-lg-3 pt-3 pt-lg-0">
					<div class="sticky-top sticky-sidebar dashboard-sidebar d-none d-lg-block d-xl-block">
						<ul id="dashboard-menu" class="nav nav-pills nav-pills-vertical-styled overflow-auto" style="max-height: 20rem;">
							<li class="nav-item">
								<a href="/en/dashboard" class="nav-link ">
									<i class="fas fa-tachometer-alt fa-fw"></i> Dashboard
								</a>
							</li>
							<li class="nav-item">
								<a href="/en/dashboard/organizer/profile" class="nav-link ">
									<i class="far fa-id-card fa-fw"></i> My organizer profile
								</a>
							</li>
							<li class="nav-item">
								<a href="/en/dashboard/organizer/my-events" class="nav-link ">
									<i class="fas fa-calendar fa-fw"></i> My events
								</a>
							</li>
							<li class="nav-item">
								<a href="/en/dashboard/organizer/my-venues" class="nav-link ">
									<i class="fas fa-map-marker-alt fa-fw"></i> My venues
								</a>
							</li>
							<li class="nav-item">
								<a href="#scanner-app-submenu-5f5de" data-toggle="collapse" data-parent="#dashboard-menu" class="nav-link dropdown-toggle">
									<i class="fas fa-qrcode fa-fw"></i> Scanner App
								</a>
								<ul id="scanner-app-submenu-5f5de" class="nav flex-column ml-3 collapse ">
									<li class="nav-item">
										<a href="/en/dashboard/organizer/my-scanners" class="nav-link ">
											My scanners
										</a>
									</li>
									<li class="nav-item">
										<a href="/en/dashboard/organizer/settings/scanner-app" class="nav-link ">
											Scanner App settings
										</a>
									</li>
								</ul>
							</li>
							<li class="nav-item">
								<a href="/en/dashboard/organizer/my-points-of-sale" class="nav-link">
									<i class="fas fa-print fa-fw"></i> My points of sale
								</a>
							</li>
							<li class="nav-item">
								<a href="/en/dashboard/organizer/reviews" class="nav-link ">
									<i class="fas fa-star fa-fw"></i> Reviews
								</a>
							</li>
							<li class="nav-item">
								<a href="#payout-submenu-5f5de" data-toggle="collapse" data-parent="#dashboard-menu" class="nav-link dropdown-toggle">
									<i class="fas fa-file-invoice-dollar fa-fw"></i> Payouts
								</a>
								<ul id="payout-submenu-5f5de" class="nav flex-column ml-3 collapse ">
									<li class="nav-item">
										<a href="/en/dashboard/organizer/my-payout-requests" class="nav-link ">
											Payout requests
										</a>
									</li>
									<li class="nav-item">
										<a href="/en/dashboard/organizer/settings/payouts" class="nav-link ">
											Payout methods
										</a>
									</li>
								</ul>
							</li>
							<li class="nav-item">
								<a href="/en/dashboard/organizer/reports" class="nav-link ">
									<i class="fas fa-funnel-dollar fa-fw"></i> Reports
								</a>
							</li>
							<li class="nav-item">
								<a href="#account-submenu-5f5de" data-toggle="collapse" data-parent="#dashboard-menu" class="nav-link dropdown-toggle">
									<i class="fas fa-user-cog fa-fw"></i> Account
								</a>
								<ul id="account-submenu-5f5de" class="nav flex-column ml-3 collapse ">
									<li class="nav-item">
										<a href="/en/dashboard/change-password" class="nav-link ">
											Change password
										</a>
									</li>
								</ul>
							</li>
							
							<li class="nav-item">
								<a href="#attendee-submenu-5f5de" data-toggle="collapse" data-parent="#dashboard-menu" class="nav-link dropdown-toggle">
									<i class="fas fa-user fa-fw"></i> My Attendee
								</a>
								<ul id="attendee-submenu-5f5de" class="nav flex-column ml-3 collapse ">
									<li class="nav-item">
										<a href="add.php" class="nav-link ">
											Add
										</a>
									</li>
									<li class="nav-item">
										<a href="list.php?code=askhuihwkhddskkadihibe" class="nav-link ">
											List
										</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</aside>
				<div class="col-lg-9 mt-4 mt-lg-0">
					<div class="card box">
						<div class="card-body">
							<form name="point_of_sale" method="post" novalidate="novalidate" action="add.php">
								<div id="point_of_sale">
									<div class="form-group">
										<label for="firstnameInput" class="required">First Name</label>
										<input type="text" id="firstnameInput" name="firstnameInput" required="required" class="form-control" />
									</div>
									<div class="form-group">
										<label for="lastnameInput" class="required">Last Name</label>
										<input type="text" id="lastnameInput" name="lastnameInput" required="required" class="form-control" />
									</div>
									<div class="form-group">
										<label for="emailInput" class="required">Email</label>
										<input type="email" id="emailInput" name="emailInput" required="required" class="form-control" />
									</div>
									<div class="form-group">
										<label for="companyInput" class="required">Company</label>
										<input type="text" id="companyInput" name="companyInput" required="required" class="form-control" />
									</div>
									<div class="form-group">
										<label for="designationInput" class="required">Designation</label>
										<input type="text" id="designationInput" name="designationInput" required="required" class="form-control" />
									</div>
									<div class="form-group">
										<label for="contactInput" class="required">Contact</label>
										<input type="number" id="contactInput" name="contactInput" required="required" class="form-control" />
									</div>
									<div class="form-group">
										<label for="usernameInput" class="required">Username</label>
										<input type="text" id="usernameInput" name="usernameInput" required="required" class="form-control" />
									</div>
									<div class="form-group">
										<label for="passwordInput" class="required">Password</label>
										<input type="password" id="passwordInput" name="passwordInput" required="required" class="form-control" />
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