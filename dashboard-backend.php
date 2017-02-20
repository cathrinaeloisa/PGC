<?php

	// TOTAL CYLINDER BALANCE WITH CUSTOMERS
	function getTotalCylinderBalanceWithCustomers() {
		require('pentagas-connect.php');
		$query = "SELECT COUNT(DD.cylinderID) AS 'cylindercount', C.name AS 'customername'
					FROM orderdetails OD JOIN orders O ON OD.orderID = O.orderID 
										 JOIN customers C ON O.customerID = C.customerID
                                         JOIN deliverydetails DD ON OD.orderDetailsID = DD.orderDetailsID
				   WHERE DD.pickedupdate IS NULL
				   GROUP BY C.name";
		return $result = mysqli_query($dbc,$query);
	}

	// TOTAL CYLINDER BALANCE WITH PGC
	function getTotalCylinderBalanceWithPGC() {
		require('pentagas-connect.php');
		$query = "SELECT COUNT(c.cylinderID) AS 'cylindercount', gt.gasType, gt.gasName
					FROM cylinders c JOIN gastype gt ON gt.gasID = c.gasID
				   WHERE c.cylinderStatusID = 401 OR c.cylinderStatusID = 402
                   GROUP BY gt.gasType, gt.gasName";
		return $result = mysqli_query($dbc,$query);
	}

	function chartValues() {
		require('pentagas-connect.php');
		$dataResult = array();

		$result = getTotalCylinderBalanceWithCustomers();
		foreach ($result as $row) {
			$dataResult[] = $row;
		}

		print json_encode($dataResult);
	}
?>