<?php
	$organizer_id = 0;
	if(!empty($_GET['code']) && $_GET['code'] == 'askhuihwkhddskkadihibe') {
		include('config.php'); 
		
		$sql = "SELECT * FROM eventic_user where by_organizer_id = '".$organizer_id."'";
		$result = $conn->query($sql);
		
	?>
	<?php if($result){ ?>
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
						<?php include('navigation.php') ?>
						<div class="col-lg-9 mt-4 mt-lg-0">
							<div class="row">
								<div class="col-12">
									<div class="card">
										<div class="table-responsive">
											<table class="table table-hover table-vcenter text-nowrap">
												<thead>
													<tr>
														<th>Name / Username</th>
														<th>Email / Phone</th>
														<th>Vcard Link</th>
														<th>QR Code</th>
													</tr>
												</thead>
												<tbody>
													<?php if ($result->num_rows > 0) { ?>
														<?php while($row = $result->fetch_assoc()) { ?>
															<tr>
																<td>
																	<small><?= $row["firstname"].' '.$row["lastname"] ?> / <?= $row["username"] ?></small>
																</td>
																<td>
																	<small><?= $row["email"] ?></small>
																	<br />
																	<small><?= $row["contact_no"] ?></small>
																</td>
																<td>
																	<small><?= $row["vcard_link"] ?></small>
																</td>
																<td>
																	<small><?= $row["qrcode"] ?></small>
																</td>
															</tr>
															
															<?php }
															} else {
															echo "<tr><td colspan='4'>No results found</td></tr>";
														} ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		<?php include('menu-bottom.php') ?>		
	<?php } ?>
<?php } ?>	