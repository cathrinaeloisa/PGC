<?php
	include_once('dashboard-backend.php');
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}
?>

<html>
	<head>
		<title>Administrative Manager Home</title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" href="CSS/bootstrap-dashboard-edit.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
		<link rel="stylesheet" href="CSS/bootstrap.min.css">

		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
		<script src="CSS/jquery.min.js"></script>
		<script src="CSS/bootstrap.min.js"></script>

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
						<li>
							<a href="administrative-manager-home.php" class="pure-menu-link highlighter"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a href="view-employees.php"  class="pure-menu-link"> Employees </a>
						</li>
						<li>
							<a href="view-gases.php" class="pure-menu-link">Gases</a>
						</li>
						<li>
							<a href="view-cylinders.php" class="pure-menu-link"> Cylinders</a>
						</li>

						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul class="dropdown">
								<li>
									<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
								</li>
								<li>
									<a href="report-cylinder-history.php" class="pure-menu-link">Cylinder History Report</a>
								</li>
				                <li>
				                  <a href="report-cylinder-status.php" class="pure-menu-link">Daily Cylinder Status Report</a>
				                </li>
							</ul>
						</li>
						<li>
							<a href="logout.php" class="pure-menu-link"> Logout </a>
						</li>
					</ul>
				</div>
			</div>
			<!-- END SIDEBAR -->

			<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
					<div class="row margin-edit-for-top-page">
				 		<div class="col">
				 			<div class="page-header page-header-edit">
								<h1><small>WELCOME</small></h1>
							</div>
				 		</div>	
					</div>

					<!-- GRAPH CONTAINER -->
					<div class="row" id="totalCylinderBalanceWithCustomersChartContainer">
						<div id="chart">
						</div>
					</div>

					<!-- TABLE CONTAINERS -->
					<div class="row margin-edit"">
				 		<div class="col-md-6" id="totalCylinderBalanceWithCustomersTableContainer">
				 			<div class="panel panel-default">
								<div class="panel-heading">Customers with Cylinder Balances</div>
							    <table class="table table-bordered table-striped">
					 				<thead>
					 					<tr class="font-size-edit">
											<th> Customer Name </th>
											<th> Cylinder Balance </th>
										</tr>
					 				</thead>
						 			<?php 
						 				$result = getTotalCylinderBalanceWithCustomers();
						 				while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
						 					echo "<tr>
						 							<td> {$row['customername']}</td>
						 							<td style='text-align:center'> {$row['cylindercount']}</td>
						 						</tr>";
						 				}
						 			?>
						 		</table>	
							</div>		 			
						</div>	

						<div class="col-md-6" id="totalCylinderBalanceWithPGCTableContainer">
				 			<div class="panel panel-default">
								<div class="panel-heading">
									Total Cylinder Balance with PGC
								</div>
							    <table class="table table-bordered table-striped">
					 				<thead>
					 					<tr class="font-size-edit">
											<th> Gas Name </th>
											<th> Cylinder Balance </th>
										</tr>
					 				</thead>
						 			<?php 
						 				$result = getTotalCylinderBalanceWithPGC();
						 				while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
						 					echo "<tr>
						 							<td> {$row['gasType']} {$row['gasName']}</td>
						 							<td style='text-align:center'> {$row['cylindercount']}</td>
						 						</tr>";
						 				}
						 			?>
						 		</table>	
							</div>		 			
						</div>	
					</div>

			</div>
		</div>

		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="CSS/dashboard-charts.js"></script>

		<script> 
			$(document).ready(function(){
				$('#Table').DataTable();
			});
		</script>

	</body>
</html>