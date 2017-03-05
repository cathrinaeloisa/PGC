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
	require('fpdf.php');
	ob_start();

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
	$pdf->AddPage('P');

  $y_axis_initial = 70;
 // //print column titles
 // // $pdf->SetFillColor(232, 232, 232);
 // // $pdf->SetFont('Arial', 'B', 12);
 // $pdf->SetY($y_axis_initial);
 // $pdf->SetX(30);

 // $gName=$gasRow['gasType']." ".$gasRow['gasName'];
 $pdf->SetFillColor(256, 256, 256);
 $pdf->SetFont('Arial', 'B', 15);
 // $pdf->Cell(40, 6, $gName, 1, 0, 'L', 1);
 $y_axis=90;
 $row_height = 6;
 $y_axis = $y_axis + $row_height;
 $i = 0;

 //Set maximum rows per page
 $max = 30;
 //Set Row Height
 $row_height = 6;
	//Send file
	$gasResult = mysqli_query($dbc, getGasNames($_SESSION['select-status']));
  $pdf->SetY(75);
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
      $pdf->Ln();
      while($cylinderRow=mysqli_fetch_array($cylinderResult)){
          $cylinderList[]=$cylinderRow['cylinderID'];
        }
        for ($i = 0; $i < $rowCount;) {
          for ($j = $i, $counter = 0; $counter < 5; $counter++, $j++) {
            if ($j < $rowCount){
              $pdf->SetFillColor(255,255,255);
              $pdf->SetFont('Arial', '', 13);
              $pdf->SetTextColor(0);
              $pdf->SetX(55);
              $pdf->Cell(100, 7, $cylinderList[$j], 1, 0, 'C', 1);
              $pdf->Ln();
            }
          }
          $i += 5;
          if($i==$max){
            $pdf->AddPage();
            //print column titles for the current page
            $pdf->SetY($y_axis_initial);
            $pdf->SetX(55);
            // $pdf->Cell(35, 6, 'Gas', 1, 0, 'L', 1);
            $pdf->SetFillColor(232, 232, 232);
            $pdf->Cell(100, 10, $gName, 1, 0, 'C', 1);
            $y_axis=75;
            $row_height = 6;
            $pdf->Cell(100, 7, $cylinderList[$j], 1, 0, 'C', 1);
            //Go to next row
            $y_axis = $y_axis + $row_height;

            //Set $i variable to 0 (first row)
            $i = 0;
          }
        }
        unset($cylinderList);

      // if($i==$max){
      //   $pdf->AddPage();
      //   //print column titles for the current page
      //   $pdf->SetY($y_axis_initial);
      //   $pdf->SetX(90);
      //   // $pdf->Cell(35, 6, 'Gas', 1, 0, 'L', 1);
      //   $pdf->SetFillColor(232, 232, 232);
      //   $pdf->Cell(40, 6, $gName, 1, 0, 'L', 1);
      //   $y_axis=100;
      //   $row_height = 6;
      //   //Go to next row
      //   $y_axis = $y_axis + $row_height;
      //
      //   //Set $i variable to 0 (first row)
      //   $i = 0;
      // }


      //Go to next row
      $y_axis = $y_axis + $row_height;
      $i = $i + 1;
	}
  //Send file
  $pdf->Output();
  ob_end_flush();


?>
