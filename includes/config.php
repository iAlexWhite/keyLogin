<?php
/**
 * @author Alex White (iAlex)
 * @version 1.0
 * @link http://projects.white.so/keyLogin/
 */
?>
<?php
	session_start();
	
	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	/* Declare our Classes */
	$log = new logFunctions();
	$user = new userControl();
	$error = new errorReport();
	
	/* Define the hash cycles, numbers from 15-80000 - do NOT change after install -> they do not have to be the same */
	define('CYCLE_ONE', '80000'); //Using more than 80000 each will slow the hashing down noticeably
	define('CYCLE_TWO', '80000');

	/* Connect to the database */
	define ( 'DB_HOSTNAME', 'localhost' );
	define ( 'DB_USERNAME', '' );
	define ( 'DB_PASSWORD', '' );
	define ( 'DB_NAME', '' );

	/* Path to keyLogin with following / */
	$mainPath = 'http://www.projects.white.so/keyLogin/';

	/* keyLogin will redirect here after logging in */
	$redirectTo = $mainPath . 'profile.php';
	
	/* Which theme are you using? (See docs for more information) */
	$usingTheme = 'default';
	
	/* keyLogin Version */
	$logVersion = '1.0';
	
	/* Do not edit anything below this unless you know what you are doing */
	mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD) or die("MySQL Error: " . mysql_error());
	mysql_select_db(DB_NAME) or die("MySQL Error: " . mysql_error());
	$dbh = new PDO("mysql:host=".DB_HOSTNAME.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
	
	class logFunctions
	{
		/* Hash the password a bit */
		function passKey($kUser, $kPass, $CycleOne, $CycleTwo)
		{
			$fHash = sha1($kUser . $CycleOne);
			$sHash = sha1($kPass . $CycleTwo);
		
			for ($i = 0; $i < $CycleOne; $i++) {
				$firstCycle = sha1($kUser . $CycleTwo);
			}
		
			for ($j = 0; $j < $CycleTwo; $j++) {
				$secondCycle = sha1($kPass . $CycleOne);
			}
		
			return sha1($firstCycle . $secondCycle);
		}
		
		/* Just a very simple validation option */
		function validateUser($user)
		{
			if(preg_match('/[^0-9A-Za-z]/', $user))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		
		/* Just a very simple sanitize option */
		function simpleSanitize($inputData)
		{
			$inputData = strip_tags(mysql_real_escape_string($inputData));
		return $inputData;
		}
		
		/* Simple email validation */
		function isEmail($email)
		{
			if (filter_var($email, FILTER_VALIDATE_EMAIL))
			{
  				return 1;
			}
			else
			{
  				return 0;
			}
		}
	}
	
	class userControl
	{
		/* Connect funtion */
		function connect()
		{
			$dbh = new PDO("mysql:host=".DB_HOSTNAME.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
		}
		
		/* Used to upgrade a user */
		function upgrade($user)
		{
			global $dbh;
			$this->connect();
			$stmt = $dbh->prepare("SELECT id, username, password, email, level, points FROM users WHERE username = :username");
			$stmt->bindParam(':username', $user);
			$stmt->execute();
			$getLevel = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$levelValue = $getLevel[0]['level'] + 1;
			
			$stmt = $dbh->prepare("UPDATE users SET level = :level WHERE username = :username");
			$stmt->bindParam(':level', $levelValue);
			$stmt->bindParam(':username', $user);
			$stmt->execute();
			
			return true;
		}
		/* Used to downgrade a user */
		function downgrade($user)
		{
			global $dbh;
			$this->connect();
			$stmt = $dbh->prepare("SELECT id, username, password, email, level, points FROM users WHERE username = :username");
			$stmt->bindParam(':username', $user);
			$stmt->execute();
			$getLevel = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$levelValue = $getLevel[0]['level'] - 1;
			
			$stmt = $dbh->prepare("UPDATE users SET level = :level WHERE username = :username");
			$stmt->bindParam(':level', $levelValue);
			$stmt->bindParam(':username', $user);
			$stmt->execute();
			
			return true;
		}
		
		/* Used to get the current level of users */
		function getLevel($user)
		{
			global $dbh;
			$this->connect();
			$stmt = $dbh->prepare("SELECT level FROM users WHERE username = :username");
			$stmt->bindParam(':username', $user);
			$stmt->execute();
			$getLevel = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$levelValue = $getLevel[0]['level'];
			
			return $levelValue;
		}
		/* Used to erase a user */
		function erase($user)
		{
			global $dbh;
			$this->connect();
			$stmt = $dbh->prepare("DELETE FROM users WHERE username = :username");
			$stmt->bindParam(':username', $user);
			$stmt->execute();
			
			return true;
		}
		
		/* Restrict lower level users from certain pages */
		function onlyLevel($allow, $userlevel)
		{
			if ($allow <= $userlevel)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		/* Restrict lower points users from certain pages */
		function onlyPoints($allow, $pointlevel)
		{
			if ($allow <= $pointlevel)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		/* Get the users gravatar */
		function getGravatar($user)
		{
			$dbh = new PDO("mysql:host=".DB_HOSTNAME.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
			$stmt = $dbh->prepare("SELECT id, username, password, email, level, points FROM users WHERE username = :username");
			$stmt->bindParam(':username', $user);
			$stmt->execute();
			$getEmail = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$email = $getEmail[0]['email'];
			$hash = md5(strtolower(trim($email)));
			$img = 'http://www.gravatar.com/avatar/' . $hash . '?s=50';
			
			return $img;
		}
		
		/* List all the users */
		function listUsers($page, $amount, $paginate)
		{
			global $usingTheme;
			global $dbh;
			$this->connect();
			
			/* $Other does the pagination working out */
			$Other = 0;
			
			/* Make sure the page is numeric, above 0 and exists before giving it */
			if (!is_numeric($page) || $page < 1 || empty($page))
			{
				$page = 1;
			}
			
			/* If no amount was specified, 6 it is! */
			if (empty($amount))
			{
				$amount = 6;
			}
			
			/* If page is higher than one, get our pagination going! */
			if ($page != 1) {
				$Other = ($page * $amount - $amount);
			}
			
			/* Get the PDO working! */
			$stmt = $dbh->prepare("SELECT username FROM users ORDER BY username ASC LIMIT $Other, $amount");
			$stmt->execute();
			$userInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$countUsers = count($userInfo);
			
			/* No users? */
			if ($countUsers == 0)
			{
				echo '<p>Does not seem to be any users!</p>';
			}
			else
			{					
				/* Get the usernames */
				foreach ($userInfo as $user)
				{
					$username = $user['username'];
					echo '<span style="float:left;">' . $username . '[' . $this->getLevel($username) . ']</span>';
					echo '<span style="float:right;"><a href="?upgrade&user='.$username.'"><img src="themes/'.$usingTheme.'/images/upgrade.png" alt="Upgrade"></a> <a href="?downgrade&user='.$username.'"><img src="themes/'.$usingTheme.'/images/downgrade.png" alt="Downgrade"></a> <a href="?erase&user='.$username.'"><img src="themes/'.$usingTheme.'/images/erase.png" alt="Erase User"></a></span><br /><br />';
				}
				
				if ($paginate)
				{
					$stmt = $dbh->prepare("SELECT username FROM users ORDER BY username ASC");
					$stmt->execute();
					$userCount = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
					/* Count the amount of users */
					$count = count($userCount);
					
					/* Divide them by the amount per page */
					$pageAmount = ceil($count/$amount);
					
					/* Paginate them! */
					if ($page == 1 && $count > $amount)
					{
						$page = $page + 1;
						echo "<p><span style='float:right;'><a href='?page=$page'>Next</a></span></p>";
					}
					elseif ($page == $pageAmount && $count > $amount)
					{
						$page = $page - 1;
						echo "<p><span style='float:left;'><a href='?page=$page'>Previous</a></span></p>";
					}
					else
					{
						$minus = $page - 1;
						$plus = $page + 1;
						echo "<p><span style='float:left;'><a href='?page=$minus'>Previous</a></span><span style='float:right;'><a href='?page=$plus'>Next</a></span></p>";
					}
				}
			}
		}
	}
	
	class errorReport
	{
		function returnError($i)
		{
			switch ($i)
			{
				case 0:
					$msg = "No credentials given.";
					return $msg;
					break;
				case 1:
					$msg = "Wrong username or password.";
					return $msg;
					break;
				case 2:
					$msg = "You cannot register if you are not using MySQL.";
					return $msg;
					break;
				case 3:
					$msg = "Please enter a valid email.";
					return $msg;
					break;
				case 4:
					$msg = "You have been logged out.";
					return $msg;
					break;
				case 5:
					$msg = "You need to log in to access that page.";
					return $msg;
					break;
				case 6:
					$msg = "Sorry, that username is taken.";
					return $msg;
					break;
				case 7:
					$msg = "Account created successfully.";
					return $msg;
					break;
				case 8:
					$msg = "Sorry an error has occured!";
					return $msg;
					break;
				case 9:
					$msg = "You must fill in all the fields.";
					return $msg;
					break;
				case 10:
					$msg = "You do not have permission to view this page.";
					return $msg;
					break;
				case 11:
					$msg = "That username is invalid.";
					return $msg;
					break;
			}
		}
	}
?>