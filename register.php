<?php
/**
 * @author Alex White (iAlex)
 * @version 1.0
 * @link http://projects.white.so/keyLogin/
 */
?>
<?php
	include ('includes/config.php');
	
	/* Check if you are already logged in */
	if (isset($_SESSION['logged_in']))
	{
		header("Location: $redirectTo");
	}
	else
	{
		session_destroy();
	}

	/* Something is empty? */
	if (!empty($_POST) && (empty($_POST['uname']) || empty($_POST['pass']) || empty($_POST['email'])))
	{
		$_SESSION['notification'] = $error->returnError(9);
	}
	
	/* Invalid username */
	if (!empty($_POST['uname']) && !empty($_POST['pass']) && !empty($_POST['email']) && !$log->validateUser($_POST['uname']))
	{
		$_SESSION['notification'] = $error->returnError(11);
	}
	
	/* Everything is there! */
	if (!empty($_POST['uname']) && !empty($_POST['pass']) && !empty($_POST['email']) && $log->validateUser($_POST['uname']))
	{	
		/* Sanitize the user and password */
		$username = $log->simpleSanitize($_POST['uname']);
		$password = $log->simpleSanitize($_POST['pass']);
		
		/* Hash the password as best we can, with the salt */
		$saltPassword = $log->passKey($username, $password, CYCLE_ONE, CYCLE_TWO);
		
		/* Get the email, and see if it is valid */
		$email = $_POST['email'];
		$emailCheck = $log->isEmail($email);

		/* Lets do some error checking! ~ woo! */
		$stmt = $dbh->prepare("SELECT id, username, password, email, level FROM users WHERE username = :username");
		$stmt->bindParam(':username', $username);
		$stmt->execute();
		$rowNumber = $stmt->rowCount();

		/* Check if the username is taken already */
		if ($rowNumber == 1)
		{
			$_SESSION['notification'] = $error->returnError(6);
		}
		
		/* Was the email valid? If so - register the user */
		elseif ($emailCheck == 1)
		{
			/* Get the date */
			$created = date('d-m-Y', time());
			
			/* Create this new user */
			$data = array( 'username' => $username, 'password' => $saltPassword, 'email' => $email, 'level' => '0', 'lastlogon' => $created );			
			$stmt = $dbh->prepare("INSERT INTO users (username, password, email, level, lastlogon) VALUES (:username, :password, :email, :level, :lastlogon)");
			$stmt->execute($data);
			
			/* Successfull */
			$_SESSION['notification'] = $error->returnError(7);  	
		}
		else
		{
			/* Invalid email :( */
			$_SESSION['notification'] = $error->returnError(3);
		}
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>keyLogin | Version 1.0</title>
		<link rel="stylesheet" href="themes/<?php echo $usingTheme; ?>/<?php echo $usingTheme; ?>.css" type="text/css"/>
	</head>

	<body>
		<div class="logo"><img src="themes/<?php echo $usingTheme; ?>/images/logo.png"/></div>
		<div class="container">
			<p align="center"><?php if (!empty($_SESSION['notification'])){echo $_SESSION['notification'];} ?></p>
			<form method="post" action="register.php" align="center">
				<p align="center">Username<br />
				<input align="center" type="text" name="uname" id="uname" /></p>

				<p align="center">Password<br />
				<input align="center" type="password" name="pass" id="pass" /></p>
				
				<p align="center">Email<br />
				<input align="center" type="text" name="email" id="email" /></p>
				<p align="center"><input type="submit" class="minimal" name="login" id="login" value="Sign Up" /></p>
			</form>
			<p>Already a user? - <a href="index.php">Login here</a></p>
		</div>
	</body>
</html>