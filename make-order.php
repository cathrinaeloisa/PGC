<?php
	require_once('/pentagas-connect.php');
	session_start();

	$userType = $_SESSION['userTypeID'];
	if ($userType != 103) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	$delivery_date = NULL;
	$order_date = NULL;
	$customerID = NULL;
	$message = NULL;
	$order_gas = array();
	$order_quantity = array();
	$order_status = NULL;
	$array_size = 0;
	$orderID = NULL;

	
	$customerID = $_SESSION['customerID'];
	$validToAdd = TRUE;
	$addedOrder = FALSE;

	if (isset($_POST['place-order'])) {
		$order_quantity = $_POST['quantity'];
		$order_gas = $_POST['order_array'];
		
		if (in_array(0, $order_quantity)) {
    		$message='Please insert quantity to order.';
		}
		else {
			if (empty($_POST['delivery-date'])){
				$delivery_date=FALSE;
				$message='You forgot to enter the delivery date!';
			} 
			else {
				$ddate = date_create($_POST['delivery-date']);
				$delivery_date = date_format($ddate, 'Y-m-d');
				$order_date = date('Y-m-d');

				if ($delivery_date < $order_date) {
					$message='Delivery date not accepted!';
				}

				else {
					$array_size = sizeof($order_quantity);
					$noGas = NULL;				
					for ($i = 0; $i < sizeof($order_gas); $i++) {
						// require_once('/pentagas-connect.php');
						// COUNTS TOTAL NUMBER OF GASES FOR ORDERED GAS TYPE
						$query_gases = "SELECT gasName, c.gasTypeID, count(cylinderID) as totalCylinders FROM cylinders C JOIN gasType G ON C.gasTypeID = G.gasTypeID WHERE c.gasTypeID = {$order_gas[$i]} AND cylinderstatusID = '401' GROUP BY c.gasTypeID";
						$result_gases = mysqli_query($dbc, $query_gases);
					 	$row = mysqli_fetch_array($result_gases, MYSQLI_ASSOC);
					 	
					 	// CHECK IF QUANTITY ORDERED IS VALID FOR AVAILABLE CYLINDERS
					 	if ($order_quantity[$i] > $row['totalCylinders']) {
					 		$getName = "SELECT gasName FROM gasType WHERE gasTypeID = {$order_gas[$i]}";
							$nameResult = mysqli_query($dbc, $getName);
							$nameRow = mysqli_fetch_array($nameResult, MYSQLI_ASSOC);
					 	
					 		if (sizeof($order_gas) >= 2) {
						 		$noGas .= " {$nameRow['gasName']},";
						 		$message="Not enough cylinders for" .$noGas. "!";
						 	}
						 	else $message="Not enough cylinders for {$nameRow['gasName']}!";
					 		$validToAdd = FALSE;
					 	}
					}

					if ($validToAdd) {
						$array_size = sizeof($order_gas);
						// require_once('/pentagas-connect.php');

						$query_orders = "INSERT INTO orders (userID, orderStatusID, customerID, orderDate) VALUES ('{$_SESSION['userID']}','802','{$customerID}', '{$order_date}')";
						$result_orders = mysqli_query($dbc, $query_orders);
						if ($result_orders) {
							$addedOrder = TRUE;
						}
						else $message = "Error creating order.";
					}

				}
			}
		}
	}

	if ($addedOrder) {		
		for ($i = 0; $i < $array_size; $i++) {
			$get_gas = $order_gas[$i];
			
			//ORDER DETAILS
			for ($qty = 1; $qty <= $order_quantity[$i]; $qty++) {
				//SELECT AVAILABLE CYLINDER FOR GAS TYPE
				$query1="SELECT cylinderID, cylinderstatusID FROM cylinders WHERE cylinderstatusID = '401' AND gasTypeID = $get_gas LIMIT 1";
				$result1 = mysqli_query($dbc, $query1);

				if ($result1) {
					//SUCCESSFUL SELECTING OF CYLINDER
					$row1 = mysqli_fetch_array($result1,MYSQLI_ASSOC);
					$selectedCylinder = $row1['cylinderID'];

					//SELECT ORDER ID 
					$select_orderID ="SELECT orderID FROM orders ORDER BY orderID DESC LIMIT 1";
					$result_orderID = mysqli_query($dbc, $select_orderID);
					
					if ($result_orderID) {
						//SUCCESSFUL SELECTING OF CORRESPONDING ORDER
						$row_orderID = mysqli_fetch_array($result_orderID, MYSQLI_ASSOC);
						$selectedOrder = $row_orderID['orderID'];

						$query2 ="INSERT INTO orderdetails (cylinderID, orderID, deliveryDate) VALUES ('{$selectedCylinder}', '{$selectedOrder}', '{$delivery_date}')";
						$result2 = mysqli_query($dbc, $query2);

						if ($result2) {
							$query3 ="UPDATE cylinders SET cylinderstatusID = '406' WHERE cylinderID = {$row1['cylinderID']}";
							$result3 = mysqli_query($dbc, $query3);

							$_SESSION['message']="Order and details added!";
							header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/view-orders.php");

						}
						else $message = "Error adding order details.";
					}
					else $message = "Error selecting order.";
				}
				else $message = "Error selecting cylinders.";
			}
		}
	}
?>

