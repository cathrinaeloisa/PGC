<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 103) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	if (isset($_POST['submit'])){
		$message=NULL;

		$orderIDs = "SELECT orderID FROM orders";
		$result = mysqli_query($dbc,$orderIDs);

		if (!isset($_POST['orderNumber'])){
			$message = 'Please select an order.';
			$orderList = "SELECT * FROM orders NATURAL JOIN orderStatus NATURAL JOIN orderDetails NATURAL JOIN customers GROUP BY orderID ORDER BY orderID ASC";
		}
		else {
			if (isset($_POST['orderNumber'])) {
				$encounteredNotValid = FALSE;
				foreach ($_POST['orderNumber'] as $orderNumber) {
					// CHECK CURRENT ORDER STATUS
					$query = "SELECT * FROM orders WHERE orderID = $orderNumber";
					$result = mysqli_query($dbc,$query);
					$row = mysqli_fetch_array($result);
					$selected = $row['orderStatusID'];
					

					if ($selected != 802) {
						$encounteredNotValid = TRUE;
					}
					else {
						$query = "UPDATE orders SET orderStatusID = 803 WHERE orderID = $orderNumber";
						$result = mysqli_query($dbc,$query);
						
						if ($result) {
							$query = "UPDATE cylinders NATURAL JOIN orderDetails SET cylinderStatusID = 401 WHERE orderID = $orderNumber";
							$updateCancel = mysqli_query($dbc,$query);
							if ($updateCancel) $message = 'Order/s cancelled!';
							else $message = 'Error cancelling order.';
						}
					}
				}

				if ($encounteredNotValid) $message = "Completed and/or Cancelled orders can no longer be cancelled.";
			}

			$orderList = "SELECT * FROM orders NATURAL JOIN orderStatus NATURAL JOIN orderDetails NATURAL JOIN customers GROUP BY orderID ORDER BY orderID ASC";
		}
	}
	else if (isset($_POST['filter']) && !empty($_POST['search'])) {
		$message = NULL;
		$search = $_POST['search'];
		$searchBy = $_POST['searchBy'];

		$orders = "SELECT * FROM orders NATURAL JOIN customers NATURAL JOIN orderStatus";
		$result = mysqli_query($dbc,$orders);

		$orderList = "SELECT * FROM orders NATURAL JOIN customers NATURAL JOIN orderStatus NATURAL JOIN orderDetails WHERE CONCAT(`orderID`,`orderStatusDescription`, `orderStatusID`, `customerID`, `name`) LIKE '%".$search."%'";
	}

	else {	
		$orderList = "SELECT * FROM orders NATURAL JOIN orderStatus NATURAL JOIN orderDetails GROUP BY orderID ORDER BY orderID ASC";
	}
?>


<html>
	<head> 
		<title> Cancel Order </title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="value" href="/cancel-order-function.php">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>

	</head>
	<body>
		<div class="pure-g">
			<!-- DON'T TOUCH -->
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
							<a class="pure-menu-link"> Account </a>
							<ul class="dropdown">
								<li>
									<a href="view-account-details.php" class="pure-menu-link"> View Account Details </a>
								</li>
								<li>
									<a href="edit-account-details.php" class="pure-menu-link"> Edit Account Details </a>
								</li>
							</ul>
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
									<a href="view-orders.php" class="pure-menu-link"> View Orders </a>
								</li>
								<li>
									<a href="view-customers.php" class="pure-menu-link"> Create New Order </a>
								</li>
								<li>
									<a href="cancel-order.php" class="pure-menu-link highlighter"> Cancel Order</a>
								</li>
							</ul>
						</li>
						<li>
							<a href="set-pickup-date.php" class="pure-menu-link"> Set Pick-up Date</a>
						</li>
						<li>
							<a href="logout.php" class="pure-menu-link"> Logout </a>
						</li>
					</ul>
				</div>
			</div>
			<!-- END -->

			<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
				<div class="content-container">
					<div class="page-title-container">
						<p class="title"> Cancel Order </p>
					</div>

					<div class="divider">
						<div>
							<?php 
								if (isset($message)){
									echo $message;
								}
							?>
						</div>
					</div>
					
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
						<div align="center">
							<table class="pure-table" id="Table">
								<thead>
									<tr>
										<th> </th>
										<th> Order Number </th>
										<th> Order Status </th>
										<th> Customer Name </th>
										<th> Order Date </th>
										<th> Delivery Date </th>
									</tr>
								</thead>

								<?php
									$result = mysqli_query($dbc,$orderList);
									$index = 1;
									while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
										if ($index % 2 == 0) {
											echo "	<tr class=\"pure-table-odd\">
														<td> <input type=\"checkbox\" name=\"orderNumber[]\" value={$row['orderID']}></td>
														<td> {$row['orderID']} </td>
														<td> {$row['orderStatusDescription']} </td>
														<td> {$row['name']} </td>
														<td> {$row['orderDate']} </td>
														<td> {$row['deliveryDate']} </td>
													</tr>";
										}
										else {
											echo "	<tr>
														<td> <input type=\"checkbox\" name=\"orderNumber[]\" value={$row['orderID']}></td>
														<td> {$row['orderID']} </td>
														<td> {$row['orderStatusDescription']} </td>
														<td> {$row['name']} </td>
														<td> {$row['orderDate']} </td>
														<td> {$row['deliveryDate']} </td>
													</tr>";
										}

										$index++;
									}
								?>

							</table>

						</div>
						
						<br>

						<div align="center"><input type="submit" name="submit" value="Cancel Order"></div>
					
					</form>

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
			      "targets"  : [0,2,3],
			      "orderable": false,
				}]
			});
		</script>
	</body>
</html>