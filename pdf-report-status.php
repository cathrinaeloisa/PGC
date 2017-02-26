<?php
$timestamp = NULL;
$message = NULL;
	session_start();
	require_once('pentagas-connect.php');
	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}
	function countCylinders($status) {

		return $query = "SELECT COUNT(c.cylinderID) from cylinders c
		                        join gastype gt on c.gasID=gt.gasID
		                        join cylinderstatus cs on c.cylinderStatusID=cs.cylinderStatusID
		                        where cs.cylinderStatusDescription LIKE '{$_POST['select-status']}'";
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
							<a class="pure-menu-link"> Cylinders</a>
							<ul class="dropdown">
								<li>
									<a href="view-cylinders.php" class="pure-menu-link"> Cylinder Details</a>
								</li>
				                <li>
				                  <a href="cylinder-history.php" class="pure-menu-link">Cylinder Transaction Records</a>
				                </li>
							</ul>
						</li>
						<li>
							<a class="pure-menu-link"> Reports</a>
							<ul>
								<li>
									<a href="report-inventory.php" class="pure-menu-link"> Inventory Report</a>
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
				<div class="row">
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
				</div>

				<?php
							ob_start();
							require('fpdf.php');
		        	include('pentagas-connect.php');
							class PDF extends FPDF
							{
							// Page header
							function Header()
							{
									// Select Arial bold 15
									$this->SetFont('Arial','B',15);
									// Move to the right
									$this->Cell(45);
									// Framed title
									$this->Image('pentagon_png.png',70,8,-300);
									$this->Cell(100,83,'Daily Cylinder Availability Report',0,0,'C');
									$tDate = date("F j, Y, g:i a");
									$this->Cell(-15, 95, $tDate, 0, false, 'R', 0, 0, true, 'T', 'M');
									// $this->Cell(-11, 95, 'Date : '.$tDate, 0, false, 'R', 0, 0, true, 'T', 'M');
									//$this->Cell(0, $height,'Date : '.$tDate , 0, 0, 'C')
									$this->Ln(20);

							}

							// Page footer
							function Footer()
							{
									// Position at 1.5 cm from bottom
									$this->SetY(-15);
									// Arial italic 8
									$this->SetFont('Arial','I',8);
									$this->AliasNbPages();
									$this->AliasNbPages('{totalPages}');
									// Page number
									$this->Cell(0,10,'Page '.$this->PageNo() . "/{totalPages}",0,0,'C');
							}
							}
							//Create new pdf file
							$pdf=new PDF('P', 'mm', 'A4');

							//Disable automatic page break
							$pdf->SetAutoPageBreak(false);

							//Add first page
							$pdf->AddPage();

							//set initial y axis position per page
							$y_axis_initial = 70;

							//print column titles
							$pdf->SetFillColor(232, 232, 232);
							$pdf->SetFont('Arial', 'B', 12);
							$pdf->SetY($y_axis_initial);
							$pdf->SetX(30);
							$pdf->Cell(75, 6, 'GAS', 1, 0, 'C', 1);
							$pdf->Cell(75, 6, 'CYLINDER ID', 1, 0, 'C', 1);
							$y_axis=70;
							$row_height = 6;
							$y_axis = $y_axis + $row_height;
		        	$query = "SELECT * from cylinders c
		                        join gastype gt on c.gasID=gt.gasID
		                        join cylinderstatus cs on c.cylinderStatusID=cs.cylinderStatusID
		                        where cs.cylinderStatusDescription LIKE '{$_SESSION['HAHA']}';

		                " ;
	            	$result = mysqli_query($dbc,$query);
								//initialize counter
								$i = 0;

								//Set maximum rows per page
								$max = 30;

								//Set Row Height
								$row_height = 6;
								if(!isset($message)){
									while($row=mysqli_fetch_array($result)){
										if ($i == $max){
												 $pdf->AddPage();
												 //print column titles for the current page
												 $pdf->SetY($y_axis_initial);
												 $pdf->SetX(25);
												 $pdf->Cell(75, 6, 'GAS', 1, 0, 'C', 1);
												 $pdf->Cell(75, 6, 'CYLINDER ID', 1, 0, 'C', 1);
												 $y_axis=70;
												 $row_height = 6;
												 //Go to next row
												 $y_axis = $y_axis + $row_height;

												 //Set $i variable to 0 (first row)
												 $i = 0;
										 }
										 $cyID = $row['cylinderID'];
										 $gas = $row['gasType']." ".$row['gasName'];

										 $pdf->SetY($y_axis);
										 $pdf->SetX(30);
										 $pdf->Cell(75, 6, $gas, 1, 0, 'L', 1);
										 $pdf->Cell(75, 6, $cyID, 1, 0, 'C', 1);
										 //Go to next row
										 $y_axis = $y_axis + $row_height;
										 $i = $i + 1;
									}
								}
								//Send file
								$pdf->Output();
								 ob_end_flush();
	            ?>

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
