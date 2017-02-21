<?php
$timestamp = NULL;
$message = NULL;


	session_start();
	require_once('pentagas-connect.php');

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
		<title> Cylinder Activity </title>
		<link rel="stylesheet" href="CSS/dashboard.css">
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
	
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		<script src="CSS/jquery.min.js"></script>
		<script src="CSS/bootstrap.min.js"></script>
		<link rel="stylesheet" href="CSS/bootstrap.min.css">

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
									<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
								</li>
								<li>
									<a href="report-cylinder-history.php" class="pure-menu-link">Cylinder History Report</a>
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
						<h1>Daily Cylinder Status Report</h1>
					</div>
				</div>

	        	<?php
		        	$query = "SELECT * from cylinders c
		                        join gastype gt on c.gasID=gt.gasID
		                        join cylinderstatus cs on c.cylinderStatusID=cs.cylinderStatusID
		                " ;

	            	$result = mysqli_query($dbc,$query);
	            ?>

				<!-- CHOOSE STATUS CONTAINER -->
				<div class="row">
					<form action="report-show-cylinder-status.php" method="post" class="form-horizontal" id="statusSelectionForm">
						<div class="well well-lg">
							<div class="form-group">
								<label for="select-status" class="col-sm-2 control-label"> Select a Status: </label>
								<div class="col-sm-5">
									<input type="text" placeholder="Select a Status" class="form-control" name="select-status" list="statusList"/>
									<datalist id="statusList">
										<?php
											while ($row_status=mysqli_fetch_array($result_statusList, MYSQLI_ASSOC)) {
												echo "<option value=\"{$row_status['cylinderStatusDescription']}\">"; 
											}
										?>
									</datalist>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<center><input class="btn btn-primary" type="submit" name="show-report" value="Show Report"></center>
							</div>
						</div>
					</form>
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
