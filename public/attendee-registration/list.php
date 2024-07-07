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