<?php
	require_once('pentagas-connect.php');
	session_start();
	$userType = $_SESSION['userTypeID'];
	if ($userType != 103) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}
	
	$customerList = "SELECT * FROM customers";

// ADD NEW CUSTOMER
	function validName($string) {
		return !preg_match('/[\'^$%&*()}{@#?~><>,|=_+]/', $string);
	}	

	if (isset($_POST['add-customer'])){
		$message=NULL;
		
		if (validName($_POST['fullname'])) {
			$fullname=$_POST['fullname'];
			$contactNum=$_POST['contactNum'];
			$email=$_POST['email'];
			$address=$_POST['address'];
			$customerType=$_POST['customerType'];

			// CHECK IF CUSTOMER EXISTS
			$existingCustomer = FALSE;
			$existingEmail = FALSE;
			$customerList = "SELECT * FROM customers";
			$allCustomers = mysqli_query($dbc,$customerList);

			while ($row=mysqli_fetch_array($allCustomers,MYSQLI_ASSOC)) {
				if ($row['name'] == $fullname && $row['contactNum'] == $contactNum && $row['emailAddress'] == $email && $row['deliveryAddress'] == $address) $existingCustomer = TRUE;
				else if ($row['emailAddress'] == $email) $existingEmail = TRUE;
			}

			if ($existingEmail) $message = "Email is already in use.";
			else if ($existingCustomer) $message = "Customer already exists!";
			else if (!$existingCustomer && !$existingEmail) {
				// GENERATE CUSTOMER ID
				$query = "SELECT customerID FROM customers WHERE customerType = '$customerType' ORDER BY customerID DESC LIMIT 1"; 
				$result = mysqli_query($dbc,$query);
				$result = mysqli_fetch_array($result, MYSQLI_ASSOC);

				if ($customerType == "Medical") $customerTypeID = 100;
				else $customerTypeID = 200;

				$customerNumber = sprintf("%05d", substr($result['customerID'],4) + 1);
				$customerID = $customerTypeID."-".$customerNumber;

				$addCustomer = "INSERT INTO customers (customerID, customerType, name, deliveryAddress, contactNum, emailAddress) VALUES ('{$customerID}','{$customerType}','{$fullname}','{$address}','{$contactNum}', '{$email}')";
				$result=mysqli_query($dbc,$addCustomer);

				if ($result) {
					$message = "$fullname added as a new customer.";
					// header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/view-customers.php");
				}
				else $message = "Error adding $fullname.";
				$flag=1;
			}
		}
	}

// EDIT CUSTOMER DETAILS
if (isset($_POST['save-customer-details'])){
		$message = NULL;

		$customerID = $_POST['customerID'];
		$customer_name = $_POST['fullname'];
		$delivery_address = $_POST['address'];
		$contact_num = $_POST['contactNum'];
		$email_add = $_POST['email'];
		
		
		if (!isset($message)) {
			$update_customer="UPDATE customers SET name='{$customer_name}', deliveryAddress='{$delivery_address}', contactNum='{$contact_num}', emailAddress='{$email_add}' WHERE customerID = '{$customerID}'";
			$result=mysqli_query($dbc,$update_customer);

			if ($result) {
				$message = "Customer details successfully updated!";
			}
			else $message = "The system encountered an error. Please try again.";
		
		}
	}

