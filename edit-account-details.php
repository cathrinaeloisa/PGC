<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	$userName = $_SESSION['userName'];
	$userID = $_SESSION['userID'];
	$employeeName = $_SESSION['name'];

	$accountDetails = "SELECT * FROM userAccounts NATURAL JOIN usertypes WHERE userID = '{$userID}'";

	if (isset($_POST['submit'])) {
		$newName = $_POST['newName'];
		$newUsername = $_POST['newUsername'];
		if ($newName == $employeeName && $newUsername == $userName) {
			$_SESSION['message'] = "No changes were made.";
			header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/view-account-details.php");
		}
		else{
			$existing = FALSE;

			$query = "SELECT username FROM userAccounts";
			$result = mysqli_query($dbc,$query);
			while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
				if ($row['username'] == $newUsername) $existing = TRUE;
			}

			if (!$existing) {
				if ($newName != $employeeName && $newUsername != $userName) {
					$query = "UPDATE useraccounts SET name = '{$newName}', username = '{$newUsername}' WHERE userID = $userID";
					$_SESSION['name'] = $newName;
					$_SESSION['userName'] = $newUsername;
				}
				else if ($newName != $employeeName) {
					$query = "UPDATE useraccounts SET name = '{$newName}' WHERE userID = $userID";
					$_SESSION['name'] = $newName;
				}
				else if ($newUsername != $userName) {
					$query = "UPDATE useraccounts SET username = '{$newUsername}' WHERE userID = $userID";
					$_SESSION['userName'] = $newUsername;
				}

				$result = mysqli_query($dbc,$query);
				if ($result) {
					$_SESSION['message'] = "Account details updated!";
					header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/view-account-details.php");
				}
				else $_SESSION['message'] = "Error updating account details.";
			}

			else {
				$message = "Username already exists.";
			}
		}
	}

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
										<a class="pure-menu-link"> Customers </a>
										<ul class="dropdown">
											<li>
												<a href="view-customers.php" class="pure-menu-link"> View Customers </a>
											</li>
											<li>
												<a href="new-customer.php" class="pure-menu-link"> Add New Customer </a>
											</li>
											<li>
												<a href="view-customers.php" class="pure-menu-link"> Edit Customer Details </a>
											</li>
										</ul>
									</li>
									<li>
										<a class="pure-menu-link"> Orders </a>
										<ul class="dropdown">
											<li>
												<a href="view-orders.php" class="pure-menu-link"> View Orders </a>
											</li>
											<li>
												<a href="make-order.php" class="pure-menu-link"> Create New Order </a>
											</li>
											<li>
												<a href="cancel-order.php" class="pure-menu-link"> Cancel Order</a>
											</li>
										</ul>
									</li>
									<li>
										<a href="set-pickup-date.php" class="pure-menu-link"> Set Pick-up Date</a>
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
											<li>
												<a href="refill-cylinder.php" class="pure-menu-link"> Refill Cylinder </a>
											</li>
											<li>
												<a href="receive-cylinder.php" class="pure-menu-link"> Receive Cylinder </a>
											</li>
										</ul>
									</li>';
							}
						?>

						<!-- DISPATCHER LINK -->
						<?php
							if ($userType == 105) {
								echo '<li>
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

						<!-- REPORTS FOR MANAGERS LINK -->
						<?php
							if ($userType == 101 || $userType == 102) {
								echo '<li>
										<a class="pure-menu-link"> Reports </a>
										<ul class="dropdown">';
									
										if ($userType == 101) {
										echo '<li>
												<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
												<a href="report-cylinder-status.php" class="pure-menu-link"> Inventory Report</a>
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
								else if ($userType == 105) echo '<p class="title">Sales and Marketing Department: Sales and Marketing Manager </p>';
							?>
						</div>

						<div class="divider">
							<div>
								<?php 
									if (isset($_SESSION['message'])) {
										echo $_SESSION['message'];
									}
									else if (isset($message)){
										echo $message;
									}
								?>
							</div>
						</div>

						<div align="center"> 
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="pure-form pure-form-aligned">
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
												
												<div class='pure-control-group'>
													<tr>
														<td>Full Name</td>
														<td width='70%'>
															<input type='text' size='30' name='newName' value={$row['name']}>
														</td>
													</tr>
												</div>
												
												<div class='pure-control-group'>
													<tr class='pure-table-odd'>
														<td>Username</td>
														<td width='70%'>
															<input type='text' size='30' name='newUsername' value={$row['username']}>
														</td>
													</tr>
												</div>

												<tr>
													<td>Password</td>
													<td width='70%'>
														<a href='change-password.php'> Change Password
													</td>
												</tr>";
										}
									?>
								
								</table>

								<br>
								<br>

								<input type="submit" name="submit" value="Update Account Details"> &nbsp&nbsp&nbsp	
								<a class="cancel-button" href="view-account-details.php"> Cancel </a>

							</form>
						</div>

					</div>
				</div>
			</div>
		</div>
	</body>
</html>