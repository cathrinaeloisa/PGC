<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 106) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function getDateToday() {
		$timestamp = date('M d, Y');
		return $timestamp;
	}
?>

<html>
	<head>
		<title>Production Manager Home</title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" href="CSS/bootstrap-dashboard-edit.css">
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
		<link rel="stylesheet" href="CSS/bootstrap.min.css">

		<script src="CSS/jquery.min.js"></script>
		<script src="CSS/bootstrap.min.js"></script>

		<!-- FOR CHARTS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"></script>

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
							<a href="production-manager-home.php" class="pure-menu-link highlighter"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>
						<li>
							<a href="refill-cylinder.php" class="pure-menu-link"> Cylinders</a>
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
							<h3>WELCOME</h3>
						</div>
			 		</div>	
				</div>

				<!-- CYLINDERS RECEIVED/PICKED UP PANEL -->
				<div class="row">
					<div class="col">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Cylinders Picked Up</h3>
							</div>
							<div class="panel-body">

							</div>
						</div>
					</div>
				</div>
				<!-- END CYLINDERS RECEIVED PANEL -->

				<!-- CYLINDERS REFILLED PANEL -->
				<div class="row">
					<div class="col">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Cylinders Refilled</h3>
							</div>
							<div class="panel-body">

							</div>
						</div>
					</div>
				</div>
				<!-- END CYLINDERS REFILLED PANEL -->

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