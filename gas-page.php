<?php

	session_start();

	//$userType = $_SESSION['userTypeID'];
	//if ($userType != 101) {
	//	header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	//}

	if(isset($_GET['gasHold'])){
		$gasTypeID = $_GET['gasHold'];	
	}

	require_once('pentagas-connect.php');

	$query1 = "Select * from gastype where gastypeID = {$gasTypeID}";	
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
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a href="view-employees.php" class="pure-menu-link"> Employees </a>
						</li>
						<li>
							<a href="view-gases.php" class="pure-menu-link">Gases</a>
						</li>
						<li>
							<a href="view-cylinders.php" class="pure-menu-link"> Cylinders</a>
						</li>

						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul>
								<li>
									<a href="report-inventory.php" class="pure-menu-link highlighter"> Inventory Report</a>
								</li>
								<li>
									<a href="report-cylinder-status.php" class="pure-menu-link"> Cylinder Status Report</a>
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
						<p class="title"> <?php 	echo "<b>Gas Type: \"{$row1['gasName']}\"  </b>"; ?> <a href="report-inventory.php" class="a-href-right"> Return to Initial Report </a> </p>
					</div>

					<div class="divider">
						<?php
							date_default_timezone_set('Asia/Manila');
							$timestamp = date("F j, Y // g:i a");
							echo '<center>' .$timestamp. '</center>';
						?>
					</div>


					<?php
						require_once('pentagas-connect.php');
							$query1 = "Select * from gastype where gastypeID = {$gasTypeID}";	
							$result1 = mysqli_query($dbc,$query1);
							$row1=mysqli_fetch_array($result1,MYSQL_ASSOC);



							// require_once('../sql_connect.php');



							$query = "Select  cylinderID, gasname from cylinders c join gastype g on c.gasTypeID = g.gasTypeID
																			
																				where g.gasTypeID = {$gasTypeID}
																			
								  " ;	
							$query2 = "Select  d.cylinderID from orders o	join orderdetails d on o.orderID = d.orderID
						                                                join cylinders c on c.cylinderID = d.cylinderID
						                                                join gastype g on c.gasTypeID = g.gasTypeID
																		where   pickupDate = null 
																				or pickupDate > '{$_SESSION['end-date']}'
																				and
																				deliveryDate >= '{$_SESSION['start-date']}'
																				and deliveryDate <= '{$_SESSION['end-date']}'
																				and orderDate >= '{$_SESSION['start-date']}'
																				and orderDate <= '{$_SESSION['end-date']}'
																				";	
						$result = mysqli_query($dbc,$query);
						$result2 = mysqli_query($dbc,$query2);
						echo '<table id ="table";>
									<thead> 
										<th>Cylinder ID </th>
									
									
									</thead>';



						while($row=mysqli_fetch_array($result,MYSQL_ASSOC)){
							$row2=mysqli_fetch_array($result2,MYSQL_ASSOC);
							if($row['cylinderID'] == $row2['cylinderID']){
							
							
							}
							else{
								echo "<tr>

								<td width=\"20%\" ><div align=\"center\"> <a href='cylinder-for-inventory.php?cylinderHold={$row['cylinderID']}'>".$row['cylinderID']."
								</div></td>


								</tr>";
							}
							// puede dito yung latest return ng cylinder 
							
						}

					?>
				</div>
			</div>
		</div>
	</body>
</html>