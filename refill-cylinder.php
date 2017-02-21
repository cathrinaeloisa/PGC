<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 106) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	if (isset($_POST['cylinders']))
	{
		foreach($_POST['cylinders'] as $check) {
			$update ="UPDATE cylinders SET cylinderStatusID= 401 where cylinderID = '{$check}'";
			$result = mysqli_query($dbc,$update);

			if ($result) {
				$date = date('Y-m-d');
				$auditInsert = "INSERT INTO cylinderRefillAudit (cylinderID,auditDate) VALUES ('{$check}','{$date}')";
				$insertResult = mysqli_query($dbc,$auditInsert);
			}

			else $message = "The system encountered an error.";
		}

		$message = "Status updated!";
	}

	$cylinderList = " SELECT c.cylinderID, cus.name, dd.deliverydate, dd.pickedupdate, gt.gasName, gt.gasType
						FROM cylinders c JOIN gasType gt ON c.gasID = gt.gasID
										 JOIN orderDetails od ON gt.gasID = od.gasID
										 JOIN orders o ON o.orderID = od.orderID
										 JOIN customers cus ON cus.customerID = o.customerID
										 JOIN deliveryDetails dd ON od.orderDetailsID = dd.orderDetailsID
					   WHERE c.cylinderStatusID = 402
					GROUP BY c.cylinderID";


?>


<html>
	<head>
		<title>Refill Cylinder</title>
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
							<a href="production-manager-home.php" class="pure-menu-link"> Home </a>
						</li>
						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>
						<li>
							<a href="refill-cylinders.php" class="pure-menu-link highlighter"> Cylinders</a>
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
						<p class="title"> Refill Empty Cylinders </p>
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
							<table class="hover stripe cell-border" id="Table">
								<thead>
									<tr>
										<th style="text-align:center"><input type="checkbox" name="select-all" id="select-all"></th>
										<th style="text-align:center !important; font-size:15"> Cylinder Number </th>
										<th style="text-align:center !important; font-size:15"> Gas Name </th>
										<th style="text-align:center !important; font-size:15"> Last Received From </th>
										<th style="text-align:center !important; font-size:15"> Date Picked Up </th>
									</tr>
								</thead>

								
								<?php 
									$result = mysqli_query($dbc,$cylinderList);
									while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
										echo "<tr style=\"text-align:center\">
												<td> 
													<input type='checkbox' name='cylinders[]' value={$row['cylinderID']}>
												</td>
												<td> {$row['cylinderID']} </td>
												<td> {$row['gasType']} {$row['gasName']} </td>
												<td> {$row['name']} </td>
												<td> {$row['pickedupdate']} </td>
											</tr>";
									}
				            	?>

							</table>

							<input type="submit" class="btn btn-primary" name="submit" value="Refill Cylinders"> 
						</div>
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
			      "targets"  : [0],
			      "orderable": false,
				}]
			});

			$('#select-all').click(function(event) {   
			    if(this.checked) {
			        // Iterate each checkbox
			        $(':checkbox').each(function() {
			            this.checked = true;                        
			        });
			    }
			});
		</script>
	</body>
</html>




