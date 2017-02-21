<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	// ADD CYLINDER
	$cylinderID = NULL;
	if (isset($_POST['add-cylinder'])){
		$message=NULL;
		$gasID = $_POST['gasID'];
		$date = DATE('Y-m-d');
		for ($i = 1; $i <= $_POST['amount']; $i++){
			$year = DATE('Y');
			$year -= 1973;
			// Get last cylinder added
			$query = "SELECT cylinderID FROM cylinders ORDER BY cylinderID DESC LIMIT 1";
			$result = mysqli_query($dbc,$query);
			$result = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$cylinderNumber = sprintf("%04d", substr($result['cylinderID'],0,4) + 1);
			$cylinderID = $cylinderNumber."-".$year;

			$query="INSERT INTO cylinders (cylinderID,gasID,cylinderStatusID,dateAcquired) VALUES ('$cylinderID','$gasID','402', '$date')";
			$result = mysqli_query($dbc,$query); 
		}

		if ($result) {
			$getGasName = "SELECT gasType,gasName FROM gasType WHERE gasID = '$gasID'";
			$gasNameResult = mysqli_query($dbc,$getGasName); 
			$row = mysqli_fetch_array($gasNameResult, MYSQLI_ASSOC);

			$message = "{$_POST['amount']} cylinder/s added for {$row['gasType']} {$row['gasName']}.";
		}
		else $message = "Error adding new cylinder";
			
	}/*End of main Submit conditional*/

	$cylinderList = "SELECT * FROM cylinders NATURAL JOIN gasType NATURAL JOIN cylinderStatus ORDER BY cylinderID";
	
?>

<html>
	<head>
		<title>View Cylinders</title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>

		<script src="CSS/jquery.min.js"></script>
		<script src="CSS/bootstrap.min.js"></script>
		<link rel="stylesheet" href="CSS/bootstrap.min.css">

	</head>	
	<body>
		<!-- <?php echo dirname($_SERVER['PHP_SELF'])?> -->
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
							<a href="view-gases.php" class="pure-menu-link">Gases</a>
						</li>
						<li>
							<a href="view-cylinders.php" class="pure-menu-link highlighter"> Cylinders</a>
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

			<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
				<div class="content-container">
					<div class="page-title-container">
						<p class="title"> Cylinders </p>
					</div>

					<div class="divider">
						<div>
							<?php 
								if (isset($message)) {
									echo $message;
									$message = NULL;
								}
							?>
						</div>
					</div>
					
					<table class="hover stripe cell-border" id="Table" style="text-align:center">
						<thead style="text-align:center">
							<th style="text-align:center !important;"> Cylinder Number </th>
							<th style="text-align:center !important;"> Gas Type </th>
							<th style="text-align:center !important;"> Gas </th>
							<th style="text-align:center !important;"> Cylinder Status </th>
						</thead>

						<?php
							$result = mysqli_query($dbc,$cylinderList);
							while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
								echo "	<tr>
											<td> {$row['cylinderID']} </td>
											<td> {$row['gasType']} </td>
											<td> {$row['gasName']} </td>
											<td> {$row['cylinderStatusDescription']} </td>
										</tr>";
							}
						?>

					</table>

					<br>
					<br>

					<!-- Button trigger modal -->
					<center>
						<button type="button" class="btn" data-toggle="modal" data-target="#addCylinder">Add New Cylinder</button>
					</center>

					<!-- Modal for Add Cylinder -->
					<div class="modal fade" id="addCylinder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						<div class="modal-dialog" role="document">
					    	<div class="modal-content">
					      		<div class="modal-header">
					        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        		<h4 class="modal-title" id="myModalLabel">New Cylinder Details</h4>
					      		</div>

					      		<div class="modal-body">
					      			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-horizontal" id="addCylinderForm">
					      				<div>
											<div class="form-group">
												<label for="gasID" class="col-sm-3 control-label"> Gas </label>
												<div class="col-sm-8">
													<select class="form-control" name="gasID">
														<option value="">Select...</option>
														<?php
															$query = "SELECT * FROM gasType";
															$result = mysqli_query($dbc,$query);
															$show = "";
															while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
																echo "<option value={$row['gasID']}> {$row['gasType']} {$row['gasName']} </option>";
																$show = $row['gasID'];
															}
														?>

													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="amount" class="col-sm-3 control-label"> Number of Cylinders </label>
												<div class="col-sm-8">
													<input type="number" class="form-control" name="amount" min="1">
												</div>
											</div>
										</div>
					      		</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						        	<button type="submit" name="add-cylinder" class="btn btn-primary">Add Cylinder(s)</button>
						    	</div>

						      </form>
					    </div>
					  </div>
					</div>

				</div>
			</div>
		</div>

		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="CSS/jquery.validate.min.js"></script>

		<script type="text/javascript">
			$(function() {
        // Setup form validation on the #register-form element
		        $("#addCylinderForm").validate({
		            // Specify the validation rules
		            rules: {
		            	gasID: "required",
		                amount: "required",
		            },
		            highlight: function(element) {
		                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		            },
		            success: removeError,
		            // Specify the validation error messages
		            messages: {
		                gasID: "Please select gas for cylinder/s.",
		                amount: "Please input amount of cylinders.",
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
				$('#Table').DataTable();
			});
			$('#Table').DataTable({
				"order": [],
			    "columnDefs": [ {
			      "targets"  : [0],
			      "orderable": false,
				}]
			});
		</script>
	</body>
</html>