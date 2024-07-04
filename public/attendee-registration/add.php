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
				/*
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
				*/
				
				// Prepare and bind
				$stmt = $conn->prepare("INSERT INTO eventic_user (username, username_canonical, email, email_canonical, enabled, salt, password, roles, firstname, lastname, slug, created_at, updated_at, by_organizer_id, vcard_link, qrcode, contact_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				
				$stmt->bind_param("sssssssssssssssss", $usernameInput, $usernameInputcanonical, $emailInput, $emailInputcanonical, $enabled, $salt, $passwordInput, $roles, $firstnameInput, $lastnameInput, $usernameInputcanonical, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $organizer_id, $vcard_link, $qrcode_filename,$contactInput);
				
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
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="robots" content="index, follow, all" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Attendee Registration/</title>
        <link rel="stylesheet" href="/assets/app.263b044c.css">
        <link rel="stylesheet" href="/assets/app.orange.6536ed08.css">
	</head>
    <body class="bg-gray" data-currency-ccy="INR" data-currency-symbol="₹" data-currency-position="left" data-timezone="Asia/Kolkata" data-cookie-bar-page-link="/en/page/cookie-policy">
        <?php if($errormessage) { ?>
			<div class="ui-pnotify ui-pnotify-with-icon stack-bar-top ui-pnotify-in animated ui-pnotify-mobile-top ui-pnotify-mobile-able" aria-live="assertive" role="alertdialog" ui-pnotify="true" style="animation-duration: 0.25s; left: 0px; right: auto; top: 0px;">
				<div class="ui-pnotify-container alert alert-danger ui-pnotify-sharp ui-pnotify-shadow" role="alert" style="width: 100%; min-height: 16px;">
					<div class="ui-pnotify-icon ui-pnotify-icon-bs4">
						<span class="fas fa-exclamation-triangle"></span>
					</div>
					<h4 class="ui-pnotify-title ui-pnotify-title-bs4"></h4>
					<div class="ui-pnotify-text" role="alert"><?= $errormessage?></div>
				</div>
			</div>
		<?php } ?>
		<?php if($successmessage) { ?>
		<div class="ui-pnotify ui-pnotify-with-icon stack-bar-top ui-pnotify-in animated ui-pnotify-mobile-top ui-pnotify-mobile-able" aria-live="assertive" role="alertdialog" ui-pnotify="true" style="animation-duration: 0.25s; left: 0px; right: auto; top: 0px;">
				<div class="ui-pnotify-container alert alert-success ui-pnotify-sharp ui-pnotify-shadow" role="alert" style="width: 100%; min-height: 16px;">
					<div class="ui-pnotify-icon ui-pnotify-icon-bs4">
						<span class="fas fa-exclamation-triangle"></span>
					</div>
					<h4 class="ui-pnotify-title ui-pnotify-title-bs4"></h4>
					<div class="ui-pnotify-text" role="alert"><?= $successmessage?></div>
				</div>
			</div>
		<?php } ?>
		<section class="header-main sticky-top">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-6 col-lg-3 order-0 order-lg-0 header-logo-wrapper">
						<div class="brand-wrap">
							<a href="/en">
								<img class="logo img-fluid" src="/uploads/layout/667506592f302814630743.png" alt="Creative Exchange">
							</a>
						</div>
					</div>
					<div class="col-12 col-lg-5 order-2 order-lg-1 mt-3 mb-3 mt-lg-0 mb-lg-0 header-search-wrapper">
						<form action="/en/events" class="search-wrap">
							<div class="input-icon">
								<i class="fa fa-search"></i>
								<input name="keyword" class="form-control top-search" placeholder="Search for events" type="text">
								</div>
							</form>
							</div>
							<div class="col-6 col-lg-4 text-right order-1 order-lg-2 header-actions-wrapper">
						<div class="widgets-wrap d-flex justify-content-end">
							<div class="widget-header dropdown">
								<a href="#" class="ml-3 icontext" data-toggle="dropdown" onclick="openDashboardSideNav()">
									<span class="avatar" style="background: url('/uploads/organizers/user.png');">
									</span>
									<div class="text-wrap ">
										<span>organizer <i class="fas fa-caret-down"></i></span>
									</div>
								</a>
								<div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow header-user-dropdown-menu">
									<a class="dropdown-item" href="/en/dashboard">
										<i class="fas fa-tachometer-alt fa-fw"></i> Dashboard
									</a>
									<hr class="dropdown-divider">
									<a class="dropdown-item" href="/en/signout"><i class="fas fa-sign-out-alt fa-fw"></i> Sign out</a>
								</div>
							</div>
							<div class="widget-header d-lg-none">
								<button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#main_nav" aria-expanded="false" aria-label="Toggle navigation">
									<span class="icon-bar top-bar"></span>
									<span class="icon-bar middle-bar"></span>
									<span class="icon-bar bottom-bar"></span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<header class="section-header">
			<nav class="navbar navbar-expand-lg navbar-light bg-white ">
				<div class="container">
					<div class="collapse navbar-collapse" id="main_nav">
						<ul class="navbar-nav nav-fill w-100">
							<li class="nav-item ">
								<a class="nav-link" href="/en">
									<i class="fas fa-home fa-fw"></i> Home
								</a>
							</li>
							<li class="nav-item ">
								<a class="nav-link" href="/en/events">
									<i class="fas fa-calendar fa-fw"></i> Browse Events
								</a>
							</li>
							<li class="nav-item dropdown dropdown-hover  ">
								<a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-stream fa-fw"></i> Explore</a>
								<div class="dropdown-menu dropdown-menu-arrow">
									<a href="/en/events?category=concert-music" class="dropdown-item"><i class="fas fa-music fa-fw"></i> Concert / Music</a>
									<a href="/en/events?category=trip-camp" class="dropdown-item"><i class="fas fa-campground fa-fw"></i> Trip / Camp</a>
									<a href="/en/events?category=sport-fitness-1" class="dropdown-item"><i class="fas fa-futbol fa-fw"></i> Sport / Fitness</a>
									<a href="/en/events?category=museum-monument" class="dropdown-item"><i class="fas fa-landmark fa-fw"></i> Museum / Monument</a>
									<a href="/en/events?category=recreation-park-attraction" class="dropdown-item"><i class="fas fa-rocket fa-fw"></i> Recreation park / Attraction</a>
									<a href="/en/events?category=theater" class="dropdown-item"><i class="fas fa-theater-masks fa-fw"></i> Theater</a>
									<a href="/en/events?category=restaurant-gastronomy" class="dropdown-item"><i class="fas fa-utensils fa-fw"></i> Restaurant / Gastronomy</a>
									<a href="/en/events?category=festival-spectacle" class="dropdown-item"><i class="fab fa-napster fa-fw"></i> Festival / Spectacle</a>
									<a href="/en/events?category=workshop-training" class="dropdown-item"><i class="fas fa-chalkboard-teacher fa-fw"></i> Workshop / Training</a>
									<a href="/en/categories" class="dropdown-item"><i class="fas fa-folder-open fa-fw"></i> All categories</a>
								</div>
							</li>
							<li class="nav-item ">
								<a class="nav-link" href="/en/venues">
									<i class="fas fa-compass fa-fw"></i> Venues
								</a>
							</li>
							<li class="nav-item ">
								<a class="nav-link" href="/en/help-center">
									<i class="fas fa-question-circle fa-fw"></i> How It works?
								</a>
							</li>
							<li class="nav-item ">
								<a class="nav-link" href="/en/blog">
									<i class="fas fa-newspaper fa-fw"></i> Blog
								</a>
							</li>
							<li class="nav-item ">
								<a class="nav-link" href="/en/dashboard/attendee/my-tickets">
									<i class="fas fa-ticket-alt fa-fw"></i> My tickets
								</a>
							</li>
							<li class="nav-item ">
								<a class="nav-link" href="/en/dashboard/organizer/my-events/add">
									<i class="fas fa-calendar-plus fa-fw"></i> Add my event
								</a>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</header>
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
		<footer class="section-footer border-top-gray">
			<div class="container">
				<section class="footer-top padding-top">
					<div class="row">
						<aside class="col-sm-6 col-lg-3">
							<h5 class="title text-dark">Useful Links</h5>
							<ul class="list-unstyled">
								<li class="mb-1">
									<a href="/en/page/about-us">
										About us
									</a>
								</li>
								<li class="mb-1">
									<a href="/en/help-center">
										Help center
									</a>
								</li>
								<li class="mb-1">
									<a href="/en/blog">
										Blog
									</a>
								</li>
								<li class="mb-1">
									<a href="/en/venues">
										Venues
									</a>
								</li>
								<li class="mb-1">
									<a href="/en/contact">
										Send us an email
									</a>
								</li>
							</ul>
						</aside>
						<aside class="col-sm-6 col-lg-3">
							<h5 class="title text-dark">My Account</h5>
							<ul class="list-unstyled">
								<li class="mb-1">
									<a href="/en/signup/attendee">
										Create an account
									</a>
								</li>
								<li class="mb-1">
									<a href="/en/signup/organizer">
										Sell tickets online
									</a>
								</li>
								<li class="mb-1">
									<a href="/en/dashboard/attendee/my-tickets">
										My tickets
									</a>
								</li>
								<li class="mb-1">
									<a href="/en/resetting/request">
										Forgot your password ?
									</a>
								</li>
								<li class="mb-1">
									<a href="/en/page/pricing-and-fees">
										Pricing and fees
									</a>
								</li>
							</ul>
						</aside>
						<aside class="col-sm-6 col-lg-3">
							<h5 class="title text-dark">Event Categories</h5>
							<ul class="list-unstyled">
								<li class="mb-1"><a href="/en/events?category=concert-music">Concert / Music</a></li>
								<li class="mb-1"><a href="/en/events?category=trip-camp">Trip / Camp</a></li>
								<li class="mb-1"><a href="/en/events?category=sport-fitness-1">Sport / Fitness</a></li>
								<li class="mb-1"><a href="/en/events?category=museum-monument">Museum / Monument</a></li>
								<li class="mb-1">
									<a href="/en/categories">
										All categories
									</a>
								</li>
							</ul>
						</aside>
						<aside class="col-sm-6 col-lg-3">
							<article>
								<h5 class="title text-dark">Contact Us</h5>
								
								<div class="btn-group white">
									<a class="btn btn-facebook" title="Facebook" target="_blank" href="https://business.facebook.com"><i class="fab fa-facebook-f fa-fw"></i></a>
									<a class="btn btn-instagram" title="Instagram" target="_blank" href="https://www.instagram.com"><i class="fab fa-instagram fa-fw"></i></a>
									<a class="btn btn-youtube" title="Youtube" target="_blank" href="https://www.youtube.com"><i class="fab fa-youtube fa-fw"></i></a>
									<a class="btn btn-twitter" title="Twitter" target="_blank" href="https://www.twitter.com"><i class="fab fa-twitter fa-fw"></i></a>
								</div>
								<div class="clearfix"></div>
							</article>
						</aside>
					</div>
					<br>
				</section>
				<section class="footer-bottom row">
					<div class="col-sm-12">
						<p class="text-center text-dark">
							<a href="/en/page/terms-of-service" class="text-dark">Terms of service</a>
							<span class="text-gray">|</span>
							<a href="/en/page/privacy-policy" class="text-dark">Privacy policy</a>
							<span class="text-gray">|</span>
							<a href="/en/page/cookie-policy" class="text-dark">Cookie policy</a>
							<span class="text-gray">|</span>
							<a href="/en/page/gdpr-compliance" class="text-dark">GDPR compliance</a>
						</p>
					</div>
					<div class="col-sm-12">
						<p class="text-dark-50 text-center">
							Copyright &copy; 2024
						</p>
					</div>
				</section>
			</div>
		</footer>
		<div id="dashboard-sidenav" class="dashboard-sidenav d-lg-none d-xl-none">
			<a href="javascript:void(0)" class="dashboard-sidenav-close" onclick="closeDashboardSideNav()">&times;</a>
			<span class="dashboard-sidenav-username"><i class="far fa-user"></i> PCF</span>
			<ul class="nav nav-pills nav-pills-vertical-styled overflow-auto">
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
					<a href="#scanner-app-submenu-3685a" data-toggle="collapse" data-parent="#dashboard-menu" class="nav-link dropdown-toggle">
						<i class="fas fa-qrcode fa-fw"></i> Scanner App
					</a>
					<ul id="scanner-app-submenu-3685a" class="nav flex-column ml-3 collapse ">
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
					<a href="#payout-submenu-3685a" data-toggle="collapse" data-parent="#dashboard-menu" class="nav-link dropdown-toggle">
						<i class="fas fa-file-invoice-dollar fa-fw"></i> Payouts
					</a>
					<ul id="payout-submenu-3685a" class="nav flex-column ml-3 collapse ">
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
					<a href="#account-submenu-3685a" data-toggle="collapse" data-parent="#dashboard-menu" class="nav-link dropdown-toggle">
						<i class="fas fa-user-cog fa-fw"></i> Account
					</a>
					<ul id="account-submenu-3685a" class="nav flex-column ml-3 collapse ">
						<li class="nav-item">
							<a href="/en/dashboard/change-password" class="nav-link ">
								Change password
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item">
					<a href="/en/signout" class="nav-link">
						<i class="fas fa-sign-out-alt fa-fw"></i> Sign out
					</a>
				</li>
			</ul>
		</div>
		<script src="/assets/runtime.5b7a9943.js"></script>
		<script src="/assets/0.01fae393.js"></script>
		<script src="/assets/1.39211bc7.js"></script>
		<script src="/assets/app.9d5bde40.js"></script>
		
		<a class="material-scrolltop cursor-pointer btn btn-sm btn-primary"></a>
		<div id="sfwdt15385f" class="sf-toolbar sf-display-none"></div>
	</body>
</html>