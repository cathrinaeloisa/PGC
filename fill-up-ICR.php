<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 105) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	$_SESSION['selectCustomer'] = NULL;

	function getCustomersWithCylinderHoldings() {
		return $query = " SELECT c.customerID, c.customerType, c.name, c.deliveryAddress
							FROM customers c JOIN orders o ON o.customerID = c.customerID
											 JOIN orderDetails od ON o.orderID = od.orderID
											 JOIN deliveryDetails dd ON dd.orderDetailsID = od.orderDetailsID
						   WHERE dd.pickedupdate IS NULL
					    GROUP BY c.customerID";
	}

	if (isset($_POST['customerSelectButton'])) {
		echo $_SESSION['selectCustomer'];
		$_SESSION['selectCustomer'] = $_POST['selectCustomer'];
	}
?>

<html>
	<head>
		<title>Dispatcher Home</title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<script src="CSS/jquery.min.js"></script>
		<script src="CSS/bootstrap.min.js"></script>
		<link rel="stylesheet" href="CSS/bootstrap.min.css">
		<link rel="stylesheet" href="CSS/docsupport/prism.css">
		<link rel="stylesheet" href="CSS/chosen.css">
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
							<a href="dispatcher-home.php" class="pure-menu-link"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						
						<li>
							<a href="fill-up-ICR.php" class="pure-menu-link highlighter"> Incoming Cylinders</a>
						</li>
						<li>
							<a href="dispatcher-form.php" class="pure-menu-link"> Dispatch Cylinders</a>
						</li>
							

						<li>
							<a href="dispatcher-home.php" class="pure-menu-link"> View Inventory</a>
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
				<!-- WELCOME HEADER -->
				<div class="row">
			 		<div class="col">
			 			<div class="page-header">
						  
						</div>
			 		</div>	
				</div>

				<!-- FORM FOR GETTING CUSTOMER INFORMATION START-->
				<div class="row">
					<div class="col">
						<div class="well">
							<form action="show-incoming-cylinders.php" class="form-horizontal" id="customerInformation" method="post">
								<div class="form-group">
									<label for="timeIn" class="col-sm-3 control-label">Time in: </label>
							    	<div class="col-sm-2">
							    		<input type="time" class="form-control" name="timeIn" value="<?php if (isset($_POST['timeIn'])) echo $_POST['timeIn']; ?>"> 
							   		</div>
								</div>	

								<div class="form-group">
									<label for="selectCustomer" class="col-sm-3 control-label">Received from: </label>
							    	<div class="col-sm-3">
							    		<select class="form-control" name="selectCustomer">
							    			<option value="">Select...</option>
							    			<?php
							    				$result = mysqli_query($dbc,getCustomersWithCylinderHoldings());
												while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
													echo "<option value=\"{$row['customerID']}\"> {$row['name']} </option>"; 
												}
							    			?>
							    		</select>
							   		</div>
								</div>	
								<br>
								<div class="form-group">
									<center>
										<input class="btn btn-primary" type="submit" name="customerSelectButton" value="Proceed">
									</center>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- FORM FOR GETTING CUSTOMER INFORMATION END -->

			</div>
		</div>

		<script type="text/javascript" src="CSS/jquery.validate.min.js"></script>
		<script type="text/javascript">
			$(function() {
       			// Setup form validation on the #register-form element
		        $("#customerInformation").validate({
		            // Specify the validation rules
		            rules: {
		                timeIn: "required",
		                selectCustomer: "required",
		            },
		            highlight: function(element) {
		                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		            },
		            success: removeError,
		            // Specify the validation error messages
		            messages: {
		                timeIn: "Please input time in.",
		                selectCustomer: "Please select customer.",
		            }
		        });

		        function removeError(element) {
		        element.addClass('valid')
		            .closest('.form-group')
		            .removeClass('has-error');
    			}
    		})
		</script>
	</body>
</html>