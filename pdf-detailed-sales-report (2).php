
<?php
	require_once('pentagas-connect.php');
	session_start();
	
	function getOrdersFromDateRange($orderID, $startDate, $endDate) {
		return $query = "SELECT o.orderID, cus.name, o.orderDate, od.quantity, gt.gasType, gt.gasName, gt.gasID
						   FROM orders o JOIN orderDetails od ON o.orderID = od.orderID
								 		 JOIN deliveryDetails dd ON od.orderDetailsID = dd.orderDetailsID
										 JOIN customers cus ON cus.customerID = o.customerID
										 JOIN gasType gt ON od.gasID = gt.gasID
									    WHERE o.orderDate >= '{$startDate}'
										  AND o.orderDate <= '{$endDate}'
										  AND o.orderID = '{$orderID}'
									 GROUP BY gt.gasID";	
	}

	function getOrderDetails($orderID) {
		return $query =" SELECT cus.name, o.orderDate
							FROM orders o JOIN customers cus on o.customerID = cus.customerID
						   WHERE o.orderID = '{$orderID}'";
	}


	function getPrice($gasID) {
		return $query = " SELECT gpa.price
							FROM gaspricingaudit gpa JOIN gasType gt ON gpa.gasID = gt.gasID
						   WHERE gpa.gasID = '{$gasID}'
					    ORDER BY gpa.auditID DESC
					       LIMIT 1";
	}


	if(isset($_GET['orderID'])){
		$orderID = $_GET['orderID'];
		$_SESSION['orderID'] = $orderID;	
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
	$pdf->AddPage('P');

	// PAGE HEADER
	// Logo
	$pdf->Image('pentagon_png.png',72,10,65); //CHANGE PATH NAME TO RUN !! 
	$pdf->Ln(40);
	$pdf->SetFont('Arial','B',15);
	$pdf->Cell(80);
	// Title
	$pdf->Cell(30,10,'Detailed Sales Report',0,0,'C');
	$pdf->Ln(6);
	$pdf->SetFont('Arial', '', 11);
	$pdf->Cell(190,10, 'Generated: ' . $timestamp ,0,0,'C');

	//Print Sales Details
	$orderDetailsResult = mysqli_query($dbc,getOrderDetails($_SESSION['orderID']));
	$orderDetails=mysqli_fetch_array($orderDetailsResult,MYSQL_ASSOC);
	$pdf->Ln(10);
	$pdf->SetX(15);
	$pdf->SetFont('Arial', '', 13);
	$pdf->Cell(100,15, 'Order Number  : ' . $_SESSION['orderID'] ,0,0,'L');	//ORDER NUMBER 	:
	$pdf->Ln(6);
	$pdf->SetX(15);
	$pdf->Cell(90,15, 'Customer         : ' . $orderDetails['name'] ,0,0,'L');	//CUSTOMER 		:
	$pdf->Ln(6);
	$pdf->SetX(15);
	$pdf->Cell(90,15, 'Order Date       : ' . $orderDetails['orderDate'] ,0,0,'L');	//ORDER DATE 	:	
	$pdf->Ln(6);


	//set initial y axis position per page
	$y_axis_initial = 70;

	//print column titles
	$pdf->SetFillColor(232, 232, 232);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->SetY(90);
	$pdf->SetX(15);
	$pdf->Cell(60, 6, 'Gas Ordered', 1, 0, 'L', 1);
	$pdf->Cell(40, 6, 'Qty. of Cylinders', 1, 0, 'L', 1);
	$pdf->Cell(40, 6, 'Unit Price', 1, 0, 'L', 1);
	$pdf->Cell(40, 6, 'Total Price', 1, 0, 'L', 1);
	$y_axis=90;
	$row_height = 6;
	$y_axis = $y_axis + $row_height;
	          
	//initialize counter
	$i = 0;
	//Set maximum rows per page
	$max = 25;
	//Set Row Height
	$row_height = 6;

	if(isset($_SESSION['enddate']) && isset($_SESSION['startdate'])){
		$ordersResult = mysqli_query($dbc,getOrdersFromDateRange($_SESSION['orderID'], $_SESSION['startdate'], $_SESSION['enddate']));
		$totalSales = 0;

		while($ordersRow=mysqli_fetch_array($ordersResult,MYSQL_ASSOC)){
			$priceResult = mysqli_query($dbc,getPrice($ordersRow['gasID']));
			$detailsRow=mysqli_fetch_array($priceResult,MYSQL_ASSOC);
			$totalPrice = $detailsRow['price'] * $ordersRow['quantity'];
			$totalPriceFormatted = number_format($totalPrice, 2);

			$totalSales += $totalPrice;

			$gasOrdered = $ordersRow['gasType'] . ' ' . $ordersRow['gasName'];
			$qty = $ordersRow['quantity'];
			$unitPrice = $detailsRow['price'];

			if ($i == $max){
				$pdf->AddPage();
				//print column titles for the current page
				$pdf->SetY($y_axis_initial);
				$pdf->SetX(15);
				//Go to next row
				$y_axis = $y_axis + $row_height;

				//Set $i variable to 0 (first row)
				$i = 0;
			}

			$pdf->SetFillColor(256, 256, 256);
			$pdf->SetY($y_axis);
			$pdf->SetX(15);
			$pdf->Cell(60, 6, $gasOrdered, 1, 0, 'L', 1);			// GAS ORDERED
			$pdf->Cell(40, 6, $qty, 1, 0, 'L', 1);					// QUANTITY
			$pdf->Cell(40, 6, $unitPrice, 1, 0, 'L', 1);			// UNIT PRICE
			$pdf->Cell(40, 6, $totalPriceFormatted, 1, 0, 'L', 1);	// SUM amount of each order

			//Go to next row
			$y_axis = $y_axis + $row_height;
			$i = $i + 1;
		}

		$totalSales = number_format($totalSales, 2);
		$pdf->SetFillColor(256, 256, 256);
		$pdf->SetY($y_axis);
		$pdf->SetX(15);
		$pdf->Cell(140, 6, 'TOTAL: ', 1, 0, 'R', 1);
		$pdf->Cell(40, 6, $totalSales, 1, 0, 'L', 1);		//TOTAL SALES			
	}
	//Send file
	$pdf->Output();
	ob_end_flush();
				
?>