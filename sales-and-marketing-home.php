<?php
	session_start();
	include_once('dashboard-backend.php');
	require_once('pentagas-connect.php');

	$userType = $_SESSION['userTypeID'];
	if ($userType != 102) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}
?>

<html>
	<head>
		<title>Sales and Marketing Manager Home</title>
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
					<ul class="pure-menu-list">
						<li>
							<a href="sales-and-marketing-home.php" class="pure-menu-link highlighter"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a href="view-employees.php" class="pure-menu-link"> Employees </a>
						</li>
						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul class="dropdown">
								<li>
									<a href="report-sales.php" class="pure-menu-link"> Sales Report </a>
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
				<div class="row margin-edit-for-top-page">
			 		<div class="col">
			 			<div class="page-header page-header-edit">
						  WELCOME	
						</div>
			 		</div>	
				</div>

				<!-- GRAPH CONTAINER -->
				<div class="row" id="totalCylinderBalanceWithCustomersChartContainer">
					<div id="chart">
					</div>
				</div>
			</div>
		</div>
	</body>
</html>

