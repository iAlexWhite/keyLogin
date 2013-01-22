<?php
/**
 * @author Alex White (iAlex)
 * @version 1.0
 * @link http://projects.white.so/keyLogin/
 */
?>
<?php
	include ('includes/config.php');
	if (isset($_SESSION['logged_in']))
	{
		header("Location: $redirectTo");
	}
	else
	{
		session_destroy();
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
  			<form method="post" action="includes/process.php" align="center">
				<p align="center">Your username<br />
				<input align="center" type="text" name="uname" id="uname" /></p>

				<p align="center">Your password<br />
				<input align="center" type="password" name="pass" id="pass" /></p>
				<p align="center"><input type="submit" class="minimal" name="login" id="login" value="Login" /></p>
			</form>
			<p>Not a user? - <a href="register.php">Register here</a></p>
		</div>
	</body>
</html>