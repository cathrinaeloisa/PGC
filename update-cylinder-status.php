<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 104) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}
	$message=NULL;

	if (isset($_POST['submit'])){
		if (empty($_POST['newStatus'])){
			$message = "Please select new status";
		} else {
			// CHECK CURRENT STATUS
			$newStatus = $_POST['newStatus'];		
	
			//CHECK UPDATABLE STATUSES FOR CURRENT STATUS
			// echo $newStatus;
			if (isset($_POST['cylinders'])) {
				foreach ($_POST['cylinders'] as $cylinderNumber) {
					// GET CURRENT STATUS OF CYLINDERS THAT WERE SELECTED
					$query = "SELECT * FROM cylinders WHERE cylinderID = '{$cylinderNumber}'";
					$result = mysqli_query($dbc,$query);
					$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
					$selected = $row['cylinderStatusID']; //SELECTED CYLINDER STATUS
					$valid = TRUE;

					if ($selected == 407 || $selected == 408) { //Cylinder is lost or no longer in use
						$message = "Cylinder number $cylinderNumber can no longer be updated.";
						$valid = FALSE;
					}
					else if ($selected == $newStatus) {
						$message = "Current status is equal to new status";
						$valid = FALSE;
					}
					else if ($selected == 401 && $newStatus != 403) { //Available to Damaged or Out of Hand
						$message = "Cylinder/s can only be updated to Damaged.";
						$valid = FALSE;
					}
					else if ($selected == 403 && $newStatus != 404 && $newStatus != 407) { //Damaged to in repair/NLIU
						$message = "Cylinder/s can only be updated to In Repair or No Longer In Use.";
						$valid = FALSE;
					}
					else if ($selected == 404 && $newStatus != 405) { //In repair to repaired
						$message = "Cylinder/s can only be updated to Repaired.";
						$valid = FALSE;
					}
					else if ($selected == 405 && $newStatus == 401) { //Repaired cylinder
						$message = "Cylinder/s is not yet refilled.";
						$valid = FALSE;
					}
					else if ($selected == 405 && $newStatus != 401) { //Repaired cylinder
						$message = "Cylinder/s cannot be updated to chosen status.";
						$valid = FALSE;
					}
					else if ($selected == 406 && $newStatus != 408) { //Out of hand cylinder
						$message = "Cylinder/s cannot be updated at this time.";
						$valid = FALSE;
					}
					
					else {
						$query = "UPDATE cylinders SET cylinderStatusID = $newStatus WHERE cylinderID = $cylinderNumber";
						$result = mysqli_query($dbc,$query);
						if ($result) $message = "Status updated!";
						else $message = "Error updating status.";
					}	
				}
			}
			else {
				$message = "Please select cylinder/s to update.";
			}
		}
	} 
	
	$cylindersList = "SELECT * FROM cylinders NATURAL JOIN cylinderStatus NATURAL JOIN gasType WHERE cylinderStatusID <> 402";
	
?>

<html>
	<head>
		<title>Update Cylinder Status</title>
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
							<a href="cylinder-control-home.php" class="pure-menu-link"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a class="pure-menu-link"> Cylinders </a>
							<ul>
								<li>
									<a href="update-cylinder-status.php" class="pure-menu-link highlighter"> Update Cylinder Status </a>
								</li>
								<li>
									<a href="refill-cylinder.php" class="pure-menu-link"> Refill Cylinder </a>
								</li>
								<li>
									<a href="receive-cylinder.php" class="pure-menu-link"> Receive Cylinder </a>
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
					<div class="page-title-container">
						<p class="title"> Update Cylinder Status </p>
					</div>

					<div class="divider">
						<div>
							<?php 
								if (isset($message)){
									echo $message;
									$message = NULL;
								}
							?>
						</div>
					</div>

					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
						<div align="center">
							<table class="hover stripe cell-border" id="Table">
								<thead>
									<tr>
										<th> </th>
										<th style="text-align:center"> Cylinder Number </th>
										<th style="text-align:center"> Gas Type </th>
										<th style="text-align:center"> Gas Name </th>
										<th style="text-align:center"> Cylinder Status </th>
									</tr>
								</thead>

								
								<?php
									$result = mysqli_query($dbc,$cylindersList);
									while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
										echo "	<tr style=\"text-align:center\">
													<td> 
														<input type='checkbox' name='cylinders[]' value={$row['cylinderID']}>
													</td>
													<td> {$row['cylinderID']} </td>
													<td> {$row['gasType']} </td>
													<td> {$row['gasName']} </td>
													<td> {$row['cylinderStatusDescription']} </td>
												</tr>";
									}
								
								?>

							</table>
						</div>
					
						<br>
						<br>
						
						<div align="center">
							<p>Update status to: 
								<select name="newStatus">
									<option> Select... </option>
									<option value = "404"> In Repair </option>
									<option value = "405"> Repaired </option>
									<option value = "407"> No Longer in Use </option>
									<option value = "408"> Lost </option>
								</select>
							&nbsp
							<input type="submit" name="submit" value="OK" />
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
		</script>

	</body>
</html>




