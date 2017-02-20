<?php
$timestamp = NULL;
$message = NULL;

	session_start();
	
	$userType = $_SESSION['userTypeID'];
	if ($userType != 102) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}
?>

</!DOCTYPE html>
<html>
	<head>
		<title>Sales - Sales and Marketing </title>
		<link rel="stylesheet" href="CSS/dashboard.css">
		<link rel="stylesheet" href="CSS/miggy.css">
		<link rel="stylesheet" href="CSS/patrick.css">
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
	</head>
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		
		<script> 
		$(document).ready(function(){
			$('#table').DataTable();
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
							<a class="pure-menu-link"> Employees </a>
							<ul class="dropdown">
								<li>
									<a href="view-employees.php" class="pure-menu-link"> View Employees </a>
								</li>
								<li>
									<a href="create-user-sales-and-marketing.php" class="pure-menu-link"> Create New Employee</a>
								</li>
								<li>
									<a href="edit-user-details-SAM.php" class="pure-menu-link"> Edit Employee Details</a>
								</li>
							</ul>
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
					<!-- TITLE -->
					<div class="page-title-container">
						<p class="title"> Sales Report <a href='report-sales.php' class="a-href-right" style="font-size:20px"> Return to Initial Report <a></p>
					</div>

					<!-- TIMESTAMP FOR REPORT -->
					<div class="divider">
						<?php 
							date_default_timezone_set('Asia/Manila');
							$timestamp = date("F j, Y // g:i a");
							echo '<b>' .$timestamp. '</b>';
						?>
						<?php
				
						if(isset($_POST['show-report'])){
							
							if (empty($_POST['start-date'])){
								$_SESSION['start-date']=FALSE;
								$message='You forgot to enter the start date';
							}else
								$_SESSION['start-date']=$_POST['start-date'];
							if (empty($_POST['end-date'])){
								$_SESSION['end-date']=FALSE;
								$message='You forgot to enter the end date';
							}else
								$_SESSION['end-date']=$_POST['end-date'];
							if(!empty($_POST['start-date']) && !empty($_POST['end-date'])){
								if($_POST['start-date'] > $_POST['end-date'] ){
								$message='End Date must be larger than Start Date!';
								}
							}
						}
						else{
							$SESSION['start-date'] = null;
							$SESSION['end-date'] = null;
						}			
						?>
					</div>

					<!-- ERROR MESSAGE CONTAINER -->
					<div class="error-message-container" align="center">
						<?php
							if (isset($message)) echo $message;
						?>
					</div>
					
					<?php 
						if(isset($_GET['IDHold'])){
							$IDHold = $_GET['IDHold'];	
						}

						echo "<b> Order Number: {$IDHold} </b>";


							require_once('/pentagas-connect.php');
							$query = "SELECT * from cylinders c
														join cylinderstatus s on c.cylinderStatusID = s.cylinderStatusID
														join gastype g on c.gastypeID = g.gastypeID
														join orderdetails o on c.cylinderID = o.cylinderID
														join orders r on o.orderID = r.orderID
														where r.orderID = {$IDHold}
														and orderDate >= '{$_SESSION['start-date']}'
														and orderDate <= '{$_SESSION['end-date']}'
														
									  " ;		  
							$result = mysqli_query($dbc,$query);
							echo '<table id ="table" class="pure-table pure-table-horizontal";>
										<thead> 
											<th align="center">Cylinder ID </th>
											<th align="center">Gas Type</th>
											<th align="center">Sale</th>
										</thead>';

							if(!isset($message) && isset($_SESSION['end-date']) && isset($_SESSION['start-date'])){

								$index = 1;

								while($row=mysqli_fetch_array($result,MYSQL_ASSOC)){
								$query1="SELECT GAP.gasTypeID, price, auditDate FROM gasType GT JOIN gaspricingaudit GAP ON GT.gasTypeID = GAP.gasTypeID 
																								  WHERE GT.gasTypeID = '{$row['gasTypeID']}' 
																								  ORDER BY 3 DESC 
																								  LIMIT 1";

								// $testQuery = "SELECT gasName, price, auditDate FROM gasType GT JOIN gaspricingaudit GAP ON GT.gasTypeID = GAP.gasTypeID WHERE GT.gasTypeID = '$gasTypeID' ORDER BY 3 DESC LIMIT 1";
								$result1 = mysqli_query($dbc,$query1);
								$row1=mysqli_fetch_array($result1,MYSQL_ASSOC);

									if ($index % 2 == 0) {
										echo "<tr class=\"pure-table-odd\">

										<td width=\"20%\"><div align=\"center\">{$row['cylinderID']}</td> 
										<td width=\"20%\"><div align=\"center\">{$row['gasName']}
										<td width=\"20%\"><div align=\"center\">{$row1['price']}

										</div></td>


										</tr>";
									}

									else {
										echo "<tr>

										<td width=\"20%\"><div align=\"center\">{$row['cylinderID']}</td> 
										<td width=\"20%\"><div align=\"center\">{$row['gasName']}
										<td width=\"20%\"><div align=\"center\">{$row1['price']}

										</div></td>


										</tr>";
									}
								

								}
								
							}
						?>

				
				</div>
			</div>
		</div>
	</body>
</html>