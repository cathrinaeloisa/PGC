<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 105) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function getCustomersWithCylinderHoldings() {
		return $query = " SELECT c.customerID, c.customerType, c.name, c.deliveryAddress
							FROM customers c JOIN orders o ON o.customerID = c.customerID
											 JOIN orderDetails od ON o.orderID = od.orderID
											 JOIN deliveryDetails dd ON dd.orderDetailsID = od.orderDetailsID
						   WHERE dd.pickedupdate IS NULL
					    GROUP BY c.customerID";
	}

	function getCylinders($customerID) {
		return $query = " SELECT c.cylinderID, gt.gasName, gt.gasType, gt.isSpecialGas
							FROM deliveryDetails dd JOIN orderDetails od ON dd.orderDetailsID = od.orderDetailsID
													JOIN orders o ON od.orderID = o.orderID
													JOIN cylinders c ON dd.cylinderID = c.cylinderID
													JOIN gasType gt ON c.gasID = gt.gasID
						   WHERE o.customerID = '{$customerID}'
						     AND c.cylinderStatusID = 406";
	}

	function setPickedupDate($cylinderID) {
		return $query = " UPDATE deliverydetails 
							 SET pickedupdate = CURDATE() 
						   WHERE cylinderID = '$cylinderID' 
						     AND pickedupdate IS NULL";
	}
?>

<?php 
	if (isset($_POST['selectCustomer'])) {
		$customerID = $_POST['selectCustomer'];
		$_SESSION['selectCustomer'] = $customerID;
	}
	else if (isset($_SESSION['selectCustomer'])) {
		$customerID = $_SESSION['selectCustomer'];
	}

	if(isset($_POST['cylinders']) && isset($_POST['cylinderStatusID'])) {
		$selected_key = $_POST['cylinderStatusID'];
		$error = FALSE;
		foreach($_POST['cylinders'] as $check) {
			if($selected_key==402){
				$update ="UPDATE cylinders SET cylinderStatusID= 402 where cylinderID = '{$check}'";
				// $result = mysqli_query($dbc,$update);
			}
			else if($selected_key==403){
				$update ="UPDATE cylinders SET cylinderStatusID = 403 where cylinderID = '{$check}'";
				// $result = mysqli_query($dbc,$update);
			}

			$result = mysqli_query($dbc,$update);
			if (!$result) $error = TRUE;
			else {
				$updatePickedUpDate = mysqli_query($dbc,setPickedupDate($check));
				if (!$updatePickedUpDate) $error = TRUE;
			}
		}
		
		if ($error) $message = "Error updating cylinders.";
		else $message = "Status updated!";	
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
				<div class="content-container">
					<div class="row">
						<div class="col">
							<ol class="breadcrumb">
							  <li><a href="fill-up-ICR.php">Back</a></li>
							  <li class="active">Cylinders with Customer <?php echo $customerID; ?></li>
							</ol>
						</div>
					</div>

					<!-- CYLINDERS TABLE -->
					<div>
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-horizontal" id="cylinderForm">
						<table class="hover stripe cell-border" id="cylinderTable">
			      			<thead>
								<tr>
									<th style="text-align:center"><input type="checkbox" name="select-all" id="select-all"></th>
									<th style="text-align:center"> Cylinder ID </th>
									<th style="text-align:center"> Gas Type</th>
									<th style="text-align:center"> Gas Name</th>
									<th></th>
								</tr>
							</thead>
							
							<?php
								$result = mysqli_query($dbc,getCylinders($customerID));
				                while($row = mysqli_fetch_array($result)) {
									echo "<tr style=\"text-align:center\">
											<td><input type='checkbox' name='cylinders[]' value= {$row['cylinderID']}></td>
											<td> {$row['cylinderID']} </td>
											<td> {$row['gasType']} </td>
											<td> {$row['gasName']} </td>";
											if ($row['isSpecialGas'] == 1) echo "<td> Special Gas</td>";
											else echo "<td></td>";
									echo "</tr>";
								}
			                ?>
						</table>
						
						<br>
						<br>

						<div class="well wel-sm">
							<div class="form-group">
								<label for="cylinderStatusID" class="col-sm-5 control-label">Cylinder Status: </label>
						    	<div class="col-sm-3">
						    		<select class="form-control" name="cylinderStatusID">
						    			<option value="">Select...</option>
										<option value="402">Empty</option>
										<option value="403">Damaged</option>
						    		</select>
						   		</div>
							</div>	
							<br>
							<center>
								<div class="form-group">
									<input class="btn btn-primary submitButton" type="submit" name="submit" value="Proceed">
								</div>
							</center>
						</div>
					</form>
					</div> <!-- CYLINDERS TABLE END-->


				</div> <!-- CONTENT CONTAINER END-->
			</div>
		</div>

		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>

		<script> 
			$(document).ready(function(){
			    var table = $('#customerTable').DataTable();
			});
			$('#cylinderTable').DataTable({
				"order": [],
			    "columnDefs": [ {
			      "targets"  : [0],
			      "orderable": false,
				}]
			});
		</script>

		<script type="text/javascript" src="CSS/jquery.validate.min.js"></script>
		<script type="text/javascript">
			$(function() {
       			// Setup form validation on the #register-form element
		        $("#cylinderForm").validate({
		            // Specify the validation rules
		            rules: {
		                cylinderStatusID: "required",
		                'cylinders[]': { required: true, }
		            },
		            highlight: function(element) {
		                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		            },
		            success: removeError,
		            // Specify the validation error messages
		            messages: {
		                 alert("Please select at least one cylinders")
		                cylinderStatusID: "Please select a status."
		            }
		        });

		        function removeError(element) {
		        element.addClass('valid')
		            .closest('.form-group')
		            .removeClass('has-error');
    			}
    		})
    		$('#select-all').click(function(event) {   
			    if(this.checked) {
			        // Iterate each checkbox
			        $(':checkbox').each(function() {
			            this.checked = true;                        
			        });
			    }
			});
		</script>
	</body>
</html>