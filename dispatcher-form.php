<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 105) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function getNumberOfAvailableCylinders($gasID) {
		return $query = "SELECT COUNT(cylinderID) AS 'cylinderCount'
					FROM cylinders 
				   WHERE cylinderStatusID = 401 
				     AND gasID = '{$gasID}'";
	}

	function getOrderDetails($orderID) {
		return $query = "SELECT *
					FROM orderDetails NATURAL JOIN deliveryDetails
				   WHERE orderID = '{$orderID}'";
	}

	$orderList = "SELECT o.orderID, cus.name, o.orderDate, dd.deliveryDate
					FROM orderdetails od JOIN orders o ON o.orderID = od.orderID
                                     	 JOIN customers cus ON o.customerID = cus.customerID
                                     	 JOIN deliverydetails dd ON dd.orderDetailsID = od.orderDetailsID
					WHERE dd.deliveryDate >= CURDATE()
				GROUP BY o.orderID
				ORDER BY dd.deliveryDate";

	$_SESSION['selectedOrder'] = NULL;
	$_SESSION['orderDetailsID'] = NULL;

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
				
					<div class="row">
						<div class="col">
							<div class="page-header">
								<h1><small>Pending Deliveries</small></h1>
							</div>
						</div>
					</div>

					<!-- TABLE -->
					<div>
						<table class="hover stripe cell-border" id="Table">
							<thead>
								<tr>	
									<th style="text-align:center !important;"> Order Number </th>
									<th style="text-align:center !important;"> Customer Name </th>
									<th style="text-align:center !important;"> Order Date </th>
								</tr>
							</thead>
							<tbody>
								<form action="order-details.php" method="POST">
									<?php
										$result = mysqli_query($dbc,$orderList);
										while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
											echo "	<tr style=\"text-align:center !important;\">
														<td> 
															<a href='order-details.php?selectedOrder={$row['orderID']}'>".$row['orderID']."</a>
														</td>
														<td> {$row['name']} </td>
														<td> {$row['orderDate']} </td>
													</tr>";
										}
									?>
								</form>
							</tbody>
						</table>
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