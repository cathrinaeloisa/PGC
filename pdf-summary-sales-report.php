
<?php
	require_once('pentagas-connect.php');
	session_start();

	function getOrdersFromDate($date) {
		return $query = "SELECT o.orderID, cus.name, o.orderDate
						   FROM orders o JOIN orderDetails od ON o.orderID = od.orderID
								 		 JOIN deliveryDetails dd ON od.orderDetailsID = dd.orderDetailsID
										 JOIN customers cus ON cus.customerID = o.customerID
									    WHERE o.orderDate = '{$date}'
									GROUP BY o.orderID";
	}

	function getOrderDetails($orderID) {
		return $query =" SELECT od.gasID, od.quantity
							FROM orders o JOIN orderdetails od on o.orderID = od.orderID
						   WHERE o.orderID = '{$orderID}'
						GROUP BY od.gasID";
	}

	function getPrice($gasID) {
		return $query = " SELECT gpa.price
							FROM gaspricingaudit gpa JOIN gasType gt ON gpa.gasID = gt.gasID
						   WHERE gpa.gasID = '{$gasID}'
					    ORDER BY gpa.auditID DESC
					       LIMIT 1";
	}

	if(isset($_GET['orderDate'])){
		$orderDate = $_GET['orderDate'];
		$_SESSION['orderDate'] = $orderDate;
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

	// Page header
	// Logo
	$pdf->Image('pentagon_png.png',72,10,65); //CHANGE PATH NAME TO RUN !!
	$pdf->Ln(40);
	$pdf->SetFont('Arial','B',15);
	$pdf->Cell(80);
	// Title
	$pdf->Cell(30,10,'Summary Sales Report for ' . $_SESSION['orderDate'],0,0,'C');
	// Line break
	$pdf->Ln(6);
	$pdf->SetFont('Arial', '', 11);
	$pdf->Cell(190,10, 'Generated: ' . $timestamp ,0,0,'C');

	//set initial y axis position per page
	$y_axis_initial = 70;

	//print column titles
	$pdf->SetFillColor(232, 232, 232);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->SetY(70);
	$pdf->SetX(15);
	$pdf->Cell(40, 6, 'Order ID', 1, 0, 'L', 1);
	$pdf->Cell(60, 6, 'Customer Name', 1, 0, 'L', 1);
	$pdf->Cell(40, 6, 'Order Date', 1, 0, 'L', 1);
	$pdf->Cell(40, 6, 'TOTAL SALES', 1, 0, 'L', 1);
	$y_axis=70;
	$row_height = 6;
	$y_axis = $y_axis + $row_height;

	//initialize counter
	$i = 0;
	//Set maximum rows per page
	$max = 25;
	//Set Row Height
	$row_height = 6;
	if(!isset($message) && isset($_SESSION['enddate']) && isset($_SESSION['startdate'])){
		$totalSales = 0;
		$orderRangeQueryResult = mysqli_query($dbc,getOrdersFromDate($_SESSION['orderDate'])); //GETTING ORDERS FROM SPECIFIED DATE
		while($orders=mysqli_fetch_array($orderRangeQueryResult,MYSQL_ASSOC) ){ //LOOPING THROUGH ORDERS THAT ARE WITHIN DATE
			$detailsQueryResult = mysqli_query($dbc,getOrderDetails($orders['orderID']));
			while($details=mysqli_fetch_array($detailsQueryResult,MYSQL_ASSOC) ){ //LOOPING THROUGH GASES FROM ORDER TO GET TOTAL PRICE
				$sum = 0;
				$priceQueryResult = mysqli_query($dbc,getPrice($details['gasID']));
				$price = mysqli_fetch_array($priceQueryResult,MYSQL_ASSOC);

				$sum += $details['quantity'] * $price['price']; //
				$totalSales += $sum;
			}

			$sumFormatted = number_format($sum,2);

			$orderID = $orders['orderID'];
			$customerName = $orders['name'];
			$orderDATE =$orders['orderDate'];

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
			$pdf->Cell(40, 6, $orderID, 1, 0, 'L', 1);			// ORDERID
			$pdf->Cell(60, 6, $customerName, 1, 0, 'L', 1);		// CUSTOMER NAME
			$pdf->Cell(40, 6, $orderDATE, 1, 0, 'L', 1);	// ORDER DATE
			$pdf->Cell(40, 6, $sumFormatted, 1, 0, 'L', 1);	// SUM amount of each order

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
