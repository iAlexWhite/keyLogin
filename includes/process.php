<?php
/**
 * @author Alex White (iAlex)
 * @version 1.0
 * @link http://projects.white.so/keyLogin/
 */
?>
<?php
	include('config.php');

	/* Sanitize the username and password as we're using MySQL now! */
	$username = $log->simpleSanitize($_POST['uname']);
	$password = $log->simpleSanitize($_POST['pass']);
		
	/* Need to submit a username and password! */
	if (empty($username) || empty($password))
	{
		$_SESSION['notification'] = $error->returnError(0);
		header("Location: $mainPath");
	}
		
	/* Get the hash of the password, ready to compare */
	$saltPassword = $log->passKey($username, $password, CYCLE_ONE, CYCLE_TWO);
		
	/* See if we can find the user in the database */
	$stmt = $dbh->prepare("SELECT id, username, password, email, level, lastlogon FROM users WHERE username = :username AND password = :password");
	$stmt->bindParam(':username', $username);
	$stmt->bindParam(':password', $saltPassword);
	$stmt->execute();
		
	$rowNumber = $stmt->rowCount();
	$getRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

	/* Does the user exist? */
	if ($rowNumber == 1)
	{
		/* Get the date, and the last logged in date */
		$lastLogin = $getRow[0]['lastlogon'];
		$newLogin = date('d-m-Y', time());
			
		/* Logging in on a new day? */
		if ($lastLogin != $newLogin)
		{
			$stmt = $dbh->prepare("UPDATE users SET lastlogon = :lastlogon WHERE username = :username");
			$stmt->bindParam(':lastlogon', $newLogin);
			$stmt->bindParam(':username', $username);
			$stmt->execute();
		}
		
		/* Set their sessions */
		$_SESSION['user'] = $username;
		$_SESSION['logged_in'] = true;
		header("Location: $redirectTo");
	}
	else
	{
		/* Does not seem to exist */
		$_SESSION['notification'] = $error->returnError(1);
		header("Location: $mainPath");
	}
?>