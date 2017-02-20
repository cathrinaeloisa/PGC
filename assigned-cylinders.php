<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 105) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function getAssignedCylinders($gasID, $orderDetailsID) {
		return $query = " SELECT *
							FROM deliverydetails dd JOIN cylinders c ON c.cylinderID = dd.cylinderID 
													JOIN gastype gt ON c.gasID = gt.gasID 
													JOIN orderDetails od ON od.orderDetailsID = dd.orderDetailsID
							WHERE dd.orderDetailsID = '{$orderDetailsID}'
							  AND c.gasID = '{$gasID}'";
	}

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
								  <li><a href="order-details.php">Order <?php echo $selectedOrder;?> Details </a></li>
								  <li class="active"><?php echo "$selectedGasType $selectedGasName";?> Cylinders for Order <?php echo $selectedOrder; ?></li>
								</ol>
							</div>
						</div>

						<!-- TABLE -->
						<div>
							<table class="hover stripe cell-border" id="Table">
								<thead>
									<tr>	
										<th style="text-align:center !important;"> Cylinder ID </th>
										<th style="text-align:center !important;"> Delivery Date </th>
										<th style="text-align:center !important;"> Picked-up Date </th>
									</tr>
								</thead>
								<tbody>
										<?php
											$result = mysqli_query($dbc,getAssignedCylinders($selectedGasID, $selectedOrderDetails));
											while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
												echo "
													<tr style=\"text-align:center !important;\">
														<td> {$row['cylinderID']} </td>
														<td> {$row['deliveryDate']} </td>
														<td> {$row['pickedupdate']} </td>
													</tr>
												";
											}
										?>
								</tbody>
							</table>
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