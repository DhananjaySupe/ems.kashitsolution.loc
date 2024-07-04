<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Register</title>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
		<style>
			.error {
            color: red;
			}
		</style>
	</head>
	<body>
		<div class="container mt-5">
			<div class="container">
				<br><br><br><br>
				<div class="row">
					<div class="col-md-12" style="padding-bottom:20px">
						<h2>
						<?php if(!empty($_GET['message'])) {
							echo $message = $_GET['message'];
						} ?>
						</h2>
					</div>
					<form role="form" name="playerForm" id="playerForm" action="save.php" method="post" accept-charset="utf-8" autocomplete="off"  enctype="multipart/form-data">
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputFirstName">First Name</label>
								<input type="text" class="form-control" id="exampleInputFirstName" placeholder="First Name" name="firstnameInput" required value="">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
								<label for="exampleInputLastName">Last Name</label>
								<input type="text" class="form-control" id="exampleInputLastName" placeholder="Last Name" name="lastnameInput" required value="">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputEmail">Email address</label>
								<input type="email" class="form-control" name="emailInput" id="exampleInputEmail" placeholder="Email" required value="">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputCompany">Company</label>
								<input type="text" class="form-control" id="exampleInputCompany" placeholder="Company" name="companyInput" required value="">
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputDesignation">Designation</label>
								<input type="text" class="form-control" id="exampleInputDesignation" placeholder="Designation" name="designationInput" required value="">
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputDesignation">Contact</label>
								<input type="number" class="form-control" id="exampleInputContact" placeholder="Contact" name="contactInput" required value="">
							</div>
						</div>
						
						<div class="col-md-12">
							<button type="submit" class="w-100 btn rounded-pill" data-loading="Loading...">Register</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		
	</body>
</html>