?>
<html>
	<head>
		<title>View Customers</title>
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
							<a href="billing-clerk-home.php" class="pure-menu-link"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a href="view-customers.php" class="pure-menu-link highlighter"> Customers </a>
						</li>
						<li>
							<a class="pure-menu-link"> Orders </a>
							<ul class="dropdown">
								<li>
									<a href="view-orders.php" class="pure-menu-link"> View Orders </a>
								</li>
								<li>
									<a href="view-customers.php" class="pure-menu-link"> Create New Order </a>
								</li>
								<li>
									<a href="cancel-order.php" class="pure-menu-link"> Cancel Order</a>
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
						<p class="title"> Customers </p>
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

					
					<table class="hover stripe cell-border" id="Table">
						<thead>
							<tr>
								<th ></th>
								<th style="text-align:center !important; font-size:14;"> Customer Name </th>
								<th style="text-align:center !important; font-size:14;"> Customer Type </th>
								<th style="text-align:center !important; font-size:14;"> Contact Number </th>
								<th style="text-align:center !important; font-size:14;"> Email Address </th>
								<th style="text-align:center !important; font-size:14;"> Address </th>
							</tr>
						</thead>

						<?php
							$result = mysqli_query($dbc,$customerList);
							while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
								echo "	<tr>
											<td style='text-align:center'> <input type=\"radio\" name=\"customerID\" class=\"chosenCustomer\" id=\"{$row['customerID']}\" value={$row['customerID']}></td>
											<td style='font-size:14;' id='customerName{$row['customerID']}' class=\"chosenCustomer\"> {$row['name']} </td>
											<td style='text-align:center; font-size:14;' id='customerType{$row['customerID']}' class=\"chosenCustomer\"> {$row['customerType']} </td>
											<td style='font-size:14;' id='customerNum{$row['customerID']}' class=\"chosenCustomer\"> {$row['contactNum']} </td>
											<td style='font-size:14;' id='customerEmail{$row['customerID']}' class=\"chosenCustomer\"> {$row['emailAddress']} </td>
											<td style='font-size:14;' id='customerDeliveryAddress{$row['customerID']}' class=\"chosenCustomer\"> {$row['deliveryAddress']} </td>
										</tr>";
							}
						?>
					</table>
					<br>
					<br>

					<!-- Button trigger modal -->
					<center>
						<button type="button" class="btn" data-toggle="modal" data-target="#addCustomer">Add New Customer</button>
						<button type="button" class="btn disabled" data-toggle="modal" data-target="" id="editCustomerButton">Edit Customer Details</button>
					</center>

					<!-- Modal for Add Customer -->
					<div class="modal fade" id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						<div class="modal-dialog" role="document">
					    	<div class="modal-content">
					      		<div class="modal-header">
					        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        		<h4 class="modal-title" id="myModalLabel">New Customer Details</h4>
					      		</div>

					      		<div class="modal-body">
					      			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-horizontal" id="addCustomerForm">
					      				<div>
				      						<div class="form-group">
												<label for="customerType" class="col-sm-4 control-label"> Customer Type</label>
												<div class="col-sm-7">
													<label class="radio-inline">	
														<input type="radio" name="customerType" checked="checked" value="Medical"> Medical &nbsp&nbsp&nbsp
													</label>
													<label class="radio-inline">
														<input type="radio" name="customerType" value="Industrial"> Industrial
													</label>
												</div>
											</div>
											<div class="form-group">
												<label for="fullname" class="col-sm-4 control-label"> Company/Customer Name </label>
												<div class="col-sm-7">	
													<input type="text" class="form-control" name="fullname"/>
												</div>
											</div>
											<div class="form-group">
												<label for="contactNum" class="col-sm-4 control-label"> Contact Number </label>
												<div class="col-sm-7">	
													<input type="text" class="form-control" name="contactNum"/>
												</div>
											</div>
											<div class="form-group">
												<label for="email" class="col-sm-4 control-label"> Email Address </label>
												<div class="col-sm-7">	
													<input type="text" class="form-control" name="email"/>
												</div>
											</div>
											<div class="form-group">
												<label for="address" class="col-sm-4 control-label"> Delivery Address </label>
												<div class="col-sm-7">	
													<input type="text" class="form-control" name="address"/>
												</div>
											</div>
										</div>
					      		</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						        	<button type="submit" name="add-customer" class="btn btn-primary">Add New Customer</button>
						    	</div>

						      </form>
					    </div>
					  </div>
					</div>

					<!-- Modal for Edit Customer -->
					<div class="modal fade" id="editCustomer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						<div class="modal-dialog" role="document">
					    	<div class="modal-content">
					      		<div class="modal-header">
					        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        		<h4 class="modal-title" id="myModalLabel">Edit Customer Details</h4>
					      		</div>

					      		<div class="modal-body">
					      			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-horizontal" id="editCustomerForm">
					      				<div>
					      					<input type="hidden" name="customerID" id="editCustomerID">
											<div class="form-group">
												<label for="fullname" class="col-sm-4 control-label"> Company/Customer Name </label>
												<div class="col-sm-7">	
													<input id="fullname" type="text" class="form-control" name="fullname"/>
												</div>
											</div>
											<div class="form-group">
												<label for="contactNum" class="col-sm-4 control-label"> Contact Number </label>
												<div class="col-sm-7">	
													<input id="contactNum" type="text" class="form-control" name="contactNum"/>
												</div>
											</div>
											<div class="form-group">
												<label for="email" class="col-sm-4 control-label"> Email Address </label>
												<div class="col-sm-7">	
													<input id="email" type="text" class="form-control" name="email"/>
												</div>
											</div>
											<div class="form-group">
												<label for="address" class="col-sm-4 control-label"> Delivery Address </label>
												<div class="col-sm-7">	
													<input id="address" type="text" class="form-control" name="address"/>
												</div>
											</div>
										</div>
					      		</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal" id="closeEditForm">Close</button>
						        	<button type="submit" name="save-customer-details" class="btn btn-primary">Save Changes</button>
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
		<script type="text/javascript" src="CSS/jquery.validate.min.js"></script>

		<!-- ADD CUSTOMER VALIDATION MODAL -->
		<script type="text/javascript">
			$(function() {
       			// Setup form validation on the #register-form element
		        $("#addCustomerForm").validate({
		            // Specify the validation rules
		            rules: {
		            	customerType: "required",
		                fullname: "required",
		                contactNum: "required",
		                email: "required",
		                address: "required"
		            },
		            highlight: function(element) {
		                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		            },
		            success: removeError,
		            // Specify the validation error messages
		            messages: {
		                customerType: "Please select customer type.",
		                fullname: "Please input customer name.",
		                contactNum: "Please input customer contact number.",
		                email: "Please input customer email address.",
		                address: "Please customer delivery address."
		            }
		        });

		        function removeError(element) {
		        element.addClass('valid')
		            .closest('.form-group')
		            .removeClass('has-error');
    			}
    		})
		</script>

		<!-- EDIT CUSTOMER SELECTION -->
		<script>
			// ADD DATA TARGET FOR EDIT CUSTOMER BUTTON
			$('#editCustomerButton').on('click', function() {
				var buttonClasses = document.getElementById("editCustomerButton").classList;
				if (buttonClasses.contains('disabled')) {
			        alert('Please select a customer.');
			    } else {
			        document.getElementById("editCustomerButton").setAttribute("data-target", "#editCustomer");
			    }
			})

			// ENABLES EDIT CUSTOMER BUTTON WITH RADIO BUTTON SELECTION
			$(".chosenCustomer").on('click', function() {
				var buttonClasses = document.getElementById("editCustomerButton").classList;
				var customerID = this.id;
				if (buttonClasses.contains("disabled")) buttonClasses.remove("disabled");

				$("#fullname").val( $("#customerName" + customerID).text() );
				$("#contactNum").val( $("#customerNum" + customerID).text() );
				$("#email").val( $("#customerEmail" + customerID).text() );
				$("#address").val( $("#customerDeliveryAddress" + customerID).text() );
				$("#editCustomerID").val(customerID);
			})
		</script>

		<!-- EDIT CUSTOMER DETAILS VALIDATION MODAL -->
		<script type="text/javascript">
			$(function() {
       			// Setup form validation on the #register-form element
		        $("#editCustomerForm").validate({
		            // Specify the validation rules
		            rules: {
		                fullname: "required",
		                contactNum: "required",
		                email: "required",
		                address: "required"
		            },
		            highlight: function(element) {
		                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		            },
		            success: removeError,
		            // Specify the validation error messages
		            messages: {
		                fullname: "Please input customer name.",
		                contactNum: "Please input customer contact number.",
		                email: "Please input customer email address.",
		                address: "Please customer delivery address."
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

			$('#Table').DataTable({
				"order": [],
			    "columnDefs": [ {
			      "targets"  : [0,2,3,4],
			      "orderable": false,
				}]
			});
		</script>

	</body>
</html>

