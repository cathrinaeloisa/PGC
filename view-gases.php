<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	// ADD GASS
	if (isset($_POST['add-gas'])){

		$message=NULL;

		
		if (is_numeric($_POST['gasname'])) {
			$message = "Please input proper gas name.";
		}

		else {
			$gasNames = "SELECT gasName, gasType FROM gasType";
			$result = mysqli_query($dbc,$gasNames);
			$existingGas = FALSE;
			$gas = NULL;

			//CHECK IF GAS ALREADY EXISTS
			while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
				if (strcasecmp($row['gasName'],$_POST['gasname']) == 0 && strcasecmp($row['gasType'],$_POST['gasType']) == 0) {
					$existingGas = TRUE;
				}
			}
			
			if (!$existingGas) {
				$gasType = $_POST['gasType'];
				$gasName = $_POST['gasname'];
				$gasPrice = $_POST['gasprice'];

				$date = date('Y-m-d');

				$query = "SELECT gasID FROM gastype WHERE gasType = '$gasType' ORDER BY gasID DESC LIMIT 1"; 
				$result = mysqli_query($dbc,$query);
				$result = mysqli_fetch_array($result, MYSQLI_ASSOC);

				if ($gasType == "Medical") $gasTypeID = 100;
				else $gasTypeID = 200;

				if (isset($_POST['specialgas'])) $specialgas = 1;
				else $specialgas = 0;

				$gasNumber = sprintf("%02d", substr($result['gasID'],4) + 1);
				$gasID = $gasTypeID."-".$gasNumber;
				
				$insertgas = "INSERT INTO gastype (gasID, gasName, gasType, isSpecialGas) VALUES ('{$gasID}','{$gasName}','{$gasType}','{$specialgas}')";
				$result=mysqli_query($dbc,$insertgas);

				$insertprice = "INSERT INTO gaspricingaudit (gasID, price, auditDate) VALUES ('{$gasID}','{$gasPrice}', '{$date}')";
				$result2 = mysqli_query($dbc,$insertprice);

				if ($result && $result2) {
					$message = "$gasName successfully added.";	
				}			
				else $message = "Error adding $gasName.";

			} else {
				$message = "Gas type already exists!";
			}
		}
		
	}

	//EDIT GAS PRICE
	if (isset($_POST['edit-gas'])){

		$message=NULL;

		$gasID = $_POST['chosengas'];
		$gasPrice = $_POST['gasprice'];
		$remarks = $_POST['remarks'];

		$priceResult = mysqli_query($dbc,getLatest($gasID));
		$row = mysqli_fetch_array($priceResult, MYSQLI_ASSOC);

		if ($row['price'] == $gasPrice) $message = "New price is equal to current price.";
		else {
			$date = date('Y-m-d');
			$insertPrice = "INSERT INTO gasPricingAudit (gasID,price,auditDate,remarks) VALUES ('{$gasID}','{$gasPrice}','{$date}','{$remarks}')";
			$result=mysqli_query($dbc,$insertPrice);

			$gasnamequery = "SELECT * FROM gasType WHERE gasID = '{$gasID}'";
			$gasnameresult = mysqli_query($dbc,$gasnamequery); 
			$name = mysqli_fetch_array($gasnameresult, MYSQLI_ASSOC);
			
			if ($result) {
				$message = "Price for {$name['gasType']} {$name['gasName']} successfully updated.";	
			}			
			else $message = "Error updating {$name['gasType']} {$name['gasName']}.";
		}	
		
	}

	function getLatest ($gasID) {
		return $gasList = "SELECT * 
							 FROM gasType GT JOIN gaspricingaudit GAP ON GT.gasID = GAP.gasID 
							WHERE GT.gasID = '$gasID' 
						 ORDER BY GAP.auditID DESC 
						    LIMIT 1";
	}

	$gasList = "SELECT * FROM gasType NATURAL JOIN gaspricingaudit GROUP BY gasID";

?>

