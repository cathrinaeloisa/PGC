<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function getLatest ($gasID) {
		return $gasList = "SELECT * 
							 FROM gasType GT JOIN gaspricingaudit GAP ON GT.gasID = GAP.gasID 
							WHERE GT.gasID = '$gasID' 
						 ORDER BY GAP.auditID DESC 
						    LIMIT 1";
	}

	$gasID = $_GET['gasID'];
	$gasType = $_GET['gasType'];
	$gasName = $_GET['gasName'];

	$gasList = "SELECT * FROM gasType NATURAL JOIN gaspricingaudit WHERE gasID = '{$gasID}'";

?>

<html>
	<head>
		<title>View Gases</title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>

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
							<a class="pure-menu-link highlighter">Gases</a>
						</li>
						<li>
							<a  href="view-cylinders.php" class="pure-menu-link"> Cylinders</a>
						</li>
						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul class="dropdown">
								<li>
									<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
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
			<!-- END SIDEBAR -->

			<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
				<div class="content-container">
					<div class="row">
						<div class="col">
							<ol class="breadcrumb">
							  <li><a href="view-gases.php">Back</a></li>
							  <li class="active"> Price History of <?php echo "$gasType $gasName";?></li>
							</ol>
						</div>
					</div>


					<div>
						<table class="hover stripe cell-border" id="Table">
							<thead>
								<tr>
									<th style="text-align:center !important;"> Date of Price Change</th>
									<th style="text-align:center !important;"> Gas Price </th>
									<th style="text-align:center !important;"> Reason for Price Change</th>
								</tr>
							</thead>

							<?php
								$result = mysqli_query($dbc,$gasList);
								while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {	
									echo "	<tr>
												<td style='text-align:center !important;'> {$row['auditDate']} </td>
												<td style='text-align:right !important;'> {$row['price']} </td>
												<td style='text-align:left !important;'> {$row['remarks']} </td>
											</tr>";
									
								}
							?>

						</table>
						<br>
						<br>
					
					</div>


				</div>
			</div>
		</div>

		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>

		<script> 
			$(document).ready(function(){
				$('#Table').DataTable();
			});
			$('#Table').DataTable({
				"order": [],
			    "searching": false,
			});
		</script>
	</body>
</html>