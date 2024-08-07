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
						<a href="add.php?user_id=<?=isset($_GET['user_id']) ? $_GET['user_id'] : 0  ?>" class="nav-link ">
							Add
						</a>
					</li>
					<li class="nav-item">
						<a href="list.php?user_id=<?=isset($_GET['user_id']) ? $_GET['user_id'] : 0  ?>&code=askhuihwkhddskkadihibe" class="nav-link ">
							List
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</aside>