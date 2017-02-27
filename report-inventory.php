<?php
$timestamp = NULL;
$message = NULL;
	require_once('pentagas-connect.php');
	session_start();
	
	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function statusCountCylindersOfGas ($gasID, $statusID) {
		return $query = " SELECT COUNT(c.cylinderID) AS 'cylinderCount', gt.gasID, gt.gasType, gt.gasName
							FROM cylinders c JOIN gasType gt ON c.gasID = gt.gasID
						   WHERE c.cylinderStatusID = $statusID
                             AND gt.gasID = '{$gasID}'";
	}

	function countTotalCylindersOfGas ($gasID) {
		return $query = " SELECT COUNT(c.cylinderID) AS 'cylinderCount'
							FROM cylinders c JOIN gasType gt ON c.gasID = gt.gasID
						   WHERE gt.gasID = '{$gasID}'
						   	 AND c.cylinderStatusID != 403
                             AND c.cylinderStatusID != 404
                             AND c.cylinderStatusID != 407
                             AND c.cylinderStatusID != 408";
	}

	function getGas () {
		return $query = " SELECT gasID
							FROM gasType";
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
							<a class="pure-menu-link"> Cylinders</a>
							<ul class="dropdown">
								<li>
									<a href="view-cylinders.php" class="pure-menu-link"> Cylinder Details</a>
								</li>
				                <li>
				                  <a href="cylinder-history.php" class="pure-menu-link">Cylinder Transaction Records</a>
				                </li>
							</ul>
						</li>
						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul>
								<li>
									<a href="report-inventory.php" class="pure-menu-link highlighter"> Inventory Report</a>
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
					
					<div class="row">
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
					</div>

					<!-- CODE FOR INVENTORY TABLE -->
					<div class="row">
						<table class="table table-bordered table-striped" id ="Table">
							<thead>
								<th style="text-align:center; font-size:13">Gas Name</th>
	            				<th style="text-align:center; font-size:13">Qty. Available</th>
	            				<th style="text-align:center; font-size:13">Qty. Dispatched</th>
	            				<th style="text-align:center; font-size:13">Qty. Empty</th>

								<th style="text-align:center; font-size:13">Total Cylinders</th>

							</thead>

							<?php
								$gasResult = mysqli_query($dbc, getGas());
								while ($gasRow =  mysqli_fetch_array($gasResult,MYSQL_ASSOC)) {
									$availableCountResult = mysqli_query($dbc, statusCountCylindersOfGas($gasRow['gasID'], 401));
									$reservedCountResult = mysqli_query($dbc, statusCountCylindersOfGas($gasRow['gasID'], 409));
									$dispatchedCountResult = mysqli_query($dbc, statusCountCylindersOfGas($gasRow['gasID'], 406));
									$emptyCountResult = mysqli_query($dbc, statusCountCylindersOfGas($gasRow['gasID'], 402));

									$totalCountResult = mysqli_query($dbc, countTotalCylindersOfGas($gasRow['gasID']));

									$availableCountRow = mysqli_fetch_array($availableCountResult,MYSQL_ASSOC);
									$reservedCountRow = mysqli_fetch_array($reservedCountResult,MYSQL_ASSOC);
									$dispatchedCountRow = mysqli_fetch_array($dispatchedCountResult,MYSQL_ASSOC);
									$emptyCountRow = mysqli_fetch_array($emptyCountResult,MYSQL_ASSOC);

									$totalCountRow = mysqli_fetch_array($totalCountResult,MYSQL_ASSOC);

									echo "<tr>
											<td>{$availableCountRow['gasType']} {$availableCountRow['gasName']}</td>
			                				<td width='10%' align='center'>".($availableCountRow['cylinderCount'] + $reservedCountRow['cylinderCount'])."</td>
			                				<td width='10%' align='center'>{$dispatchedCountRow['cylinderCount']}</td>
			                				<td width='10%' align='center'>{$emptyCountRow['cylinderCount']}</td>

			                				<td width='10%' align='center'>{$totalCountRow['cylinderCount']}</td>

										   </tr>";
								}
							?>
						</table>

						<br>
						<br>
						<center><b>*** END OF REPORT ***</b></center>
						<br>
						<br>						
					</div>
					<form action="print-inventory-report.php" method="post">
						<div class="row">
								<div class="col">
									<center><input class="btn btn-primary" type="submit" name="show-report" value="Print Report"></center>
								</div>
						</div>
					</form>
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
			info: false,
		});
	});
	</script>



	
</html>