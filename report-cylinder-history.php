<?php
	require_once('pentagas-connect.php');
	session_start();

	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}


	// LIST OF CUSTOMERS
	$cylinderList = "SELECT * FROM cylinders";
	$result_cylinderList= mysqli_query($dbc, $cylinderList);

	// LIST of ORDER DETAILS
	$orderdetailList = "SELECT * from customers c
								join orders o on c.customerID = o.customerID
								join orderdetails od on o.orderID = od.orderID
								join cylinders cy on od.gasID=cy.gasID
								join gastype gt on od.gasID=gt.gasID
								join deliverydetails dd on od.orderDetailsID=dd.orderDetailsID
								group by o.orderID
				" ;
	$result_orderdetailList=mysqli_query($dbc, $orderdetailList);


?>

<!DOCTYPE html>
<html>
<head>
	<title> Cylinder History </title>
	<link rel="stylesheet" href="CSS/dashboard.css" >
	<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>

	<link rel="stylesheet" href="CSS/miggy.css">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<script src="CSS/jquery.min.js"></script>
	<script src="CSS/bootstrap.min.js"></script>
	<link rel="stylesheet" href="CSS/bootstrap.min.css">


</head>
<body>
	<div class="pure-g">
		<div class="pure-u-5-24 sidebar-container">
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
									<a href="report-cylinder-history.php" class="pure-menu-link highlighter">Cylinder History Report</a>
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
			<!-- END SIDEBAR -->
		</div>

		<div class="pure-u-6-24"></div>
		<div class="pure-u-17-24">
			<!-- TITLE -->
			<div class="row">
				<div class="page-header">
					<h1> Cylinder History Report</h1>
				</div>
			</div>
			
			<div class="row">
				<form action="report-show-cylinder-history.php" method="post" class="form-horizontal" id="cylinderSelectionForm">
					<div class="well well-lg">
						<div class="row">
							<div class="col">
								<div class="form-group">
									<label for="cylinder" class="col-sm-2 control-label"> Cylinder ID: </label>
									<div class="col-sm-4">
										<input type="text" placeholder="Choose a Cylinder" class="form-control" name="cylinder" list="cylinderID"/>
										<datalist id="cylinderID">
											<?php
												while ($row_cylinders=mysqli_fetch_array($result_cylinderList, MYSQLI_ASSOC)) {
														 echo "<option value=\"{$row_cylinders['cylinderID']}\">";
												}
											?>
										</datalist>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col">
							<center><input class="btn btn-primary" type="submit" name="showReport" value="Show Report"></center>
						</div>
					</div>

				</form>
			</div>
	</div>
</div>
</body>

	<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
	<script type="text/javascript" src="CSS/jquery.validate.min.js"></script>

	<!-- FOR VALIDATION -->
	<script type="text/javascript">
		$(function() {
   			// Setup form validation on the #register-form element
	        $("#cylinderSelectionForm").validate({
	            // Specify the validation rules
	            rules: {
	            	cylinder: "required",
	            },
	            
	            highlight: function(element) {
	                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
	            },
	            success: removeError,
	            // Specify the validation error messages
	            messages: {
	                cylinder: "Please select a cylinder.",
	            }
	        });

	        function removeError(element) {
	        element.addClass('valid')
	            .closest('.form-group')
	            .removeClass('has-error');
			}
		})
	</script>
</html>

