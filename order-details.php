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
		return $query = " SELECT *
							FROM orderDetails NATURAL JOIN deliverydetails NATURAL JOIN gasType
						   WHERE orderID = '{$orderID}'
						GROUP BY orderDetailsID";
	}

	function getAllAssignedCylinders($orderID) {
		return $query = "SELECT *
						   FROM orders o NATURAL JOIN orderDetails
										 NATURAL JOIN deliverydetails
										 NATURAL JOIN gasType
						  WHERE o.orderID = '{$orderID}'";
	}

	function getGasOrders($orderID) {
		return $query = "SELECT gt.gasID, gt.gasName, gt.gasType 
						   FROM gasType gt JOIN orderDetails od ON od.gasID = gt.gasID
						   				   JOIN orders o ON o.orderID = od.orderID
						  WHERE o.orderID = '{$orderID}'";
	}

	function getQuantityOrdered($selectedOrderDetails) {
		return $query = "SELECT od.quantity, COUNT(dd.cylinderID) AS 'cylinderCount'
							FROM orderDetails od JOIN deliverydetails dd ON dd.orderDetailsID = od.orderDetailsID
						   WHERE od.orderDetailsID = '{$selectedOrderDetails}'";
	}

	if(isset($_GET['selectedOrder'])){
		$selectedOrder = $_GET['selectedOrder'];
		$_SESSION['selectedOrder'] = $selectedOrder;
	}

	if (!isset($selectedOrder)) {
		$selectedOrder = $_SESSION['selectedOrder'];
	}

?>



<html>
	<head>
		<title>Dispatcher Home</title>
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
							<a href="dispatcher-home.php" class="pure-menu-link"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a href="fill-up-ICR.php" class="pure-menu-link "> Incoming Cylinder Receipt</a>
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
			<!-- END SIDEBAR -->

			<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
					<div class="content-container">
						<!-- NAVIGATION -->
						<div class="row">
							<div class="col">
								<ol class="breadcrumb">
								  <li><a href="dispatcher-form.php">Pending Deliveries</a></li>
								  <li class="active">Order <?php echo $selectedOrder;?> Details </li>
								</ol>
							</div>
						</div>

						<!-- TABLE -->
						<div>
							<table class="hover stripe cell-border" id="Table">
								<thead>
									<tr>	
										<th style="text-align:center;"> Gas ID </th>
										<th style="text-align:center;"> Gas Name </th>
										<th style="text-align:center;"> Quantity Ordered</th>
										<th style="text-align:center;"> Delivery Date </th>
										<th ></th>
										<th></th>

									</tr>
								</thead>
								<tbody>
										<?php
											$result = mysqli_query($dbc,getOrderDetails($selectedOrder));
											while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
												echo "
													<tr style=\"text-align:center !important;\">
														<td> {$row['gasID']} </td>
														<td> {$row['gasType']} {$row['gasName']} </td>
														<td> {$row['quantity']} </td>
														<td> {$row['deliveryDate']} </td>
														<td style=\"font-size:13; padding-right:-10; padding-left:-10\">
															<a style=\"cursor: pointer;\" href='available-cylinders.php?gasID={$row['gasID']}&gasName={$row['gasName']}&gasType={$row['gasType']}&orderDetailsID={$row['orderDetailsID']}'>View Available Cylinders</a>
														</td>
														<td style=\"font-size:13; padding-right:-10; padding-left:-10\">
															<a style=\"cursor: pointer;\" href='assigned-cylinders.php?gasID={$row['gasID']}&gasName={$row['gasName']}&gasType={$row['gasType']}&orderDetailsID={$row['orderDetailsID']}'>View Assigned Cylinders</a>
														</td>
													</tr>
												";
											}
										?>
								</tbody>
							</table>
						</div>

						<!-- Button trigger modal -->
						<center>
							<button type="button" class="btn" data-toggle="modal" data-target="#viewCylinders">View All Assigned Cylinders</button>
							<button type="button" class="btn btn-primary disabled prepareReceipt" id="prepareDRButton" onload="enableButton()">Prepare Delivery Receipt</button>
						</center>

						<!-- Modal for View Cylinders -->
						<div class="modal fade" id="viewCylinders" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							<div class="modal-dialog" role="document">
						    	<div class="modal-content">
						      		<div class="modal-header">
						        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						        		<h4 class="modal-title" id="myModalLabel">Assigned Cylinders for Order <?php echo $_SESSION['selectedOrder'];?></h4>
						      		</div>

						      		<div class="modal-body">
						      			<table id="modalTable" class="hover stripe cell-border">
							      			<thead>
												<tr>	
													<?php
														$result = mysqli_query($dbc,getGasOrders($selectedOrder));
														while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
															echo "<td style='text-align:center'> {$row['gasType']} {$row['gasName']} </td>";
															$columnList[] = $row['gasID'];
														}

													?>
												</tr>
											</thead>
											<tbody>	
												<?php
													$result = mysqli_query($dbc,getAllAssignedCylinders($selectedOrder));
													while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
														echo "<tr style='text-align:center'>";
															$index = 0;
															foreach($columnList as $gasColumn) {
																if (strcmp($row['gasID'], $gasColumn) == 0) echo "<td> {$row['cylinderID']} </td>";
																else echo "<td></td>";
																$index++;
															}
														echo "</tr>";
													}
												?>
											</tbody>
						      			</table>
						      		</div>

									<div class="modal-footer">
										<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
							    	</div>

							      </form>
						    </div>
						  </div>
						</div>

					</div>
			</div>
		</div>



		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		
		<script> 
			$(document).ready(function(){
				$('#Table').DataTable();
				$('#modalTable').DataTable();
			});
			$('#Table').DataTable({
				"order": [],
			    "columnDefs": [ {
			    "paging": false,
			      "targets"  : [4,5],
			      "orderable": false,
				}]
			});
		</script>

		<script>
			$(function enableButton() {
				<?php
					$isEnabled = true;
					$result = mysqli_query($dbc,getOrderDetails($selectedOrder));

					while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
						$orderDetails = mysqli_query($dbc,getQuantityOrdered($row['orderDetailsID']));
						$orderDetailsResult = mysqli_fetch_array($orderDetails,MYSQLI_ASSOC);
						$quantityNeeded = $orderDetailsResult['quantity'] - $orderDetailsResult['cylinderCount'];
						if ($quantityNeeded > 0) $isEnabled = false;
					}
				?>

				var isEnabled = <?php echo $isEnabled; ?>;
				var buttonClasses = document.getElementById("prepareDRButton").classList;
				if (isEnabled == 1) {
					buttonClasses.remove("disabled");
				}
			})
		</script>
	</body>


</html>