<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 106) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}
?>

<html>
	<head>
		<title>Production Manager Home</title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" href="CSS/bootstrap-dashboard-edit.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
		<link rel="stylesheet" href="CSS/bootstrap.min.css">

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
							<a href="production-manager-home.php" class="pure-menu-link highlighter"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>
						<li>
							<a href="view-gases-production.php" class="pure-menu-link">Gases</a>
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