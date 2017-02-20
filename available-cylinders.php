<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 105) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function getAvailableCylinders($gasID) {
		return $query = " SELECT cylinderID
							FROM cylinders 
						   WHERE cylinderStatusID = 401 
						     AND gasID = '{$gasID}'";
	}

	function getQuantity($selectedOrderDetails) {
		return $query = " SELECT od.quantity, COUNT(dd.cylinderID) AS 'cylinderCount'
							FROM orderDetails od JOIN deliverydetails dd ON dd.orderDetailsID = od.orderDetailsID
						   WHERE od.orderDetailsID = '{$selectedOrderDetails}'";
		
	}

	$orderList = "SELECT COUNT(c.cylinderID) AS 'cylinderCount', o.orderID, cus.name, o.orderDate, dd.deliveryDate
					FROM cylinders c JOIN orderDetails od ON c.gasID = od.gasID
									 JOIN orders o ON o.orderID = od.orderID
                                     JOIN customers cus ON o.customerID = cus.customerID
                                     JOIN deliverydetails dd ON dd.orderDetailsID = od.orderDetailsID
					WHERE dd.deliveryDate > NOW()
				GROUP BY o.orderID
				ORDER BY dd.deliveryDate";

	if(isset($_GET['orderDetailsID'])) {
		$selectedOrder = $_SESSION['selectedOrder'];
		$selectedOrderDetails = $_GET['orderDetailsID'];
		$selectedGasID = $_GET['gasID'];
		$selectedGasName = $_GET['gasName'];
		$selectedGasType = $_GET['gasType'];
		$_SESSION['orderDetailsID'] = $selectedOrderDetails;
		$_SESSION['gasID'] = $selectedGasID;
		$_SESSION['gasName'] = $selectedGasName;
		$_SESSION['gasType'] = $selectedGasType;
	}

	if(!isset($selectedOrderDetails)){
		$selectedOrder = $_SESSION['selectedOrder'];
		$selectedOrderDetails = $_SESSION['orderDetailsID'];
		$selectedGasID = $_SESSION['gasID'];
		$selectedGasName = $_SESSION['gasName'];
		$selectedGasType = $_SESSION['gasType'];
	}

	if (isset($_POST['cylinders'])){
		$date = DATE('Y-m-d');
		$result = mysqli_query($dbc,getQuantity($selectedOrderDetails));
		$result=mysqli_fetch_array($result,MYSQLI_ASSOC);
		$quantityNeeded = $result['quantity'] - $result['cylinderCount'];

		foreach($_POST['cylinders'] as $cylinderID) {
			if ($quantityNeeded > 0) {
				// GENERATE DELIVERY DETAILS ID
				$query = "SELECT od.orderDetailsID, dd.deliveryDetailsID
							FROM orderDetails od JOIN deliveryDetails dd ON od.orderDetailsID = dd.orderDetailsID
						   WHERE od.orderDetailsID = '{$selectedOrderDetails}'
						     AND dd.cylinderID IS NULL
						ORDER BY dd.deliveryDetailsID ASC LIMIT 1";
				$result = mysqli_query($dbc,$query);
				$result=mysqli_fetch_array($result,MYSQLI_ASSOC);

				$query = "UPDATE deliverydetails SET cylinderID = '{$cylinderID}' WHERE deliveryDetailsID = '{$result['deliveryDetailsID']}'";
				$result = mysqli_query($dbc,$query);
				
				if ($result) {
					$query = "UPDATE cylinders c SET c.cylinderStatusID = 406 WHERE c.cylinderID = '{$cylinderID}'"; 
					$result = mysqli_query($dbc,$query);
				}

				$quantityNeeded--;
			}
			else $message = "Enough cylinders have already been selected for this order.";
		}
	}

?>

<html>
	<head>
		<title>Dispatcher Home</title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>

		<script src="jquery.min.js"></script>
		<script src="bootstrap.min.js"></script>
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
							<a href="fill-up-ICR.php" class="pure-menu-link "> Incoming Cylinders</a>
						</li>
						<li>
							<a href="dispatcher-form.php" class="pure-menu-link highlighter"> Dispatch Cylinders</a>
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

			<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
					<div class="content-container">
						<div class="row">
							<div class="col">
								<ol class="breadcrumb">
								  <li><a href="dispatcher-form.php">Pending Deliveries</a></li>
								  <li><a href="order-details.php">Order <?php echo $selectedOrder; ?> Details </a></li>
								  <li class="active">Available Cylinders for <?php echo "$selectedGasType $selectedGasName"; ?></li>
								</ol>
							</div>
						</div>

						<div style="margin-bottom: 20">
							<?php 
								if (isset($message)) {
									echo $message;
									$message = NULL;
								}
							?>
						</div>
						
						<!-- TABLE -->
						<div>
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
								<table class="hover stripe cell-border" id="Table">
									<thead>
										<tr>	
											<th style="text-align:center !important;"></th>
											<th style="text-align:center !important;"> Cylinder Serial Number </th>
										</tr>
									</thead>
									<tbody>
											<?php
												$result = mysqli_query($dbc,getAvailableCylinders($selectedGasID));
												while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
													echo "
														<tr style=\"text-align:center !important;\">
															<td>
																<input type='checkbox' name='cylinders[]' value={$row['cylinderID']}>
															</td>
															<td> {$row['cylinderID']} </td>
														</tr>
													";
												}
											?>
									</tbody>
								</table>

								<div align="center">
									<input class="btn" type="submit" id="chosenCylindersButton" name="chosenCylinders" value="Assign Cylinders to Order <?php echo $selectedOrder; ?>">
									<br>
									<br>
									<br>
									<br>

								</div>
								
							</form>

						</div>
					
					</div>
			</div>
		</div>

		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>

		<script> 
			$(document).ready(function(){
				$('#Table').DataTable();
				$('#detailsTable').DataTable();
			});
			$('#Table').DataTable({
				"order": [],
			    "columnDefs": [ {
			    "paging": false,
			      "targets"  : [0],
			      "orderable": false,
				}]
			});
		</script>
	</body>
</html>