<html>
	<head>
		<title>View Gases</title>
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
							<a class="pure-menu-link highlighter">Gases</a>
						</li>
						<li>
							<a  href="view-cylinders.php" class="pure-menu-link"> Cylinders</a>
						</li>
						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul class="dropdown">
								<li>
									<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
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
			<!-- END SIDEBAR -->

			<div class="pure-u-6-24"></div>
			<div class="pure-u-17-24">
				<div class="content-container">
					<div class="page-title-container">
						<p class="title"> Gases </p>
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

					<div>
						<table class="hover stripe cell-border" id="Table">
							<thead>
								<tr>
									<th></th>
									<th style="text-align:center !important;"> Gas Type </th>
									<th style="text-align:center !important;"> Gas Name </th>
									<th style="text-align:center !important;" width="1"> Gas Price </th>
									<th style="text-align:center !important;"> Last Price Update</th>
									<th></th>
								</tr>
							</thead>

							<?php
								$result = mysqli_query($dbc,$gasList);
								while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {	
									$row = mysqli_query($dbc,getLatest($row['gasID']));
									$row = mysqli_fetch_array($row, MYSQLI_ASSOC);

									echo "	<tr>";
									if ($row['isSpecialGas'] == 1) echo "<td> Special Gas </td>";
									else echo "<td></td>";
									echo	"	<td style='text-align:center !important;'> {$row['gasType']} </td>
												<td style='text-align:center !important;'> {$row['gasName']} </td>
												<td style='text-align:right !important;'> {$row['price']} </td>
												<td style='text-align:center !important;'> {$row['auditDate']} </td>
												<td style='text-align:center !important; font-size: 13; cursor:pointer'>
													<a href=\"price-history.php?gasID={$row['gasID']}&gasType={$row['gasType']}&gasName={$row['gasName']}\"> View Price History </a>
												</td>";
									echo "</tr>";
									
								}
							?>

						</table>
						<br>
						<br>
					
						<!-- Button trigger modal -->
						<center>
							<button type="button" class="btn" data-toggle="modal" data-target="#addGas">Add New Gas</button>
							<button type="button" class="btn" data-toggle="modal" data-target="#editGasPrice">Edit Gas Price</button>
						</center>

						<!-- Modal for Add Gas -->
						<div class="modal fade" id="addGas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							<div class="modal-dialog" role="document">
						    	<div class="modal-content">
						      		<div class="modal-header">
						        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						        		<h4 class="modal-title" id="myModalLabel">New Gas Details</h4>
						      		</div>

						      		<div class="modal-body">
						      			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-horizontal" id="addGasForm">
						      				<div>
							      				<div class="form-group">
													<label for="gasType" class="col-sm-4 control-label"> Gas Type</label>
													<div class="col-sm-7">
														<label class="radio-inline">	
															<input type="radio" name="gasType" checked="checked" value="Medical"> Medical &nbsp&nbsp&nbsp
														</label>
														<label class="radio-inline">
															<input type="radio" name="gasType" value="Technical"> Technical
														</label>
													</div>
												</div>
												<div class="form-group">
													<label for="gasname" class="col-sm-4 control-label"> Gas Name </label>
													<div class="col-sm-7">
														<input type="text" class="form-control" name="gasname"/>
													</div>
												</div>
												<div class="form-group">
													<label for="gasprice" class="col-sm-4 control-label"> Gas Price </label>
													<div class="col-sm-7">
														<input type="number" min="0" class="form-control" name="gasprice" size="18"/>
													</div>
												</div>
												<div class='form-gorup'>
													<div class="col-sm-offset-4 col-sm-7">
														<div class="checkbox">
															<label>
																<input type="checkbox" name="specialgas" value="0"> Special Gas
															</label>
														</div>
													</div>
												</div>
												<br>
											</div>
						      		</div>

									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							        	<button type="submit" name="add-gas" class="btn btn-primary">Add New Gas</button>
							    	</div>

							      </form>
						    </div>
						  </div>
						</div>

						<!-- Modal for Edit Gas -->
						<div class="modal fade" id="editGasPrice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							<div class="modal-dialog" role="document">
						    	<div class="modal-content">
						      		<div class="modal-header">
						        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						        		<h4 class="modal-title" id="myModalLabel">Edit Gas Price</h4>
						      		</div>

						      		<div class="modal-body">
						      			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-horizontal" id="editGasForm">
						      				<div>
					      						<div class="form-group">
													<label for="gastype" class="col-sm-4 control-label"> Please choose gas</label>
													<div class="col-sm-7">
														<select class="form-control" name="chosengas">
															<option value="">Select...</option>
															<?php
																$result = mysqli_query($dbc,$gasList);
																while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
																	echo "<option value=\"{$row['gasID']}\"> {$row['gasType']} {$row['gasName']}
																		</option>";
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="gasprice" class="col-sm-4 control-label"> New Price </label>
													<div class="col-sm-7">
														<input type="number" min="0" class="form-control" name="gasprice" size="18"/>
													</div>
												</div>
												<div class="form-group">
													<label for="remarks" class="col-sm-4 control-label"> Remarks</label>
													<div class="col-sm-7">
														<textarea class="form-control" name="remarks"></textarea>
													</div>
												</div>
											</div>
						      		</div>

									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							        	<button type="submit" name="edit-gas" class="btn btn-primary">Save Changes</button>
							    	</div>

							      </form>
						    </div>
						  </div>
						</div>

					</div>


				</div>
			</div>
		</div>

		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="CSS/jquery.validate.min.js"></script>

		<script type="text/javascript">
			$(function() {
        // Setup form validation on the #register-form element
		        $("#addGasForm").validate({
		            // Specify the validation rules
		            rules: {
		            	gasType: "required",
		                gasname: "required",
		                gasprice: "required",
		            },
		            highlight: function(element) {
		                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		            },
		            success: removeError,
		            // Specify the validation error messages
		            messages: {
		                gasType: "Please select gas type.",
		                gasname: "Please input gas name.",
		                gasprice: "Please input gas price.",
		            }
		        });
    		})

    		$(function() {
        // Setup form validation on the #register-form element
		        $("#editGasForm").validate({
		            // Specify the validation rules
		            rules: {
		            	chosengas: "required",
		                gasprice: "required",
		                remarks: "required",
		            },
		            highlight: function(element) {
		                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		            },
		            success: removeError,
		            // Specify the validation error messages
		            messages: {
		                chosengas: "Please select gas.",
		                gasprice: "Please input gas price.",
		                remarks: "Please state reason for price change.",
		            }
		        });
		    })

		 	function removeError(element) {
	        element.addClass('valid')
	            .closest('.form-group')
	            .removeClass('has-error');
			}

		</script>
		<script> 
			$(document).ready(function(){
				$('#Table').DataTable();
			});
			$('#Table').DataTable({
				"order": [],
			    "columnDefs": [ {
			      "targets"  : [1,5],
			      "orderable": false,
				}, {
					"targets" : [0],
					"visible" : false,
				}]
			});

		</script>
	</body>
</html>