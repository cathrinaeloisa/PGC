<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 104) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}
?>

<html>
	<head>
		<title>Cylinder Control Clerk Home</title>
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
					<ul class="pure-menu-list">
						<li>
							<a href="cylinder-control-home.php" class="pure-menu-link highlighter"> Home </a>
						</li>
						
						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>
						
						<li>
							<a class="pure-menu-link"> Cylinders </a>
							<ul class="dropdown">
								<li>
									<a href="update-cylinder-status.php" class="pure-menu-link"> Update Cylinder Status </a>
								</li>
								<li>
									<a href="refill-cylinder.php" class="pure-menu-link"> Refill Cylinder </a>
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
				</div>
			</div>
		</div>
	</body>
</html>




