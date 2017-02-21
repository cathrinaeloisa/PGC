<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 101 && $userType != 102 && $userType != 106) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	if (isset($_POST['add-employee'])){
		$message=NULL;
		$existing = FALSE;
		$checkUsernames = "SELECT username FROM useraccounts";
		$check = mysqli_query($dbc, $checkUsernames);
		while ($row=mysqli_fetch_array($check,MYSQLI_ASSOC)) {
			if ($row['username'] == $_POST['username']) $existing = TRUE;
		}

		if (!$existing) {
			$fullname=$_POST['fullname'];
			$username=$_POST['username'];
			$password=$_POST['password'];
			$userTypeID=$_POST['userTypeID'];
			
			$query="INSERT INTO useraccounts (userTypeID, username, name, password) values ('{$userTypeID}','{$username}','{$fullname}', PASSWORD('{$password}'))";
			$result = mysqli_query($dbc, $query);

			if ($result) {
				$message = "$fullname added as a new employee!";
			}
			else $message = "Error adding $fullname.";
			

			$flag=1;
		}
		else $message = "Username already exists!";
		
	}

	$userList = "SELECT * FROM useraccounts NATURAL JOIN userTypes ORDER BY userID ASC";
?>

<html>
	<head>
		<title>View Employees</title>
		
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
		<link rel="stylesheet" href="CSS/dashboard.css" >

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
					<ul>
						<?php
							if ($userType == 101) {
								echo '<li>
										<a href="administrative-manager-home.php" class="pure-menu-link"> Home </a>
									</li>';
							}
							else if ($userType == 102) {
								echo '<li>
										<a href="sales-and-marketing-home.php" class="pure-menu-link"> Home </a>
									</li>';
							}
						?>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a href="view-employees.php" class="pure-menu-link highlighter"> Employees </a>
						</li>
						<?php
							if ($userType == 101) {
								echo ' <li>
											<a href="view-gases.php" class="pure-menu-link">Gases</a>
										</li>
										<li>
											<a href="view-cylinders.php" class="pure-menu-link"> Cylinders</a>
										</li>';
							}
						?>
						<li>
							<a class="pure-menu-link"> Reports </a>
							<ul class="dropdown">
							<?php
								if ($userType == 101) {
									echo '	<li>
												<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
											</li>
											<li>
												<a href="report-cylinder-history.php" class="pure-menu-link">Cylinder History Report</a>
											</li>
							                <li>
							                  <a href="report-cylinder-status.php" class="pure-menu-link">Daily Cylinder Status Report</a>
							                </li>';

								}
								else if ($userType == 102) {
									echo '<li>
											<a href="report-sales.php" class="pure-menu-link"> Sales Report </a>
										</li>';
								}
							?>
							</ul>
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
					<div class="content-container">
					<div class="page-title-container">
						<?php
							if ($userType == 101) echo '<p class="title"> Employees: Administrative Department</p>';
							else if ($userType == 102) echo '<p class="title"> Employees: Sales and Marketing Department </p>';
						?>
					</div>

					<div class="divider">
						<div>
							<?php 
								if (isset($message)) {
									echo $message;
									$message = NULL;
								}
							?>
						</div>
					</div>
					
					<div align="center">
						<table class="hover stripe cell-border" id="Table" style="text-align:center">
							<thead>
								<tr>
									<th style="text-align:center !important;"> Employee ID </th>
									<th style="text-align:center !important;"> Employee Name </th>
									<th style="text-align:center !important;"> Employee Type </th>
								</tr>
							</thead>

							<?php
								$SAMD = FALSE;
								$AD = FALSE;
								if ($_SESSION['userTypeID'] == 102 || $_SESSION['userTypeID'] == 105) $SAMD = TRUE;
								else $AD = TRUE;

								$result = mysqli_query($dbc,$userList);

								if ($SAMD) {
									while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
										if ($row['userTypeID'] == 102 || $row['userTypeID'] == 105) {
											if ($row['userID'] !== $_SESSION['userID']) {
												echo "	<tr>
															<td> {$row['userID']} </td>
															<td> {$row['name']} </td>
															<td> {$row['userTypeDescription']} </td>
														</tr>";
											}
											
										}
										
									}
									
								}

								else if($AD) {
									while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
										if (!($row['userTypeID'] == 102 || $row['userTypeID'] == 105)) {
											if ($row['userID'] !== $_SESSION['userID']) {
												echo "	<tr>
															<td> {$row['userID']} </td>
															<td> {$row['name']} </td>
															<td> {$row['userTypeDescription']} </td>
														</tr>";	
											}
										}

									}
								}
							?>

						</table>
						<br>
						<br>
					<!-- Button trigger modal -->
					<button type="button" class="btn" data-toggle="modal" data-target="#myModal">Add New Employee</button>
					</div>
					<!-- Modal -->
					<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						<div class="modal-dialog" role="document">
					    	<div class="modal-content">
					      		<div class="modal-header">
					        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        		<h4 class="modal-title" id="myModalLabel">New Employee Details</h4>
					      		</div>

					      		<div class="modal-body">
					      			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-horizontal" id="addEmployee">
										<div>
											<div class="form-group">
												<label for="userTypeID" class="col-sm-3 control-label"> User Type </label>
												<div class="col-sm-8">	
													<select class="form-control" name="userTypeID">
													<?php
														if ($userType == 101) {
															echo '<option value="">Select...</option>
																<option value="101">Administrative Admin </option>
																<option value="103">Billing Clerk </option>
																<option value="104">Cylinder Control Clerk </option>';
														}
														else {
															echo '<option value="">Select...</option>
																<option value="102">Sales and Marketing Admin </option>
																<option value="105">Dispatcher</option>';
														}
													?>
													</select>
												</div>
											</div>

											<div class="form-group"> 
												<label for="fullname" class="col-sm-3 control-label"> Full Name </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="fullname"/>
												</div>
											</div>

											<div class="form-group"> 
												<label for="userName" class="col-sm-3 control-label"> Username </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="username"/>
												</div>
											</div>

											<div class="form-group">
												<label for="password" class="col-sm-3 control-label"> Password </label>
												<div class="col-sm-8">
													<input type="password" class="form-control" name="password"/>
												</div>
											</div>

											<div class="form-group">
												<label for="rePassword" class="col-sm-3 control-label"> Re-type Password </label>
												<div class="col-sm-8">
													<input type="password" class="form-control" name="rePassword"/>
												</div>
											</div>
										</div>
									
					      		</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						        	<button type="submit" name="add-employee" class="btn btn-primary">Add Employee</button>
						    	</div>

						      </form>
					    </div>
					  </div>
					</div>

				</div>
				</div>
			</div>
		</div>


		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="CSS/jquery.validate.min.js"></script>

		<script type="text/javascript">
			$(function() {
        // Setup form validation on the #register-form element
		        $("#addEmployee").validate({
		            // Specify the validation rules
		            rules: {
		            	userTypeID: "required",
		                fullname: "required",
		                username: "required",
		                password: "required",
		                rePassword: "required"
		            },
		            highlight: function(element) {
		                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		            },
		            success: removeError,
		            // Specify the validation error messages
		            messages: {
		                userTypeID: "Please select a user type.",
		                fullname: "Please input employee name.",
		                username: "Please input employee username.",
		                password: "Please input password.",
		                rePassword: "Please confirm password."
		            }
		        });

		        function removeError(element) {
		        element.addClass('valid')
		            .closest('.form-group')
		            .removeClass('has-error');
    			}
    		})
		</script>
		
		<script> 
			$(document).ready(function(){
				$('#Table').DataTable();
			});
		</script>
	</body>
</html>