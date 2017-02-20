<?php
$timestamp = NULL;
$message = NULL;
	require_once('pentagas-connect.php');
	session_start();
	
	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	// LIST OF STATUS
	$statusList = "SELECT * FROM CYLINDERSTATUS";
	$result_statusList = mysqli_query($dbc, $statusList);

	function getSelectedCylinders ($cylinderStatus) {
		return $thisquery = "SELECT * FROM CYLINDERS c
								JOIN CYLINDERSTATUS cs ON c.cylinderStatusID=cs.cylinderStatusID
								JOIN GASTYPE gt ON c.gasID=gt.gasID
				   			   WHERE CYLINDERSTATUSDESCRIPTION = '{$cylinderStatus}'
				   			 	";
	}

?>

</!DOCTYPE html>
<html>
	<head>
		<title>Inventory - Administrative </title>
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
							<ul>
								<li>
									<a href="report-inventory.php" class="pure-menu-link highlighter"> Inventory Report</a>
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

		<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
				<div class="content-container">
					
					<div class="page-header">
						<h1> Daily Cylinder Inventory Report</h1>
						<h7> 
							<?php
								date_default_timezone_set('Asia/Manila');
								$timestamp = date("F j, Y // g:i a");
								echo '<b>' .$timestamp. '</b>';
							?>
						</h7>
					</div>
	
					<!-- CODE FOR INVENTORY TABLE -->
					<table id ="Table">
						<thead>
							<th style="text-align:center">Cylinder ID</th>
            				<th style="text-align:center">Gas</th>
            				<th style="text-align:center">Date Acquired</th>	
						</thead>

					<?php
						echo '';

						if (!isset($message) && isset($_SESSION['select-status'])) {
						
							$cylinderStatus = $_SESSION['select-status'];
							$selected_list = mysqli_query($dbc, getSelectedCylinders($cylinderStatus));
							echo "<h3> $cylinderStatus Cylinders </h3>";

							while ($row_selected = mysqli_fetch_array($selected_list,MYSQL_ASSOC)) {
																	
								echo "<tr>
									<td width=\"20%\"><div align=\"center\">{$row_selected['cylinderID']}
	                				<td width=\"20%\"><div align=\"center\">{$row_selected['gasType']}{$row_selected['gasName']}
									<td width=\"20%\"><div align=\"center\">{$row_selected['dateAcquired']}
									
									</div></td>

									</tr>";
							}
						}
					?>
					</table>
					<br>
					<br>
					<center><b>*** END OF REPORT ***</b></center>
					<br>
					<br>
					<br>
					<br>
					<br>
					<br>
					<br>
					<br>
				</div>
			</div>
		</div>
	</body>

	<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	
	<script> 
	$(document).ready(function(){
		$('#Table').DataTable({
			paging: false,
			searching: false,
			ordering: false,
		});
	});
	</script>
	
</html>