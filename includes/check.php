<?php
/**
 * @author Alex White (iAlex)
 * @version 1.0
 * @link http://projects.white.so/keyLogin/
 */
?>
<?php
	include('config.php');

	/* If not logged in, send them to the login page */
	if (!isset($_SESSION['logged_in']))
	{
		$_SESSION['notification'] = $error->returnError(5);
		header("Location: $mainPath");
	}

	/* Check if they're logging out */
	if (isset($_GET['logout']))
	{	
		unset($_GET['logout']);
		session_destroy();
		session_start();
		$_SESSION['notification'] = $error->returnError(4);
		header("Location: $mainPath");
	}
?>