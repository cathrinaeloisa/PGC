<?php
$timestamp = NULL;
$message = NULL;
	require_once('pentagas-connect.php');
	session_start();
	
	$userType = $_SESSION['userTypeID'];
	if ($userType != 102) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function getOrdersFromDateRange($startDate, $endDate) {
		return $query = "SELECT o.orderID, cus.name, o.orderDate 
						   FROM orders o JOIN orderDetails od ON o.orderID = od.orderID
								 		 JOIN deliveryDetails dd ON od.orderDetailsID = dd.orderDetailsID
										 JOIN customers cus ON cus.customerID = o.customerID
									    WHERE o.orderDate >= '{$startDate}'
										  AND o.orderDate <= '{$endDate}'
									GROUP BY o.orderID";	
	}

	function getOrderDetails($orderID) {
		return $query =" SELECT od.gasID, od.quantity 
							FROM orders o JOIN orderdetails od on o.orderID = od.orderID
						   WHERE o.orderID = '{$orderID}'
						GROUP BY od.gasID";
	}

	function getPrice($gasID) {
		return $query = " SELECT gpa.price
							FROM gaspricingaudit gpa JOIN gasType gt ON gpa.gasID = gt.gasID
						   WHERE gpa.gasID = '{$gasID}'
					    ORDER BY gpa.auditID DESC
					       LIMIT 1";
	}


				
	if(isset($_POST['show-report'])){
		
		if (empty($_POST['startdate'])){
			$_SESSION['startdate']=FALSE;
			$message='You forgot to enter the start date';
		}else
			$_SESSION['startdate']=$_POST['startdate'];
		if (empty($_POST['enddate'])){
			$_SESSION['enddate']=FALSE;
			$message='You forgot to enter the end date';
		}else
			$_SESSION['enddate']=$_POST['enddate'];
		if(!empty($_POST['startdate']) && !empty($_POST['enddate'])){
			if($_POST['startdate'] > $_POST['enddate'] ){
			$message='End Date must be larger than Start Date!';
			}
		}
	}
	else{
		$SESSION['startdate'] = null;
		$SESSION['enddate'] = null;
	}			

?>

</!DOCTYPE html>
<html>
	<head>
		<title>Sales - Sales and Marketing </title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" href="CSS/bootstrap-dashboard-edit.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
		<link rel="stylesheet" href="CSS/bootstrap.min.css">

		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
		<script src="CSS/jquery.min.js"></script>
		<script src="CSS/bootstrap.min.js"></script>
	</head>
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		
		<script> 
		$(document).ready(function(){
			$('#table').DataTable({
				"paging": false,
				"ordering": false,
				"info": false,
				"searching": false,	
			});

		});
		</script>
		
		
	
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
							  <li><a href="report-sales.php">Back</a></li>
							  <li class="active">Sales Report for <?php echo $_SESSION['startdate'].' to '.$_SESSION['enddate'];?> </li>
							</ol>
						</div>
					</div>

						

					<div>
						<table id="table" class="table cell-border stripe" width="100%">
							<thead> 
								<th align="center">Order ID</th>
								<th align="center">Customer Name</th>
								<th align="center">Order Date</th>
								<th align="right">Total Sales</th>
							</thead>
							<?php 
								$sum = null;
								if(!isset($message) && isset($_SESSION['enddate']) && isset($_SESSION['startdate'])){	
									$totalSales = 0;
									$orderRangeQueryResult = mysqli_query($dbc,getOrdersFromDateRange($_SESSION['startdate'], $_SESSION['enddate'])); //GETTING ORDERS FROM SPECIFIED DATE RANGE
									while($orders=mysqli_fetch_array($orderRangeQueryResult,MYSQL_ASSOC) ){ //LOOPING THROUGH ORDERS THAT ARE WITHIN RANGE
										$sum = 0;										
										$detailsQueryResult = mysqli_query($dbc,getOrderDetails($orders['orderID']));
										while($details=mysqli_fetch_array($detailsQueryResult,MYSQL_ASSOC) ){ //LOOPING THROUGH GASES FROM ORDER TO GET TOTAL PRICE
											$priceQueryResult = mysqli_query($dbc,getPrice($details['gasID']));
											$price = mysqli_fetch_array($priceQueryResult,MYSQL_ASSOC);

											$sum += $details['quantity'] * $price['price'];
											$totalSales += $sum;

										}
										echo "<tr>
												<td align=\"center\"><a href='sales-details.php?IDHold={$orders['orderID']}'>".$orders['orderID']."</td>
												<td align=\"center\">{$orders['name']}</td>
												<td align=\"center\">{$orders['orderDate']}</td>
												<td align=\"right\">{$sum}</td>
												</tr>";				
									}
									echo " 	<tr>
												<td></td>
												<td></td>
												<td align=\"right\" style=\"font-weight:900\"> Total: </td>
												<td align=\"right\" style=\"font-weight:900\">{$totalSales}</td>
											</tr>";
								}
							?>
						</table>
					</div>
				
				</div>
			</div>

		</div>
	</body>
</html>