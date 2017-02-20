<?php
	require_once('pentagas-connect.php');

	session_start();

	//$userType = $_SESSION['userTypeID'];
	//if ($userType != 101) {
	//	header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	//}

	if(isset($_GET['customerHold'])){
		$customerID = $_GET['customerHold'];	
	}


	// require_once('pentagas-connect.php');

	$query1 = "SELECT * FROM customers WHERE customerid = {$customerID}";	
	$result1 = mysqli_query($dbc,$query1);
	$row1=mysqli_fetch_array($result1,MYSQL_ASSOC);

	
?>

<html>
	<head>
		<title>Inventory Report</title>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
		<link rel="stylesheet" href="CSS/dashboard.css">
		<link rel="stylesheet" href="CSS/patrick.css">
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
	</head>
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script> 
	$(document).ready(function(){
		$('#table').DataTable();
	});
	</script>
	<body>
		<!-- <?php echo dirname($_SERVER['PHP_SELF'])?> -->
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
						<li>
							<a href="administrative-manager-home.php" class="pure-menu-link"> Home </a>
						</li>

						<li>
							<a class="pure-menu-link"> Account </a>
							<ul class="dropdown">
								<li>
									<a href="view-account-details.php" class="pure-menu-link"> View Account Details </a>
								</li>
								<li>
									<a href="edit-account-details.php" class="pure-menu-link"> Edit Account Details </a>
								</li>
							</ul>
						</li>

						<li>
							<a class="pure-menu-link"> Employees </a>
							<ul class="dropdown">
								<li>
									<a href="view-employees.php" class="pure-menu-link"> View Employees</a>
								</li>
								<li>
									<a href="create-user-administrative.php" class="pure-menu-link"> Create New Employee</a>
								</li>
								<li>
									<a href="edit-user-details-administrative.php" class="pure-menu-link"> Edit Employee Details</a>
								</li>
							</ul>
						</li>
						<li>
							<a class="pure-menu-link">Gases</a>
							<ul class="dropdown">
								<li>
									<a href="view-gases.php" class="pure-menu-link"> View Gases</a>
								</li>
								<li>
									<a href="add-new-gas-type.php" class="pure-menu-link"> Add New Gas Type</a>
								</li>
								<li>
									<a href="edit-gas-price.php" class="pure-menu-link"> Edit Gas Pricing</a>
								</li>
							</ul>
						</li>
						<li>
							<a class="pure-menu-link"> Cylinders</a>
							<ul class="dropdown">
								<li>
									<a href="view-cylinders.php" class="pure-menu-link"> View Cylinders</a>
								</li>
								<li>
									<a href="add-cylinder.php" class="pure-menu-link"> Add New Cylinder</a>
								</li>
							</ul>
						</li>

						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul>
								<li>
									<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
								</li>
								<li>
									<a href="report-cylinder-status.php" class="pure-menu-link highlighter"> Cylinder Status Report</a>
								</li>
							</ul>
						</li>
						<li>
							<a href="logout.php" class="pure-menu-link"> Logout </a>
						</li>
					</ul>
				</div>
				
			</div>
	
			<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
				<div class="content-container">
					<div class="page-title-container">
						<p class="title"> <?php echo "<b>{$row1['name']} </b>";?>  <a href="inventory-report.php" class="a-href-right"> Return to Initial Report </a> </p>
					</div>

					<div class="divider">
						<?php
							
							date_default_timezone_set('Asia/Manila');
							$timestamp = date("F j, Y // g:i a");
							echo '<center>' .$timestamp. '</center>';
						
						
						?>
					</div>


					<?php
						// require_once('../pentagas-connect.php');
							// where customer id yung pinundot sa reportPrac2


						$query = "Select d.cylinderID,g.gasname, o.orderID ,orderDate,deliveryDate,pickupDate from orders o 
																				join orderdetails d on o.orderID = d.orderID 
																				join cylinders c on c.cylinderID = d.cylinderID 
																				join gastype g on c.gasTypeID = g.gasTypeID
																				join customers u on o.customerID = u.customerID 
																				where o.customerID = {$customerID}
																				AND deliveryDate >= '{$_SESSION['start-date']}'
																				and deliveryDate <= '{$_SESSION['end-date']}'
																				and orderDate >= '{$_SESSION['start-date']}'
																				and orderDate <= '{$_SESSION['end-date']}'
																			
								  " ;	
						$result = mysqli_query($dbc,$query);
						echo '<table id ="table";>
									<thead> 
										<th>Cylinder ID </th>
										<th>Gas Type</th>
										<th>Order Number</th>
										<th>Order Date</th>
										<th>Delivery Date</th>
										<th>Pick-Up Date</th>
									
									</thead>';


						$index = 1;
						while($row=mysqli_fetch_array($result,MYSQL_ASSOC)){

							if ($index % 2 == 0) {
								echo "<tr class=\"pure-table-odd\">
								<td width=\"20%\" ><div align=\"center\"> {$row['cylinderID']}</a> 
								</div></td>
								<td width=\"20%\"><div align=\"center\">{$row['gasname']}
								</div></td>
								<td width=\"20%\"><div align=\"center\">{$row['orderID']}
								</div></td>
								<td width=\"20%\"><div align=\"center\">{$row['orderDate']}
								</div></td>
								<td width=\"20%\"><div align=\"center\">{$row['deliveryDate']}
								</div></td>
								<td width=\"20%\"><div align=\"center\">{$row['pickupDate']}
								</div></td>

								</tr>";
							}

							else {
								echo "<tr>
								<td width=\"20%\" ><div align=\"center\"> {$row['cylinderID']}</a> 
								</div></td>
								<td width=\"20%\"><div align=\"center\">{$row['gasname']}
								</div></td>
								<td width=\"20%\"><div align=\"center\">{$row['orderID']}
								</div></td>
								<td width=\"20%\"><div align=\"center\">{$row['orderDate']}
								</div></td>
								<td width=\"20%\"><div align=\"center\">{$row['deliveryDate']}
								</div></td>
								<td width=\"20%\"><div align=\"center\">{$row['pickupDate']}
								</div></td>

								</tr>";
							}
						
							$index++;
							
							
						}


							


					?>
				</div>
			</div>
		</div>
	</body>
</html>