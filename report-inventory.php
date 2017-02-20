<?php
$timestamp = NULL;
$message = NULL;
	require_once('pentagas-connect.php');
	session_start();
	
	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}
?>

</!DOCTYPE html>
<html>
	<head>
		<title>Inventory - Administrative </title>
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
		
		
		<script> 
		$(document).ready(function(){
			$('#outTable').DataTable();
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
					<ul>
						<li>
							<a href="administrative-manager-home.php" class="pure-menu-link"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a href="view-employees.php" class="pure-menu-link"> Employees </a>
						</li>
						<li>
							<a href="view-gases.php" class="pure-menu-link">Gases</a>
						</li>
						<li>
							<a href="view-cylinders.php" class="pure-menu-link"> Cylinders</a>
						</li>

						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul>
								<li>
									<a href="report-inventory.php" class="pure-menu-link highlighter"> Inventory Report</a>
								</li>
								<li>
									<a href="report-cylinder-status.php" class="pure-menu-link"> Cylinder Status Report</a>
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
						<p class="title"> Inventory Report </p>
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

					<!-- DATE RANGE CONTAINER -->
					<div class="divider" align="center" style="margin-bottom:20px">
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							<div>
								<label for="start-date"> Start Date: </label>
								<input type="date" name="start-date" value ="<?php if (isset($_POST['start-date'])) echo $_POST['start-date']; ?>" /> &nbsp&nbsp
								<label for="end-date"> End Date: </label>
								<input type="date" name="end-date" value ="<?php if (isset($_POST['end-date'])) echo $_POST['end-date']; ?>" />
							</div>
							<div>
								<input style="margin-top:10px" type="submit" name="show-report" value="Show Report"/>
							</div>
						</form>
					</div>

					<div class="divider"></div>

					<div>
						<div class="container-left">
							<center>
								
								<div class= "divider"></div>
									<div style="margin-bottom:10px"><b>CYLINDERS ON HAND</b></div>
									<table id="table" class="pure-table pure-table-horizontal" width="100%">
										<thead> 
											<th align="center">Gas Name </th>
											<th align="center">Number of Cylinders</th>	
										</thead>
							
									<?php 
										if(!isset($message) && isset($_SESSION['end-date']) && isset($_SESSION['start-date'])){
											
											//where (pickupDate = null or pickupDate > $_SESSION['endDate']) AND deliveryDate >= $_SESSION['startDate'] or deliveryDate <= $_SESSION['endDate']
											// total ng lahat ng cylinders 
											// require_once('../pentagas-connect.php');
											$query = "SELECT gasname, COUNT(gasname) as numOfGas, c.cylinderStatusID ,  g.gastypeID FROM `cylinders` c 
													  join gastype g on c.gastypeid = g.gastypeid 
													  group by c.gastypeid";	
													  
											$result = mysqli_query($dbc,$query);

											// minus sa lahat ng nasa customer during that date // meaning check the delivery date 

											// require_once('../pentagas-connect.php');
											$customquery = "SELECT gasname , COUNT(gasname) as numOfGas FROM orders o
																										join orderdetails d on o.orderID = d.orderID
																										join cylinders c on d.cylinderID = c.cylinderID
																										join gastype g on c.gastypeID = g.gastypeID
																										where pickupDate = null 
																										  	    or pickupDate > '{$_SESSION['end-date']}'
																												AND deliveryDate >= '{$_SESSION['start-date']}'
																												and deliveryDate <= '{$_SESSION['end-date']}'
																												and orderDate >= '{$_SESSION['start-date']}'
																												and orderDate <= '{$_SESSION['end-date']}'
																										group by c.gastypeid 
																										
																										";	
													  
											$customresult = mysqli_query($dbc,$customquery);
											// minus sa lahat ng no longer in use "not sure to"

											while($row=mysqli_fetch_array($result,MYSQL_ASSOC) ){
												$customrow=mysqli_fetch_array($customresult,MYSQL_ASSOC);
												if($row['gasname'] == $customrow['gasname']){
													$equals = $row['numOfGas'] - $customrow['numOfGas'];
													echo "<tr>
													<td><a href='gas-page.php?gasHold={$row['gastypeID']}'>".$row['gasname']."</td>
													<td align=\"center\">{$equals}</td>
													</tr>";
												}
												else {
													echo "<tr>
													<td><a href='gas-page.php?gasHold={$row['gastypeID']}'>".$row['gasname']."</td>
													<td align=\"center\">{$row['numOfGas']}</td>
													</tr>";	
												}			
											}
											}
									?>
									</table>
							</center>
						</div>

						<div class="container-right">
							<center>
								
								<div class= "divider"></div>
									<div style="margin-bottom:10px"><b>CYLINDERS OUT OF HAND</b></div>
									<?php	
										// OUT OF HAND TABLE
										//where (pickupDate = null or pickupDate > $_SESSION['endDate']) AND deliveryDate >= $_SESSION['startDate'] or deliveryDate <= $_SESSION['endDate']
										// require_once('../pentagas-connect.php');
										if (isset($_SESSION['end-date']) && isset($_SESSION['start-date'])) {
											$query = "Select o.customerID, name, count(d.cylinderID) as numOfGas from orders o join orderdetails d on o.orderID = d.orderID
																   join cylinders c on c.cylinderID = d.cylinderID
											                       join gastype g on c.gasTypeID = g.gasTypeID
																   join customers u on o.customerID = u.customerID
																   		where pickupDate = null 
																		    or pickupDate > '{$_SESSION['end-date']}'
																			AND deliveryDate >= '{$_SESSION['start-date']}'
																			and deliveryDate <= '{$_SESSION['end-date']}'
																			and orderDate >= '{$_SESSION['start-date']}'
																			and orderDate <= '{$_SESSION['end-date']}'
																   group by o.customerID
													  " ;	
											$result = mysqli_query($dbc,$query);
										} else $message = "Please set date range.";
											echo '<table id ="outTable" class="pure-table pure-table-horizontal" width="100%">
														<thead> 
															<th width=\"20%\">Company Name </th>
															<th width=\"20%\">Number of Cylinders</th>
														
														</thead>';


										if(!isset($message) && isset($_SESSION['end-date']) && isset($_SESSION['start-date'])){
											$index = 1;
											while($row=mysqli_fetch_array($result,MYSQL_ASSOC)){

											if ($index % 2 == 0) {
												echo "<tr class=\"pure-table-odd\">

												<td width=\"20%\" ><div align=\"center\"> <a href='customer-report.php?customerHold={$row['customerID']}'>".$row['name']."</a> 
												</div></td>
												<td width=\"20%\"><div align=\"center\">{$row['numOfGas']}
												</div></td>
												</tr></table>";
											}

											else {
												echo "<tr>

												<td width=\"20%\" ><div align=\"center\"> <a href='customer-report.php?customerHold={$row['customerID']}'>".$row['name']."</a> 
												</div></td>
												<td width=\"20%\"><div align=\"center\">{$row['numOfGas']}
												</div></td>
												</tr></table>";
											}

											

											$index++;
												
											}
										
										}
									?>
							</center>
						</div>
						
					</div>


				</div>
			</div>
		</div>
	</body>
</html>