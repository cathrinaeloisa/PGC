<?php
$timestamp = NULL;
$message = NULL;


	session_start();
	require_once('pentagas-connect.php');

  $userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

?>

</!DOCTYPE html>
<html>
	<head>
		<title> Cylinder Activity </title>
		<link rel="stylesheet" href="CSS/dashboard.css">
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
	
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
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
							<a href="view-employees.php"  class="pure-menu-link"> Employees </a>
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
									<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
								</li>
								<li>
									<a href="report-cylinder-history.php" class="pure-menu-link">Cylinder History Report</a>
								</li>
				                <li>
				                  <a href="report-cylinder-status.php" class="pure-menu-link highlighter">Daily Cylinder Status Report</a>
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
				<!-- TITLE -->
				<div class="page-header">
					<h1>Daily Cylinder Status Report</h1>
					<h7> 
						<?php
							date_default_timezone_set('Asia/Manila');
							$timestamp = date("F j, Y // g:i a");
							echo '<b>' .$timestamp. '</b>';
						?>
					</h7>
				</div>

				<?php
					if(isset($_POST['show-report'])){
						$_SESSION['select-status']=$_POST['select-status'];
					}
					else{
						$_SESSION['select-status'] = null;
					}			
				
		        	require_once('pentagas-connect.php');
		        	$query = "SELECT * from cylinders c
		                        join gastype gt on c.gasID=gt.gasID
		                        join cylinderstatus cs on c.cylinderStatusID=cs.cylinderStatusID
		                        where cs.cylinderStatusDescription LIKE '{$_POST['select-status']}'; 
		                " ;

	            	$result = mysqli_query($dbc,$query);
	            ?>

	           <table id ="Table";>
	                <thead>
	                    <th style="text-align:center">Cylinder ID</th>
	                    <th style="text-align:center">Gas</th>
	                    <th style="text-align:center">Cylinder Status</th>
	                </thead>

		            <?php
			            if(!isset($message)){
			              while($row=mysqli_fetch_array($result)){
			                $blank=" ";
			                echo "<tr>
					                <td width=\"20%\"><div align=\"center\">{$row['cylinderID']}
					                <td width=\"20%\"><div align=\"center\">{$row['gasType']} {$blank} {$row['gasName']}
					                <td width=\"20%\"><div align=\"center\">{$row['cylinderStatusDescription']}
					                </div></td>
					              </tr>";
			              }
			            }
			        ?>

		        </table>

		        <br>
				<br>
				<center><b>*** END OF REPORT ***</b></center>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				
			</div>
		</div>
	</body>

	<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
	<script type="text/javascript" src="CSS/jquery.validate.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>

	<!-- FOR VALIDATION -->
	<script type="text/javascript">
		$(function() {
   			// Setup form validation on the #register-form element
	        $("#statusSelectionForm").validate({
	            // Specify the validation rules
	            rules: {
	            	'select-status': "required",
	            },
	            highlight: function(element) {
	                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
	            },
	            success: removeError,
	            // Specify the validation error messages
	            messages: {
	                'select-status': "Please select a status.",
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
		$('#Table').DataTable({
			paging: false,
			searching: false,
			ordering: false,
		});
	});
	</script>
</html>
