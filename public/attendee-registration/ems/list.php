<?php
	if(!empty($_GET['code']) && $_GET['code'] == 'askhuihwkhddskkadihibe') {
		include('config.php'); 
		
		$sql = "SELECT * FROM registration";
		$result = $conn->query($sql);
	
	?>
	<?php if($result){ ?>
		
		<!DOCTYPE html>
		<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Data</title>
				<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
			</head>
			<body>
				<div class="container mt-5">
					<h2> Data</h2>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>ID</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Email</th>
								<th>Phone</th>
								<th>Company</th>
								<th>Designation</th>
								<th>Vcard Link</th>
								<th>QR Code</th>
								<th>Created At</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if ($result->num_rows > 0) {
									// Output data of each row
									while($row = $result->fetch_assoc()) {
										echo "<tr>
										<td>" . $row["id"] . "</td>
										<td>" . $row["firstname"] . "</td>
										<td>" . $row["lastname"] . "</td>
										<td>" . $row["email"] . "</td>
										<td>" . $row["phone"] . "</td>
										<td>" . $row["company"] . "</td>
										<td>" . $row["designation"] . "</td>
										<td><a href='" . $row["vcard_link"] ."' target='_blank'>" . $row["vcard_link"] . "</a></td>
										<td><a href='qrcode/" . $row["qrcode_filename"] ."' target='_blank'>" . $row["qrcode_filename"] . "</a></td>
										<td>" . $row["created_at"] . "</td>
										</tr>";
									}
									} else {
									echo "<tr><td colspan='4'>No results found</td></tr>";
								}
							?>
						</tbody>
					</table>
				</div>
				<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
				<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
			</body>
		</html>
	<?php } ?>
	<?php
		// Close the database connection
		$conn->close();
	?>
	<?php } else { ?>
	<?php echo "Something went wrong. Please try again later."; ?>
<?php } ?>
