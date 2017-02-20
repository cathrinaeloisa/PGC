<?php
$timestamp = NULL;
$message = NULL;


	session_start();

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
				<div class="content-container">
					<!-- TITLE -->
					<div class="page-title-container">
						<p class="title">Daily Cylinder Status Report </p>
					</div>

					<!-- TIMESTAMP FOR REPORT-->
					<div class="divider">
						<?php
							date_default_timezone_set('Asia/Manila');
							$timestamp = date("F j, Y // g:i a");
							echo '<b>' .$timestamp. '</b>';
						?>
					</div>

          <?php
          require_once('pentagas-connect.php');
          $query = "SELECT * from cylinders c
                        join gastype gt on c.gasID=gt.gasID
                        join cylinderstatus cs on c.cylinderStatusID=cs.cylinderStatusID
                " ;

            $result = mysqli_query($dbc,$query);
            echo '<table class="pure-table" id ="Table";>
                  <thead>
                    <th style="text-align:center">Cylinder ID</th>
                    <th style="text-align:center">Gas</th>
                    <th style="text-align:center">Cylinder Status</th>
                  </thead>';


            if(!isset($message)){
              while($row=mysqli_fetch_array($result)){
                $blank=" ";
                echo "<tr class=\"pure-table-odd\">
                <td width=\"20%\"><div align=\"center\">{$row['cylinderID']}
                <td width=\"20%\"><div align=\"center\">{$row['gasType']} {$blank} {$row['gasName']}
                <td width=\"20%\"><div align=\"center\">{$row['cylinderStatusDescription']}
                </div></td>
                </tr>";
              }
            }
          ?>

				</div>
			</div>
		</div>
	</body>
</html>
