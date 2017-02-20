<?php
	require_once('pentagas-connect.php');
	session_start();

	$userType = $_SESSION['userTypeID'];
	$userID = $_SESSION['userID'];
	if ($userType != 103) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	$contactPerson = NULL;
	$message = NULL;
	// LIST OF CUSTOMERS
	$customerList = "SELECT * FROM CUSTOMERS ORDER BY NAME ASC";
	$result_customerList = mysqli_query($dbc, $customerList);

	// LIST OF GASES
	$gasList = "SELECT * FROM GASTYPE ORDER BY GASNAME ASC";
	$result_gasList = mysqli_query($dbc, $gasList);

	function getAvailableCylinders ($gasID) {
		return $thisquery = "SELECT COUNT(CYLINDERID) AS 'CYLINDERCOUNT'
							 FROM CYLINDERS 
				   			 WHERE CYLINDERSTATUSID = 401 
				    		 AND GASID = '{$gasID}'";
	}

	function getEmptyCylinders ($gasID) {
		return $thisquery = "SELECT COUNT(CYLINDERID) AS 'CYLINDERCOUNT'
							 FROM CYLINDERS
							 WHERE CYLINDERSTATUSID = 402
							 AND GASID = '{$gasID}'";
	}

	if (isset($_POST['placeOrder'])) {
		$selectedCustomer = $_POST['customer'];
		$orderedGases = $_POST['gases'];
		$orderedQuantity = $_POST['quantity'];
		// $deliveryDate = $_POST['deliverydate'];
		$orderDate = date('Y-m-d');
		
		if (empty($_POST['contactPerson'])) {
			$message = 'Please indicate contact person.';
		}

		else {
			$contactPerson = $_POST['contactPerson'];
		}

		// for ($i = 0; $i < sizeof($orderedGases); $i++) {

		// 	if ($deliveryDate[$i] < $orderDate) {
		// 		$message = "Delivery date should be later than the order date.";
		// 	}

		// 	else {
		// 		$deliveryDate = $deliveryDate[$i];
		// 		echo $deliveryDate;
		// 	}
		// }

		for ($i = 0; $i < sizeof($orderedGases); $i++) {
			if (substr($orderedGases[$i],0,1) == 'M') {
				$type[$i] = substr($orderedGases[$i], 0, 7);
				$name[$i] = chop(substr($orderedGases[$i], 8, (strlen($orderedGases[$i]))));
			} 

			else {
				$type[$i] = substr($orderedGases[$i], 0, 9);
				$name[$i] = chop(substr($orderedGases[$i], 10, (strlen($orderedGases[$i]))));
			}
		}

		if(!isset($message)) {
			$customer = "SELECT CUSTOMERID FROM CUSTOMERS WHERE NAME = '{$selectedCustomer}'";
			$resultCustomer = mysqli_query($dbc, $customer);
			$rowCustomer = mysqli_fetch_array($resultCustomer, MYSQLI_ASSOC);

			$queryOrderID = "SELECT ORDERID FROM ORDERS ORDER BY ORDERID DESC LIMIT 1";
			$resultOrderID = mysqli_query($dbc, $queryOrderID);
			$rowOrderID = mysqli_fetch_array($resultOrderID, MYSQLI_ASSOC);

			$date = date('Y-m');
			$order_date = str_replace('-','', $date);
			$orderNumber = sprintf("%04d", substr($rowOrderID['ORDERID'],7) + 1);
			$orderID = $order_date."-".$orderNumber;
			$message = "Order ID: $orderID";

			$queryOrders = "INSERT INTO ORDERS (ORDERID, USERID, ORDERSTATUSID, CUSTOMERID, ORDERDATE, CONTACTPERSON) VALUES ('{$orderID}', '{$userID}', '802', '{$rowCustomer['CUSTOMERID']}', '{$orderDate}', '{$contactPerson}')";
			$resultOrders = mysqli_query($dbc, $queryOrders);

			if($resultOrders) {
				$message = "Order added.";

				$queryLatestOrder = "SELECT ORDERID FROM ORDERS ORDER BY ORDERID DESC LIMIT 1";
				$resultLatestOrder = mysqli_query($dbc, $queryLatestOrder);
				$rowLatestOrder = mysqli_fetch_array($resultLatestOrder, MYSQLI_ASSOC);

				for ($i = 0; $i < sizeof($orderedGases); $i++) {
					$latestOrder = str_replace('-','',$rowLatestOrder['ORDERID']);
					$latestOrderNumber = sprintf("%02d", substr($rowLatestOrder['ORDERID'], 11) + ($i + 1));
					$orderDetailsID = $latestOrder.'-'.$latestOrderNumber;

					echo $type[$i];
					echo $name[$i];
					$selectGasID = "SELECT GASID FROM GASTYPE WHERE GASTYPE LIKE '{$type[$i]}' AND GASNAME LIKE '{$name[$i]}'";
					$resultGasID = mysqli_query($dbc, $selectGasID);
					$rowGasID = mysqli_fetch_array($resultGasID, MYSQLI_ASSOC);
					
					if ($resultGasID) {
						echo $orderDetailsID;
						echo "ORDER DETAILS <br>";
						echo $rowGasID['GASID'];
						echo "GAS ID <br>";
						echo $rowLatestOrder['ORDERID'];
						echo "LATEST ORDER <br>";
						echo $orderedQuantity[$i];
						echo "QUANTITY <br>";

						$queryOrderDetails = "INSERT INTO ORDERDETAILS (ORDERDETAILSID, GASID, ORDERID, QUANTITY) VALUES ('{$orderDetailsID}', '{$rowGasID['GASID']}', '{$rowLatestOrder['ORDERID']}', '{$orderedQuantity[$i]}')";
						$resultOrderDetails = mysqli_query($dbc, $queryOrderDetails);

						if ($resultOrderDetails) echo "YAHU";
						else echo "bakit";
					}

					else {
						$message = "Oh no!";
					}
				} 
			}

			else {
				$message = "Error adding order.";
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title> Order Form </title>
	<link rel="stylesheet" href="CSS/dashboard.css" >
	<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>

	<script src="jquery.min.js"></script>
	<script src="bootstrap.min.js"></script>
	<link rel="stylesheet" href="CSS/bootstrap.min.css">

	<link rel="stylesheet" href="CSS/miggy.css">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<body>
	<div class="pure-g">
		<div class="pure-u-5-24 sidebar-container">
			<div align="center">
				<div class="logo-container" align="center">
					<div>
						<img class="logo-edit" src="pentagon_png.png">
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
						<a href="view-customers.php" class="pure-menu-link"> Customers </a>
					</li>

					<!-- ORDERS DROPDOWN -->
					<li>
						<a class="pure-menu-link"> Orders </a>

						<ul>
							<li>
								<a href="view-orders.php" class="pure-menu-link"> View Orders </a>
							</li>

							<li>
								<a href="order-form.php" class="pure-menu-link highlighter"> Create New Order </a>
							</li>

							<li>
								<a href="cancel-order.php" class="pure-menu-link"> Cancel Order </a>
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
			
			<div class="row">
				<div class="col">
					<div class="page-header">
						<p class="title"> Order Form </p>
					</div>
				</div>
			</div>

			<div class="divider">
				<div>
					<?php
						if (isset($message)) {
							echo '<b><font color="red">' .$message. '</font></b>';
						}
					?>
				</div>
			</div>

			<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="form-horizontal">
				<div class="well well-lg">
					<div class="row">
						<div class="col">
							<div class="form-group">
								<label for="customer" class="col-sm-2 control-label"> Customer Name: </label>
									<div class="col-sm-5">
										<input type="text" placeholder="Choose a Customer" class="form-control" name="customer" list="customerName"/>
										<datalist id="customerName">
											<?php
												while ($row_customers=mysqli_fetch_array($result_customerList, MYSQLI_ASSOC)) {
													   echo "<option value=\"{$row_customers['name']}\">";							 
												}
											?>
										</datalist>
									</div>
									<a href="view-customers.php" style="float: left; margin-left: 10px" class="default"> Add New Customer </a>
							</div>

							<div class="form-group">
								<label for="contactPerson" class="col-sm-2 control-label"> Contact Person: </label>

								<div class="col-sm-5">
									<input type="text" class="form-control" name="contactPerson">
								</div>
							</div>
						</div>
					</div>

					<!-- <div class="row">
						<div class="col">
							<div class="order-form-div">
								<label style="float: left; margin-right: 10px"> Delivery Address: </label>
								<div class="col-xs-3">
									<input type="text" class="form-control input-sm" style="margin-top: -7px" disabled/>
								</div>
							</div>
						</div>
					</div> -->
				</div>

				<br>

				<div>
					<div class="row">
						<div class="col-lg-12 center-block" style="float:none;">
						<center>
							<table id="dataTable" class="table table-bordered">
								<thead>
									<tr>
										<th style="text-align: center !important"> Gas Name </th>
										<th style="text-align: center !important"> Quantity <br> <a onclick="return false" data-toggle="modal" data-target="#viewCylinders"> View Cylinders </a> </th>
										<th style="text-align: center !important"> Delivery Date </th>
									</tr>
								</thead>

								<tbody>
									<tr>
										<td class="col-sm-3">
											<input type="text" class="form-control" placeholder="Please select Gas" name="gases[]" list="gasName">
												<datalist id="gasName">
													<?php
														while ($row_gases=mysqli_fetch_array($result_gasList, MYSQLI_ASSOC)) {
												 		  echo "<option value=\"{$row_gases['gasType']} {$row_gases['gasName']}\">";							 
														}
													?>
												</datalist>
										</td>
										<td class="col-sm-2">
											<input type="number" class="form-control" min="1" name="quantity[]"> 
										</td>
										<td class="col-sm-2">
											<input type="date" class="form-control" name="deliverydate[]">
										</td>
									</tr>
								</tbody>
	    					</table>
	    				</center>
	    					<a style="float: right;" onclick="addRow('dataTable')"> Add Row </a>
	    					<br>
	    					<center><input type="submit" name="placeOrder" class="btn btn-default" value="Place Order"></center>
						</div>
					</div>
				</div>
			</form>

			<!-- MODAL -->
			<div class="modal fade" id="viewCylinders" tabindex="-1" role="dialog" aria-labelledby="modalLabel"> 
				<div class="modal-dialog" role="document">	
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="modalLabel"> Cylinder Inventory </h1>
						</div>

						<div class="modal-body">
							<form method="post" class="form-horizontal" id="requestCylinders">
								<table class="table table-bordered">
									<thead>
										<th style="text-align: center !important"> Gas Name </th>
										<th style="text-align: center !important"> Quantity Available </th>
										<th style="text-align: center !important"> Quantity Empty </th>
										<!-- <th style="text-align: center !important"> Quantity for Request </th> -->
									</thead>

									<tbody>
										<?php
											$gasList = "SELECT * FROM GASTYPE ORDER BY GASNAME ASC";
											$result_gasList = mysqli_query($dbc, $gasList);

											while ($row_gases = mysqli_fetch_array($result_gasList, MYSQLI_ASSOC)) {
												$getGasID = $row_gases['gasID'];

												$resultAvailable = mysqli_query($dbc, getAvailableCylinders($getGasID));

												while ($rowAvailable = mysqli_fetch_array($resultAvailable, MYSQLI_ASSOC)) {
													
													$resultEmpty = mysqli_query($dbc, getEmptyCylinders($getGasID));

													while ($rowEmpty = mysqli_fetch_array($resultEmpty, MYSQLI_ASSOC)) {
														echo "<tr>
																<td style=\"text-align: center !important\"> {$row_gases['gasType']} {$row_gases['gasName']} </td>
																<td style=\"text-align: center !important\"> {$rowAvailable['CYLINDERCOUNT']} </td>
																<td style=\"text-align: center !important\"> {$rowEmpty['CYLINDERCOUNT']} </td>
														      </tr>";
													}
												}
											}
										?>
									</tbody>
								</table>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal"> Close </button>
						        	<!-- <button type="submit" name="add-cylinder" class="btn btn-primary"> Request </button> -->
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script language="javascript">
		function addRow(tableID) {

		var table = document.getElementById(tableID);

		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);

		var colCount = table.rows[1].cells.length;

			for(var i=0; i<colCount; i++) {

				var newcell	= row.insertCell(i);

				newcell.innerHTML = table.rows[1].cells[i].innerHTML;
				//alert(newcell.childNodes);
				switch(newcell.childNodes[0].type) {
					case "newrow":
						newcell.childNodes[0].value = rowCount + 2 ;
						break;
					case "text":
						newcell.childNodes[0].value = "";
						break;
					case "checkbox":
						newcell.childNodes[0].checked = false;
						break;
					case "select-one":
						newcell.childNodes[0].selectedIndex = 0;
						break;
				}
			}
		}
	</script>
</body>
</html>