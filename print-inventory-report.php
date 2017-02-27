
<?php
	require_once('pentagas-connect.php');
	session_start();
	
	function statusCountCylindersOfGas ($gasID, $statusID) {
		return $query = " SELECT COUNT(c.cylinderID) AS 'cylinderCount', gt.gasID, gt.gasType, gt.gasName
							FROM cylinders c JOIN gasType gt ON c.gasID = gt.gasID
						   WHERE c.cylinderStatusID = $statusID
                             AND gt.gasID = '{$gasID}'";
	}

	function countTotalCylindersOfGas ($gasID) {
		return $query = " SELECT COUNT(c.cylinderID) AS 'cylinderCount'
							FROM cylinders c JOIN gasType gt ON c.gasID = gt.gasID
						   WHERE gt.gasID = '{$gasID}'
						   	 AND c.cylinderStatusID != 403
                             AND c.cylinderStatusID != 404
                             AND c.cylinderStatusID != 407
                             AND c.cylinderStatusID != 408";
	}

	function getGas () {
		return $query = " SELECT gasID
							FROM gasType";
	}


	date_default_timezone_set('Asia/Manila');
	$timestamp = date("F j, Y, g:i a");
	require('fpdf.php');
						
	class PDF extends FPDF
	{
	// Page footer
	function Footer()
		{
	    // Position at 1.5 cm from bottom
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',10);
		// Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		}
	}
	
	ob_start();
	//Connect to your database
	include("pentagas-connect.php");
	//Create new pdf file
	$pdf = new PDF();

	//Disable automatic page break
	$pdf->SetAutoPageBreak(false);

	//Add first page
	$pdf->AliasNbPages();
	$pdf->AddPage('L');

	// Page header
	// Logo
	$pdf->Image('C:\xampp\htdocs\systimpfeb4\pentagon_png.png',115,10,65); //CHANGE PATH NAME TO RUN !! 
	$pdf->Ln(40);
	$pdf->SetFont('Arial','B',15);
	$pdf->Cell(80);
	// Title
	$pdf->Cell(110,10,'Daily Inventory Report',0,0,'C');
	// Line break
	$pdf->Ln(6);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->Cell(270,10, $timestamp ,0,0,'C');

	//set initial y axis position per page
	$y_axis_initial = 70;

	//print column titles
	$pdf->SetFillColor(232, 232, 232);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->SetY(70);
	$pdf->SetX(35);
	$pdf->Cell(58, 6, 'Gas Name', 1, 0, 'L', 1);
	$pdf->Cell(40, 6, 'Qty. Available', 1, 0, 'L', 1);
	$pdf->Cell(40, 6, 'Qty. Dispatched', 1, 0, 'L', 1);
	$pdf->Cell(40, 6, 'Qty. Empty', 1, 0, 'L', 1);
	$pdf->Cell(44, 6, 'TOTAL CYLINDERS', 1, 0, 'L', 1);
	$y_axis=70;
	$row_height = 6;
	$y_axis = $y_axis + $row_height;
	          
	// require_once('pentagas-connect.php');
	$query = " SELECT gasID
				 FROM gasType";

	$result = mysqli_query($dbc,$query);
	//initialize counter
	$i = 0;
	//Set maximum rows per page
	$max = 25;
	//Set Row Height
	$row_height = 6;

	while($gasRow=mysqli_fetch_array($result)){
		$availableCountResult = mysqli_query($dbc, statusCountCylindersOfGas($gasRow['gasID'], 401));
		$reservedCountResult = mysqli_query($dbc, statusCountCylindersOfGas($gasRow['gasID'], 409));
		$dispatchedCountResult = mysqli_query($dbc, statusCountCylindersOfGas($gasRow['gasID'], 406));
		$emptyCountResult = mysqli_query($dbc, statusCountCylindersOfGas($gasRow['gasID'], 402));
							
		$totalCountResult = mysqli_query($dbc, countTotalCylindersOfGas($gasRow['gasID']));
		$availableCountRow = mysqli_fetch_array($availableCountResult,MYSQL_ASSOC);
		$reservedCountRow = mysqli_fetch_array($reservedCountResult,MYSQL_ASSOC);
		$dispatchedCountRow = mysqli_fetch_array($dispatchedCountResult,MYSQL_ASSOC);
		$emptyCountRow = mysqli_fetch_array($emptyCountResult,MYSQL_ASSOC);
		$totalCountRow = mysqli_fetch_array($totalCountResult,MYSQL_ASSOC);

		if ($i == $max){
			$pdf->AddPage();
			//print column titles for the current page
			$pdf->SetY($y_axis_initial);
			$pdf->SetX(35);
			//Go to next row
			$y_axis = $y_axis + $row_height;

			//Set $i variable to 0 (first row)
			$i = 0;
		}

		$gasNT = $availableCountRow['gasType'] . " " . $availableCountRow['gasName'];
		$qtyAvailable = $availableCountRow['cylinderCount'] + $reservedCountRow['cylinderCount'];
		$qtyDispatched = $dispatchedCountRow['cylinderCount'];
		$qtyEmpty = $emptyCountRow['cylinderCount'];
		$totalCylinders = $totalCountRow['cylinderCount'];

		$pdf->SetFillColor(256, 256, 256);
		$pdf->SetY($y_axis);
		$pdf->SetX(35);
		$pdf->Cell(58, 6, $gasNT, 1, 0, 'L', 1);
		$pdf->Cell(40, 6, $qtyAvailable, 1, 0, 'L', 1);
		$pdf->Cell(40, 6, $qtyDispatched, 1, 0, 'L', 1);
		$pdf->Cell(40, 6, $qtyEmpty, 1, 0, 'L', 1);
		$pdf->Cell(44, 6, $totalCylinders, 1, 0, 'L', 1);

		//Go to next row
		$y_axis = $y_axis + $row_height;
		$i = $i + 1;
	} 		        
	//Send file
	$pdf->Output();
	ob_end_flush();
				
?>