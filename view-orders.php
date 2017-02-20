<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 103) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	$orderList = "SELECT * FROM orders NATURAL JOIN orderStatus NATURAL JOIN customers NATURAL JOIN orderDetails GROUP BY orderID";
		
?>

<html>
	<head>
		<title>View Orders</title>
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
					<ul class="pure-menu-list">
						<li>
							<a href="billing-clerk-home.php" class="pure-menu-link"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>
						
						<li>
							<a class="pure-menu-link"> Customers </a>
							<ul class="dropdown">
								<li>
									<a href="view-customers.php" class="pure-menu-link"> View Customers </a>
								</li>
								<li>
									<a href="new-customer.php" class="pure-menu-link"> Add New Customer </a>
								</li>
								<li>
									<a href="view-customers.php" class="pure-menu-link"> Edit Customer Details </a>
								</li>
							</ul>
						</li>
						<li>
							<a class="pure-menu-link"> Orders </a>
							<ul>
								<li>
									<a href="view-orders.php" class="pure-menu-link highlighter"> View Orders </a>
								</li>
								<li>
									<a href="view-customers.php" class="pure-menu-link"> Create New Order </a>
								</li>
								<li>
									<a href="cancel-order.php" class="pure-menu-link"> Cancel Order</a>
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
						<p class="title"> Orders </p>
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
				

					<div>
						<table class="hover stripe cell-border" id="Table">
							<thead>
								<tr>
									<th style="text-align:center !important;"> Order Number </th>
									<th style="text-align:center !important;"> Customer Name </th>
									<th style="text-align:center !important;"> Order Date </th>
									<th style="text-align:center !important;"> Delivery Date </th>
									<th style="text-align:center !important;"> Order Status </th>
								</tr>
							</thead>

							<?php
								$result = mysqli_query($dbc,$orderList);
								while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
									echo "	<tr>
												<td> {$row['orderID']} </td>
												<td> {$row['name']} </td>
												<td> {$row['orderDate']} </td>
												<td> {$row['deliveryDate']} </td>
												<td> {$row['orderStatusDescription']} </td>
											</tr>";
								}
							?>

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
			});
			$('#Table').DataTable({
				"order": [],
			    "columnDefs": [ {
			      "targets"  : [4],
			      "orderable": false,
				}]
			});

		</script>

	</body>
</html>

