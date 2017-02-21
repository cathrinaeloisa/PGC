<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	$userID = $_SESSION['userID'];
	$userName = $_SESSION['userName'];

	$accountDetails = "SELECT * FROM userAccounts NATURAL JOIN usertypes WHERE userID = '{$userID}'";
?>

<html>
	<head>
		<title>Account Details</title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">

		<script src="CSS/jquery.min.js"></script>
		<script src="CSS/bootstrap.min.js"></script>
		<link rel="stylesheet" href="CSS/bootstrap.min.css">

	</head>
	
	<body>
		<div class="pure-g">
			<!-- SIDEBAR -->
			<div class="pure-u-5-24 sidebar-container">
				<div align="center">
					<div class="logo-container" align="center">
						<div>
							<img class="logo-edit" 	src="pentagon_png.png">
						</div>
					</div>
				</div>
				
				<div class="sidebar-elements">
					<ul>
						<!-- HOME LINK -->
						<?php
							if ($userType == 101) {
								echo '<li>
										<a href="administrative-manager-home.php" class="pure-menu-link"> Home </a>
									</li>';
							}
							else if ($userType == 102) {
								echo '<li>
										<a href="sales-and-marketing-home.php" class="pure-menu-link"> Home </a>
									</li>';
							}
							else if ($userType == 103) {
								echo '<li>
										<a href="billing-clerk-home.php" class="pure-menu-link"> Home </a>
									</li>';
							}
							else if ($userType == 104) {
								echo '<li>
										<a href="cylinder-control-home.php" class="pure-menu-link"> Home </a>
									</li>';
							}
							else if ($userType == 105) {
								echo '<li>
										<a href="dispatcher-home.php" class="pure-menu-link"> Home </a>
									</li>';
							}
							else if ($userType == 106) {
								echo '<li>
										<a href="production-manager-home.php" class="pure-menu-link"> Home </a>
									</li>';
							}
						?>

						<li>
							<a href="view-account-details.php" class="pure-menu-link highlighter"> Account </a>
						</li>

						<!-- EMPLOYEES LINK FOR MANAGERS-->
						<?php
							if ($userType == 101 || $userType == 102) {
								echo '<li>
										<a href="view-employees.php" class="pure-menu-link"> Employees </a>	
									</li>';
							}
						?>

						<!-- BILLING CLERK LINKS -->
						<?php
							if ($userType == 103) {
								echo '<li>
										<a href="view-customers.php" class="pure-menu-link"> Customers </a>
									</li>
									<li>
										<a class="pure-menu-link"> Orders </a>
										<ul class="dropdown">
											<li>
												<a href="view-orders.php" class="pure-menu-link"> View Orders </a>
											</li>
											<li>
												<a href="order-form.php" class="pure-menu-link"> Create New Order </a>
											</li>
											<li>
												<a href="cancel-order.php" class="pure-menu-link"> Cancel Order</a>
											</li>
										</ul>
									</li>';
							}
						?>

						<!-- CYLINDER CONTROL CLERK LINKS -->
						<?php
							if ($userType == 104) {
								echo '<li>
										<a class="pure-menu-link"> Cylinders </a>
										<ul class="dropdown">
											<li>
												<a href="update-cylinder-status.php" class="pure-menu-link"> Update Cylinder Status </a>
											</li>
										</ul>
									</li>';
							}
						?>

						<!-- DISPATCHER LINK -->
						<?php
							if ($userType == 105) {
								echo '<li>
										<a href="fill-up-ICR.php" class="pure-menu-link"> Incoming Cylinders</a>
									</li>
									<li>
										<a href="dispatcher-form.php" class="pure-menu-link"> Dispatch Cylinders</a>
									</li>
									<li>
										<a href="dispatcher-home.php" class="pure-menu-link"> View Inventory</a>
									</li>';
							}
						?>

						<!-- ADMINISTRATIVE GASES AND CYLINDERS LINK -->
						<?php
							if ($userType == 101) {
								echo ' <li>
											<a href="view-gases.php" class="pure-menu-link">Gases</a>
										</li>
										<li>
											<a href="view-cylinders.php" class="pure-menu-link"> Cylinders</a>
										</li>';
							}
						?>

						<!-- PRODUCTION CYLINDERS LINK  -->
						<?php
							if ($userType == 106) {
								echo '  <li>
											<a href="refill-cylinder.php" class="pure-menu-link"> Cylinders</a>
										</li>';
							}
						?>

						<!-- REPORTS FOR MANAGERS LINK -->
						<?php
							if ($userType == 101 || $userType == 102) {
								echo '<li>
										<a class="pure-menu-link"> Reports </a>
										<ul class="dropdown">';
									
										if ($userType == 101) {
										echo '	<li>
													<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
												</li>
												<li>
													<a href="report-cylinder-history.php" class="pure-menu-link">Cylinder History Report</a>
												</li>
								                <li>
								                  <a href="report-cylinder-status.php" class="pure-menu-link">Daily Cylinder Status Report</a>
								                </li>';
										}
										else if ($userType == 102) {
											echo '<li>
													<a href="report-sales.php" class="pure-menu-link"> Sales Report </a>
												</li>';
										}

								echo '</ul>
								</li>';
							}
						?>

						<li>
							<a href="logout.php" class="pure-menu-link"> Logout </a>
						</li>
					</ul>
				</div>
				
			</div>

			<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
				<div class="content-container">
					<div class="content-container">
						
						<div class="page-title-container">
							<?php
								if ($userType == 101) echo '<p class="title">Administrative Department: Administrative Manager</p>';
								else if ($userType == 102) echo '<p class="title">Sales and Marketing Department: Sales and Marketing Manager </p>';
								else if ($userType == 103) echo '<p class="title">Administrative Department: Billing Clerk </p>';
								else if ($userType == 104) echo '<p class="title">Administrative Department: Cylinder Control Clerk </p>';
								else if ($userType == 105) echo '<p class="title">Sales and Marketing Department: Dispatcher </p>';
								else if ($userType == 106) echo '<p class="title">Production Department: Production Manager </p>';

							?>
						</div>

						<div class="divider">
							<div>
								<?php 
									if (isset($message)) {
										echo $message;
										$message = NULL;
									}
								?>
							</div>
						</div>

						<div align="center"> 
							<table class="pure-table" style="margin-top:20px">
								<thead>
									<tr>
										<th colspan="2" style="text-align:center"> Account Details </th>
									</tr>
								</thead>
								<?php
									$result = mysqli_query($dbc,$accountDetails);
									while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
										echo"<tr>
												<td>Employee ID</td>
												<td width='70%'> {$row['userID']} </td>
											</tr>
											<tr class='pure-table-odd'>
												<td>Employee Type</td>
												<td width='70%'> {$row['userTypeDescription']} </td>
											</tr>
											<tr>
												<td>Full Name</td>
												<td width='70%'>{$row['name']}</td>
											</tr>
											<tr class='pure-table-odd'>
												<td>Username</td>
												<td width='70%'>{$row['username']}</td>
											</tr>
											<tr>
												<td>Password</td>
												<td width='70%'>********</td>
											</tr>";
									}
								?>
							</table>

							<br>
							<br>

							<a href="edit-account-details.php"> Edit Account Details</a>

						</div>

					</div>
				</div>
			</div>
		</div>
	</body>
</html>