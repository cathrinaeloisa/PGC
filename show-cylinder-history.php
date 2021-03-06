<?php
	require_once('pentagas-connect.php');
	session_start();

	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}


	// LIST OF CUSTOMERS
	$cylinderList = "SELECT * FROM cylinders";
	$result_cylinderList= mysqli_query($dbc, $cylinderList);

	// LIST of ORDER DETAILS
	$orderdetailList = "SELECT * from customers c
								join orders o on c.customerID = o.customerID
								join orderdetails od on o.orderID = od.orderID
								join cylinders cy on od.gasID=cy.gasID
								join gastype gt on od.gasID=gt.gasID
								join deliverydetails dd on od.orderDetailsID=dd.orderDetailsID
								group by o.orderID
				" ;
	$result_orderdetailList=mysqli_query($dbc, $orderdetailList);


?>

<!DOCTYPE html>
<html>
<head>
	<title> Cylinder Transaction Record</title>
	<link rel="stylesheet" href="CSS/dashboard.css" >
	<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
	<link rel="stylesheet" href="CSS/bootstrap.min.css">

	<script src="CSS/jquery.min.js"></script>
	<script src="CSS/bootstrap.min.js"></script>

</head>
<body>
	<div class="pure-g">
		<div class="pure-u-5-24 sidebar-container">
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
					<ul>
						<li>
							<a href="administrative-manager-home.php" class="pure-menu-link"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a href="view-employees.php"  class="pure-menu-link"> Employees </a>
						</li>
						<li>
							<a href="view-gases.php" class="pure-menu-link">Gases</a>
						</li>
						<li>
							<a href="view-cylinders.php" class="pure-menu-link"> Cylinders</a>
						</li>
						<li>
							<a href="cylinder-history.php" class="pure-menu-link highlighter">Cylinder Transaction Records</a>
						</li>
						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul>
								<li>
									<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
								</li>
				                <li>
				                  <a href="report-cylinder-status.php" class="pure-menu-link">Daily Cylinder Status Report</a>
				                </li>
							</ul>
						</li>
						<li>
							<a href="logout.php" class="pure-menu-link"> Logout </a>
						</li>
					</ul>
				</div>
			</div>
			<!-- END SIDEBAR -->
		</div>

		<div class="pure-u-6-24"></div>
		<div class="pure-u-17-24">
			<div class="content-container">
				<!-- TITLE -->
				<div class="row">
					<div class="col">
						<ol class="breadcrumb">
							<li><a href="cylinder-history.php">Back</a></li>
							<li class="active">Transaction Record for Cylinder <?php echo $_POST['cylinder']; ?></li>
							<?php
								date_default_timezone_set('Asia/Manila');
								$timestamp = date("F j, Y // g:i a");
								echo '<b class="col-sm-offset-5">' .$timestamp. '</b>';
							?>
						</ol>
					</div>
				</div>

				<?php
					$cyID=null;
					if(isset($_POST['showReport'])){
						$cyID = $_POST['cylinder'];
					}
					$_SESSION['curdate']= date("Y-m-d");
					$_SESSION['silindro']= $cyID;
					$orderdetailList = "SELECT * from customers c
											join orders o on c.customerID = o.customerID
											join orderdetails od on o.orderID = od.orderID
											join cylinders cy on od.gasID=cy.gasID
											join gastype gt on od.gasID=gt.gasID
											join deliverydetails dd on od.orderDetailsID=dd.orderDetailsID
											where pickedupdate<='{$_SESSION['curdate']}'
												and cy.cylinderID='{$_SESSION['silindro']}'
												" ;
					$result = mysqli_query($dbc,$orderdetailList);
				?>

				<div class="row">
					<table class="table table-bordered table-striped" id="Table">
						<thead>
							<th style="text-align:center">Customer Name</th>
							<th style="text-align:center">Date Delivered</th>
							<th style="text-align:center">Date Picked Up</th>
						</thead>

						<?php
							if(isset($_POST['showReport'])){
								while($row=mysqli_fetch_array($result)){
									echo "<tr>
									<td width=\"20%\" style=\"text-align:center\">{$row['name']}</td>
									<td width=\"20%\" style=\"text-align:center\">{$row['deliveryDate']}</td>
									<td width=\"20%\" style=\"text-align:center\">{$row['pickedupdate']}</td>
									</tr>";
								}
							}
						?>
					</table>
				</div>
			</div>
	</div>
</div>
</body>

	<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	
	<script> 
	$(document).ready(function(){
		$('#Table').DataTable({
			paging: false,
			searching: false,
			ordering: false,
		});
	});
	</script>

</html>
