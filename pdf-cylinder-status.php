<?php
	require_once('pentagas-connect.php');
	session_start();

	$userType = $_SESSION['userTypeID'];
	if ($userType != 101) {
		header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
	}

	function getCylinders($status, $gasID) {
		if (strcmp($status, "Available") == 0) {
			return $query = "SELECT c.cylinderID from cylinders c
		                        join gastype gt on c.gasID=gt.gasID
		                        join cylinderstatus cs on c.cylinderStatusID=cs.cylinderStatusID
		                        where c.gasID = '{$gasID}'
		                        AND cs.cylinderStatusDescription LIKE '{$status}'
		                        OR c.gasID = '{$gasID}'
		                        AND cs.cylinderStatusDescription LIKE 'Reserved'";
		}

		else return $query = "SELECT c.cylinderID from cylinders c
		                        join gastype gt on c.gasID=gt.gasID
		                        join cylinderstatus cs on c.cylinderStatusID=cs.cylinderStatusID
		                        where cs.cylinderStatusDescription LIKE '{$status}'
		                        AND c.gasID = '{$gasID}'";
	}

	function getGasNames($status) {
		if (strcmp($status, "Available") == 0) {
			return $query = "SELECT gt.gasName, gt.gasType, gt.gasID
							   FROM gasType gt JOIN cylinders c ON c.gasID = gt.gasID
							   				   JOIN cylinderStatus cs ON c.cylinderStatusID = cs.cylinderStatusID
							  WHERE cs.cylinderStatusDescription LIKE '{$status}'
							     OR cs.cylinderStatusDescription LIKE 'Reserved'
						   GROUP BY gt.gasID";
		}

		else return $query ="SELECT gt.gasName, gt.gasType, gt.gasID
							   FROM gasType gt JOIN cylinders c ON c.gasID = gt.gasID
							   				   JOIN cylinderStatus cs ON c.cylinderStatusID = cs.cylinderStatusID
							  WHERE cs.cylinderStatusDescription LIKE '{$status}'
						   GROUP BY gt.gasID";
	}
	if(isset($_GET['select-status'])){
		$status = $_GET['select-status'];
		$_SESSION['select-status'] = $status;
	}

	date_default_timezone_set('Asia/Manila');
	$timestamp = date("F j, Y, g:i a");

	ob_start();
	require('fpdf.php');

	class PDF extends FPDF {
	function Header()
		{
            // Select Arial bold 15
            $this->SetFont('Arial','B',15);
            // Move to the right
            $this->Cell(45);
            // Framed title
            $this->Image('pentagon_png.png',70,8,-300);

        	include('pentagas-connect.php');
        	$query = "SELECT * FROM CYLINDERSTATUS";
        	$queryResult = mysqli_query($dbc,$query);
        	$statusSelected = $_SESSION['select-status'];
        	while ($row = mysqli_fetch_array($queryResult,MYSQL_ASSOC)) {
	            if ($statusSelected == $row['cylinderStatusDescription']) {
	            	$this->Cell(95,83,'Daily ' . $row['cylinderStatusDescription'] . ' Cylinder Report',0,0,'C');
	            }
        	}
        	$timestamp = date("F j, Y, g:i a");
        	$this->Cell(-15, 95, $timestamp, 0, false, 'R', 0, 0, true, 'T', 'M');
	        $this->Ln(20);
        }
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

	$pdf = new PDF('P', 'mm', 'A4');

	//Disable automatic page break
	$pdf->SetAutoPageBreak(false);

	//Add first page
	$pdf->AliasNbPages();
	$pdf->AddPage();

 $y_axis_initial = 78;
 $pdf->SetY($y_axis_initial);
 $y_axis=80;
 $row_height = 8;
 $y_axis = $y_axis + $row_height;
 $b = 0;

$max = 18;

 //Set Row Height
 $row_height = 8;
	//Send file
	$gasResult = mysqli_query($dbc, getGasNames($_SESSION['select-status']));
	while($gasRow=mysqli_fetch_array($gasResult)){
      $gasCount=mysqli_num_rows($gasResult);
      $cylinderResult = mysqli_query($dbc, getCylinders($_SESSION['select-status'], $gasRow['gasID']));
      $pdf->SetX(55);
      $gName=$gasRow['gasType']." ".$gasRow['gasName'];
	  	$rowCount = mysqli_num_rows($cylinderResult);

      $pdf->SetFillColor(232,232,232);
      $pdf->SetFont('Arial', 'B', 14);
      $pdf->SetTextColor(0);
      $pdf->Cell(100, 10, $gName, 1, 0, 'C', 1);

      while($cylinderRow=mysqli_fetch_array($cylinderResult)){
          $cylinderList=$cylinderRow['cylinderID'];
					if($cylinderRow['gasID']=$gasRow['gasID']){
						if($b==$max){
							$pdf->AddPage();
							//print column titles for the current page
							$pdf->SetY($y_axis_initial);
							$pdf->SetX(55);
							$y_axis=78;
							$row_height = 8;
							//Go to next row
							$y_axis = $y_axis + $row_height;

							//Set $i variable to 0 (first row)
							$b = 0;
						}
						$pdf->SetFillColor(255,255,255);
						$pdf->SetFont('Arial', '', 13);
						$pdf->SetY($y_axis);
						$pdf->SetX(55);
						$pdf->MultiCell(100, 10,$cylinderList, 1, 'C', 1);
					 	//unset($cylinderList);
						//Go to next row
						$y_axis = $y_axis + $row_height;
						$b= $b + 1;
					}
        }
				$y_axis = $y_axis + $row_height+4;
	}
  //Send file
  $pdf->Output();
  ob_end_flush();


?>
