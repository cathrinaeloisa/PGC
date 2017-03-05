<?php
$timestamp = NULL;
$message = NULL;

	session_start();
	require_once('pentagas-connect.php');

	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function getCylinders($status, $gasID) {
		if (strcmp($status, "Available") == 0) {
			return $query = "SELECT c.cylinderID from cylinders c
		                        join gastype gt on c.gasID=gt.gasID
		                        join cylinderstatus cs on c.cylinderStatusID=cs.cylinderStatusID
		                        where c.gasID = '{$gasID}'
		                        AND cs.cylinderStatusDescription LIKE '{$status}'
		                        OR c.gasID = '{$gasID}'
		                        AND cs.cylinderStatusDescription LIKE 'Reserved'";
		}

		else return $query = "SELECT c.cylinderID from cylinders c
		                        join gastype gt on c.gasID=gt.gasID
		                        join cylinderstatus cs on c.cylinderStatusID=cs.cylinderStatusID
		                        where cs.cylinderStatusDescription LIKE '{$status}'
		                        AND c.gasID = '{$gasID}'";
	}

	function getGasNames($status) {
		if (strcmp($status, "Available") == 0) {
			return $query = "SELECT gt.gasName, gt.gasType, gt.gasID
							   FROM gasType gt JOIN cylinders c ON c.gasID = gt.gasID
							   				   JOIN cylinderStatus cs ON c.cylinderStatusID = cs.cylinderStatusID
							  WHERE cs.cylinderStatusDescription LIKE '{$status}'
							     OR cs.cylinderStatusDescription LIKE 'Reserved'
						   GROUP BY gt.gasID";
		}

		else return $query ="SELECT gt.gasName, gt.gasType, gt.gasID
							   FROM gasType gt JOIN cylinders c ON c.gasID = gt.gasID
							   				   JOIN cylinderStatus cs ON c.cylinderStatusID = cs.cylinderStatusID
							  WHERE cs.cylinderStatusDescription LIKE '{$status}'
						   GROUP BY gt.gasID";
	}

?>

</!DOCTYPE html>
<html>
	<head>
		<title> Cylinder Status Report </title>
		<link rel="stylesheet" href="CSS/dashboard.css">
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>

		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
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
									<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
								</li>
				                <li>
				                  <a href="report-cylinder-status.php" class="pure-menu-link highlighter">Daily Cylinder Status Report</a>
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
				<!-- TITLE -->
				<div class="row">
					<div class="page-header">
						<h1> Daily
							<?php
								if ($_POST['select-status'] == 'Available') echo " Cylinder Availablity ";
								else if ($_POST['select-status'] == 'Empty') echo " Empty Cylinder ";
								else if ($_POST['select-status'] == 'Damaged') echo " Damaged Cylinder ";
								else if ($_POST['select-status'] == 'In Repair') echo " In Repair Cylinder ";
								else if ($_POST['select-status'] == 'Repaired') echo " Repaired Cylinder ";
								else if ($_POST['select-status'] == 'Dispatched') echo " Dispatched Cylinder ";
								else if ($_POST['select-status'] == 'No Longer In Use') echo " No Longer In Use Cylinder ";
								else if ($_POST['select-status'] == 'Lost') echo " Lost Cylinder ";
								$_SESSION['select-status'] = $_POST['select-status'];
							?> Report
						</h1>
						<h7>
							<?php
								date_default_timezone_set('Asia/Manila');
								$timestamp = date("F j, Y // g:i a");
								echo '<b>' .$timestamp. '</b>';
							?>
						</h7>
					</div>
				</div>

	            <div class="row">
		           <table class="table table-bordered table-striped" id ="Table";>
		           		<?php
				            $gasResult = mysqli_query($dbc, getGasNames($_POST['select-status']));
							while($gasRow=mysqli_fetch_array($gasResult)){
				                echo "<thead>
					                    <th colspan=\"5\" style='border-top:2px solid black; border-bottom:2px solid black; text-align:center'>{$gasRow['gasType']} {$gasRow['gasName']} Cylinders</th>
					                  </thead>";

				            	$cylinderResult = mysqli_query($dbc, getCylinders($_POST['select-status'], $gasRow['gasID']));
				            	$rowCount = mysqli_num_rows($cylinderResult);
				            	while($cylinderRow=mysqli_fetch_array($cylinderResult)){
				                	$cylinderList[] = $cylinderRow['cylinderID'];
				            	}

				            	for ($i = 0; $i < $rowCount;) {
				            		echo "<tr>";
				            		for ($j = $i, $counter = 0; $counter < 5; $counter++, $j++) {
				            			if ($j < $rowCount) echo "<td align='center'>{$cylinderList[$j]}</td>";
				            			else echo "<td></td>";
				            		}
				            		echo "</tr>";
				            		$i += 5;
				            	}
				            	unset($cylinderList);
					        }
					    ?>


			        </table>

			        <br>
					<br>
					<center><b>*** END OF REPORT ***</b></center>

					<br>
				</div>
				<form action="pdf-cylinder-status.php" method="post">
					<div class="row">
							<div class="col">
								<center><input class="btn btn-primary" type="submit" name="show-report" value="Print Report"></center>
							</div>
					</div>
			</div>
		</div>
	</body>

	<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
	<script type="text/javascript" src="CSS/jquery.validate.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>

	<!-- FOR VALIDATION -->
	<script type="text/javascript">
		$(function() {
   			// Setup form validation on the #register-form element
	        $("#statusSelectionForm").validate({
	            // Specify the validation rules
	            rules: {
	            	'select-status': "required",
	            },
	            highlight: function(element) {
	                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
	            },
	            success: removeError,
	            // Specify the validation error messages
	            messages: {
	                'select-status': "Please select a status.",
	            }
	        });

	        function removeError(element) {
	        element.addClass('valid')
	            .closest('.form-group')
	            .removeClass('has-error');
			}
		})
	</script>

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
