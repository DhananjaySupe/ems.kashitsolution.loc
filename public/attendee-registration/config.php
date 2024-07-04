<?php error_reporting(0); ?>
<?php
	##Run This Query
	##ALTER TABLE `eventic_user` ADD `by_organizer_id` INT NULL AFTER `facebook_profile_picture`, ADD `vcard_link` VARCHAR(100) NULL AFTER `by_organizer_id`, ADD `qrcode` VARCHAR(100) NULL AFTER `vcard_link`;
	##ALTER TABLE `eventic_user` ADD `contact_no` VARCHAR(15) NULL AFTER `qrcode`;
	
	
	define("BASE_URL", "http://ems.kashitsolution.loc/", true);
	
	// Database connection details
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "eventmanagementsystem";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
?>