</!DOCTYPE html>
<html>
	<head>
		<title>Order Form - Billing Clerk</title>
		<link rel="stylesheet" href="CSS/dashboard.css">
		<link rel="stylesheet" href="CSS/miggy.css">
		<link rel="stylesheet" href="CSS/patrick.css">
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">

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
									<a href="view-customers.php" class="pure-menu-link highlighter"> Create New Order </a>
								</li>
								<li>
									<a href="cancel-order.php" class="pure-menu-link"> Cancel Order</a>
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

			<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
				<div class="content-container">
					<!-- TITLE -->
					<div class="page-title-container">
						<p class="title"> Order Form 
						<a href="view-customers.php" class="a-href-right"> Back </a>
					</div>
					
					<div class="divider">
						<div>
							<?php 
								if (isset($message)){
								 echo '<b><font color="red">'.$message.'</font></b>';
								}
							?>
						</div>
					</div>



					<div class="container-left">
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="pure-form pure-form-aligned">
							<div class="order-form-div">
								<fieldset>
									<?php 
										// require_once('/pentagas-connect.php');
										// RETRIEVE CUSTOMER DETAILS
										$query_customer="SELECT * FROM customers WHERE customerID = {$_SESSION['customerID']}";
										$result_customer=mysqli_query($dbc,$query_customer);
										$row_customer=mysqli_fetch_array($result_customer,MYSQLI_ASSOC);

										// FOR TABLE OF AVAILABLE CYLINDERS
										//echo $row_gas;
										$query_gas="SELECT * FROM gastype ORDER BY gasName ASC";
										$result_gas=mysqli_query($dbc,$query_gas);

										$query_totalcylinders="SELECT gasName, COUNT(gasName) AS numGas FROM cylinders C JOIN gastype G ON c.gasTypeID = g.gasTypeID 
											WHERE cylinderstatusID = 401
											GROUP BY gasName ";
										$result_totalcylinders=mysqli_query($dbc,$query_totalcylinders);
									?>

									<!-- SHOW CUSTOMER DETAILS -->
									<div>
										<div class="pure-control-group">
											<label for="customer-name" style="font-size:20px"> Customer Name: </label> <?php echo "<b><font style=\"font-size:20px\" size=\"75\"> {$row_customer['name']} </font></b>"?>
										</div>
										
										<div class="pure-control-group">
											<label for="delivery-address" style="font-size:20px"> Delivery Address: </label> <?php echo "<b><font style=\"font-size:20px\"> {$row_customer['address']}</font></b>"?>
										</div>

										<div class="pure-control-group">
											<label for="contact-num" style="font-size:20px"> Contact Number: </label> <?php echo "<b><font style=\"font-size:20px\"> {$row_customer['contactNum']}</font></b>"?>
										</div>
									</div>
									<!-- END CUSTOMER DETAILS -->

									<br>
									<br>

									<div class="table-margin-left" style="margin-bottom:15px; margin-top:15px">
										<label for="delivery-date"> Delivery Date: </label>
										<input type="date" name="delivery-date"/>
									</div>
									<div>
										<table id="dataTable" class='pure-table pure-table-horizontal table-width-order table-margin-left'>
										<?php
											echo "<tr>
													<td> <select name=\"order_array[]\">";
												while($row=mysqli_fetch_array($result_gas,MYSQLI_ASSOC)) {
												echo "<option value=\"{$row['gasTypeID']}\"> {$row['gasName']} </option>";
												}
											echo "<td><input type=\"number\" min=\"0\" name=\"quantity[]\"></td></tr>";
										?>
										</table>
										
										<!---<table class="pure-table pure-table-horizontal table-width-order table-margin-right-order">!
										<?php
											//echo "<thead>
												//	<th> Gas Name </th>
												//	<th> Available </th> </thead>";
												//while($row_totalcylinders=mysqli_fetch_array($result_totalcylinders,MYSQLI_ASSOC)) {
											//echo "<tr> <td align=\"center\"> {$row_totalcylinders['gasName']} </div></td>
												//	<td align=\"right\"> {$row_totalcylinders['numGas']}</div></td></tr>";
										?>-->
									</div>
									<br>
									<a onclick="addRow('dataTable')" class="table-margin-left add-row-button"> Add Row </a>	
									<br>
									<br>
									
									<center>
										<input type="submit" name="place-order" class="place-order-button" value="Place Order"/>
									</center>
									
								</fieldset>
							</div>

						</form>	
					</div>

					<div class="container-right"> <!--- CATHY PLS ADD THIS CLASS THANK YOU !-->
						<center>
						<table class="pure-table pure-table-horizontal table-width">
							<?php
								echo "<thead>
										<th> Gas Name </th>
										<th> Available </th> </thead>";
										while($row_totalcylinders=mysqli_fetch_array($result_totalcylinders,MYSQLI_ASSOC)) {
											echo "<tr> <td align=\"center\"> {$row_totalcylinders['gasName']} </div></td>
											<td align=\"right\"> {$row_totalcylinders['numGas']}</div></td></tr>";
										}
							?>
						</table>
						</center>
					</div>
					

				</div>
			</div>
		</div>

		<script language="javascript">
			function addRow(tableID) {

			var table = document.getElementById(tableID);

			var rowCount = table.rows.length;
			var row = table.insertRow(rowCount);

			var colCount = table.rows[0].cells.length;

			for(var i=0; i<colCount; i++) {

				var newcell	= row.insertCell(i);

				newcell.innerHTML = table.rows[0].cells[i].innerHTML;
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