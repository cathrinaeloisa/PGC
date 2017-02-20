<?php
	require_once('pentagas-connect.php');
	session_start();
	
	if (isset($_POST['login'])) {
		$message = NULL;

		if (empty($_POST['userName'])) {
			$_SESSION['userName'] = FALSE;
			$message = 'Please enter username.';
		} else if (empty($_POST['password'])){
			$_SESSION['password']=FALSE;
			$message = 'Please enter password.';
		} else {
			$_SESSION['userName'] = $_POST['userName'];
			$password=$_POST['password']; 
			$userName = $_SESSION['userName'];

			$query = "SELECT * FROM useraccounts WHERE username = '{$userName}' AND password = PASSWORD('{$password}')";
			$result = mysqli_query($dbc,$query);

			if (mysqli_num_rows($result) > 0) {
				echo "ENTER";
				while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
					$_SESSION['userTypeID'] = $row['userTypeID'];
					$_SESSION['userID'] = $row['userID'];
					$_SESSION['name'] = $row['name'];
					if ($row['userTypeID'] == 101) {
						header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/administrative-manager-home.php");
					} else if ($row['userTypeID'] == 102) {
						header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/sales-and-marketing-home.php");
					} else if ($row['userTypeID'] == 103) {
						header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/billing-clerk-home.php");
					} else if ($row['userTypeID'] == 104) {
						header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/cylinder-control-home.php");
					} else if ($row['userTypeID'] == 105) {
						header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/dispatcher-home.php");
					} else if ($row['userTypeID'] == 106) {
						header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/production-manager-home.php");
					}
				}
			} else $message = "Username and password do not match.";

		}
	}
?>

<html>
	<head>
		<title>Pentagon Gas Corporation Home</title>
		<link rel="stylesheet" href="CSS/index.css" >
		<link rel="stylesheet" type="text/css" href="CSS/pure-release-0.6.0/pure-min.css">
	</head>
	
	<body>
		<div class="main-container">
			<div class="pure-g">
				<div class="pure-u-5-24"></div>
				<div class="pure-u-1-24"></div>
				<div class="pure-u-5-24">
					<div align="center">
						<div class="logo-container">
							<img class="logo-edit" src="pentagon_png.png">
						</div>
					</div>
				</div>
				<div class="pure-u-1-24"></div>
				<div class="pure-u-1-24 line"></div>
				<div class="pure-u-4-24">
					<div class="test">
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="pure-form pure-form-aligned">
							<div class="pure-control-group">
								<input class="fields" type="text" name="userName" placeholder="USERNAME" value="<?php if (isset($_POST["userName"]))
									echo $_POST["userName"]; ?>">
							</div>

							<div class="pure-control-group">
								<input class="fields" id="password" type="password" name="password" placeholder="PASSWORD">
							</div>

							<div>
								<div class="error-container">
									<?php if (isset($message)){
										echo '<p>'.$message.'</p>';
									}?>
								</div>
								<input  class="button" type="submit" name="login" value="LOG IN">
							</div> 
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>