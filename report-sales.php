<?php
$timestamp = NULL;
$message = NULL;
	require_once('pentagas-connect.php');
	session_start();

	// $userType = $_SESSION['userTypeID'];
	// if ($userType != 102) {
	// 	header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	// }

	if(isset($_POST['show-report'])){
		if (empty($_POST['startdate'])){
			$_SESSION['startdate']=FALSE;
			$message='You forgot to enter the start date';
		} else $_SESSION['startdate'] = $_POST['startdate'];
		if (empty($_POST['enddate'])){
			$_SESSION['enddate']=FALSE;
			$message='You forgot to enter the end date';
		}else
			$_SESSION['enddate']=$_POST['enddate'];
		if(!empty($_POST['startdate']) && !empty($_POST['enddate'])){
			if($_POST['startdate'] > $_POST['enddate'] ){
			$message='End Date must be larger than Start Date!';
			}
		}
	}
	else{
		$_SESSION['startdate'] = null;
		$_SESSION['enddate'] = null;
	}
?>

</!DOCTYPE html>
<html>
	<head>
		<title>Sales - Sales and Marketing </title>
		<link rel="stylesheet" href="CSS/dashboard.css" >
		<link rel="stylesheet" href="CSS/bootstrap-dashboard-edit.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"></link>
		<link rel="stylesheet" href="CSS/bootstrap.min.css">

		<script src="CSS/jquery.min.js"></script>
		<script src="CSS/bootstrap.min.js"></script>
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
							<a href="sales-and-marketing-home.php" class="pure-menu-link"> Home </a>
						</li>

						<li>
							<a href="view-account-details.php" class="pure-menu-link"> Account </a>
						</li>

						<li>
							<a href="view-employees.php" class="pure-menu-link"> Employees </a>
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

				<!-- TITLE -->
				<div class="page-header">
					<h1> Sales Report</h1>
					<h7>
						<?php
							date_default_timezone_set('Asia/Manila');
							$timestamp = date("F j, Y // g:i a");
							echo "<b>".$timestamp."<b>";
						?>
					</h7>
				</div>

				<!-- DATE RANGE CONTAINER -->
				<div class="well" id="dateSpace">
					<form action="report-sales-dates.php" method="post" class="form-horizontal" id="dateRange">
						<div class="form-group">
							<label for="startdate" class="col-sm-2 control-label"> Start Date: </label>
							<div class="col-sm-3">
								<input type="date" name="startdate" id="startdate" value ="<?php if (isset($_POST['startdate'])) echo $_POST['startdate']; ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="enddate"  class="col-sm-2 control-label"> End Date: </label>
							<div class="col-sm-3">
								<input type="date" name="enddate" id="enddate" value ="<?php if (isset($_POST['enddate'])) echo $_POST['enddate']; ?>" class="form-control">
							</div>
						</div>

						<div class="form-group">
							<center>
								<button class="btn btn-primary show" type="submit" name="show-report"> Show Report </button>
							</center>
						</div>
					</form>
				</div>

			</div>
		</div>
	</body>
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"> </script>
	<script type="text/javascript" src="CSS/jquery.validate.min.js"></script>

	<!-- FOR VALIDATION -->
	<script type="text/javascript">
		jQuery.validator.addMethod("isValid", function (value, element) {
		    var startDate = $('#startdate').val();
		    var finDate = $('#enddate').val();
		    return Date.parse(startDate) < Date.parse(finDate);
		}, "* End date must be after start date");
		$(function() {
   			// Setup form validation on the #register-form element
	        $("#dateRange").validate({
	            // Specify the validation rules
	            rules: {
	            	startdate: {
	            		required: true,
	            	},
	            	enddate: {
	            		required: true,
	            		isValid: true,
	            	},
	            },

	            highlight: function(element) {
	                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
	            },
	            success: removeError,
	            // Specify the validation error messages
	            messages: {
	                startdate: {
	                	required: "This is a required field.",
	                },
	                enddate: {
	                	required: "This is a required field.",
	                	isValid: "End date must be after start date",
	                },
	            }
	        });
	        function removeError(element) {
	        element.addClass('valid')
	            .closest('.form-group')
	            .removeClass('has-error');
			}
		})
	</script>
</html>
