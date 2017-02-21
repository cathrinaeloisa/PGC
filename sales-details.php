<?php
	require_once('/pentagas-connect.php');
	session_start();
	
	$userType = $_SESSION['userTypeID'];
	if ($userType != 102) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function getOrdersFromDateRange($orderID, $startDate, $endDate) {
		return $query = "SELECT o.orderID, cus.name, o.orderDate, od.quantity, gt.gasType, gt.gasName, gt.gasID
						   FROM orders o JOIN orderDetails od ON o.orderID = od.orderID
								 		 JOIN deliveryDetails dd ON od.orderDetailsID = dd.orderDetailsID
										 JOIN customers cus ON cus.customerID = o.customerID
										 JOIN gasType gt ON od.gasID = gt.gasID
									    WHERE o.orderDate >= '{$startDate}'
										  AND o.orderDate <= '{$endDate}'
										  AND o.orderID = '{$orderID}'
									 GROUP BY gt.gasID";	
	}

	function getOrderDetails($orderID) {
		return $query =" SELECT cus.name, o.orderDate
							FROM orders o JOIN customers cus on o.customerID = cus.customerID
						   WHERE o.orderID = '{$orderID}'";
	}


	function getPrice($gasID) {
		return $query = " SELECT gpa.price
							FROM gaspricingaudit gpa JOIN gasType gt ON gpa.gasID = gt.gasID
						   WHERE gpa.gasID = '{$gasID}'
					    ORDER BY gpa.auditID DESC
					       LIMIT 1";
	}


	if(isset($_GET['orderID'])){
		$orderID = $_GET['orderID'];	
	}

?>

</!DOCTYPE html>
<html>
	<head>
		<title>Sales - Sales and Marketing </title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
		<link rel="stylesheet" href="CSS/bootstrap.min.css">

		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
		<script src="CSS/jquery.min.js"></script>
		<script src="CSS/bootstrap.min.js"></script>
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
							<a href="sales-and-marketing-home.php" class="pure-menu-link"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a href="view-employees.php" class="pure-menu-link"> Employees </a>
						</li>
						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul>
								<li>
									<a href="report-sales.php" class="pure-menu-link highlighter"> Sales Report </a>
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
					<div class="row">
						<div class="col">
							<ol class="breadcrumb">
							  <li><a href="report-show-sales.php">Back</a></li>
							  <li class="active">Detailed Sales Report</li>
							</ol>
						</div>
					</div>

					

					<dl class="row">
						<?php 
							$orderDetailsResult = mysqli_query($dbc,getOrderDetails($orderID));
							$orderDetails=mysqli_fetch_array($orderDetailsResult,MYSQL_ASSOC);
						?>
						<dt class="col-sm-2">Order Number:</dt>
						<dd class="col-sm-10"><?php echo $orderID; ?></dd>

						<dt class="col-sm-2">Customer:</dt>
						<dd class="col-sm-10"><?php echo $orderDetails['name'];?></dd>

						<dt class="col-sm-2">Order Date:</dt>
						<dd class="col-sm-10"><?php echo $orderDetails['orderDate'];?></dd>
					</dl>

					<div class="row">
						<table id ="table" class="table table-bordered table-striped">
							<thead> 
								<th style="text-align:center">Gas Ordered</th>
								<th style="text-align:center">Qty. of Cylinders</th>
								<th style="text-align:center">Unit Price</th>
								<th style="text-align:center">Total Price</th>

							</thead>
							<?php	  
								if(isset($_SESSION['enddate']) && isset($_SESSION['startdate'])){
									$ordersResult = mysqli_query($dbc,getOrdersFromDateRange($orderID, $_SESSION['startdate'], $_SESSION['enddate']));
									$totalSales = 0;

									while($ordersRow=mysqli_fetch_array($ordersResult,MYSQL_ASSOC)){
										// $query1="SELECT GAP.gasTypeID, price, auditDate 
										//FROM gasType GT JOIN gaspricingaudit GAP ON GT.gasTypeID = GAP.gasTypeID 
										//WHERE GT.gasTypeID = '{$row['gasTypeID']}' 
										//ORDER BY 3 DESC 
										//LIMIT 1";
										$priceResult = mysqli_query($dbc,getPrice($ordersRow['gasID']));
										$detailsRow=mysqli_fetch_array($priceResult,MYSQL_ASSOC);
											$totalPrice = $detailsRow['price'] * $ordersRow['quantity'];
											$totalPriceFormatted = number_format($totalPrice, 2);

											$totalSales += $totalPrice;

											echo "<tr>
													<td width='50%' align='center'>{$ordersRow['gasType']} {$ordersRow['gasName']}</td>
													<td width='20%' align='center'>{$ordersRow['quantity']}</td> 
													<td align='right'>{$detailsRow['price']}</td>
													<td align='right'>{$totalPriceFormatted}</td>
												 </tr>";
									}

									$totalSales = number_format($totalSales, 2);
									echo " 	<tr>
												<td></td>
												<td></td>
												<td align=\"right\" style=\"font-weight:900\"> Total Sales: </td>
												<td align=\"right\" style=\"font-weight:900\">{$totalSales}</td>
											</tr>";
									
								}
							?>
						</table>
						<br>
						<br>
						<center><b>*** END OF REPORT ***</b></center>
						<br>
						<br>
						<br>
						<br>
						<br>
						<br>
						<br>
						<br>
					</div>

				</div>
			</div>
		</div>
	</body>
</html>