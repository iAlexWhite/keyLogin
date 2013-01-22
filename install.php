<?php
/**
 * @author Alex White (iAlex)
 * @version 1.0
 * @link http://projects.white.so/keyLogin/
 */
?>
<?php
	include ('includes/config.php');
	$phpVersion = phpversion();
	$phpCheck = seValid($phpVersion);
	$sqlVersion = mysql_get_client_info();
	$sqlCheck = seValid($sqlVersion);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Web Installer | keyLogin</title>
		<link rel="stylesheet" href="themes/<?php echo $usingTheme; ?>/<?php echo $usingTheme; ?>.css" type="text/css"/>
	</head>
	
	<body>
		<div class="logo"><img src="themes/<?php echo $usingTheme; ?>/images/logo.png"/></div>
		<div class="container">
			<p>Web Installer | keyLogin</p>
			
			<?php
				if (!empty($_POST['username']) && !empty($_POST['password']))
				{
					$adminUser = $_POST['username'];
					$postPass = $_POST['password'];
					$adminPass = $log->passKey($adminUser, $_POST['password'], CYCLE_ONE, CYCLE_TWO);
					$adminEmail = $_POST['email'];
					$created = date('d-m-Y', time());
				
					$userQuery = "CREATE TABLE IF NOT EXISTS `users` (
   						`id` int(16) not null auto_increment,
   						`username` varchar(16),
   						`password` varchar(72),
						`email` varchar(64),
   						`level` int(2),
						`lastlogon` varchar(12),
   						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
				
					mysql_query($userQuery) or die(mysql_error());
					echo "<span style='color:green;'><p>Users Table Installed<br /></p></span>";
			
					mysql_query("INSERT INTO users (username, password, email, level, lastlogon) VALUES ('$adminUser', '$adminPass', '$adminEmail', '5', '$created')") or die(mysql_error());
					echo "<span style='color:green;'><p>Admin User has been added</p></span>";
			
					echo '<p>keyLogin has been installed successfully.</p>';
					echo '<p><a href="index.php">Login</a><br />(Username: ' . $adminUser . ' Password: ' . $postPass . ')</p>';
				}
			?>
			
			<div align="center" valign="top" class="content">
				<form method="post" action="install.php?p=t">
					<p>Admin Username:<br />
					<input type="text" name="username" /></p>

					<p>Admin Password:<br />
					<input type="password" name="password" /></p>
					
					<p>Admin Email:<br />
					<input type="email" name="email" /></p>

					<p><input type="submit" class="minimal" value="Install keyLogin" /></p>
				</form>
			</div>
		</div>
		<div class="container">
			<?php
				/* Check version of PHP */
				if ($phpCheck)
				{
					$phpVersion = '<span style="color:green;">' . $phpVersion . '</span><img src="themes/default/images/green.png" class="goRight">';
				}
				else
				{
					$phpVersion = '<span style="color:orange;">' . $phpVersion . '</span><img src="themes/default/images/help.png" class="goRight">';
				}
				/* Check version of MySQL */
				if ($sqlCheck)
				{
					$sqlVersion = '<span style="color:green;">' . $sqlVersion . '</span><img src="themes/default/images/green.png" class="goRight">';
				}
				else
				{
					$sqlVersion = '<span style="color:orange;">' . $sqlVersion . '</span><img src="themes/default/images/help.png" class="goRight">';
				}
				/* Check for PDO */
				if (gotPDO())
				{
					$pdoResult = '<span style="color:green;">enabled</span><img src="themes/default/images/green.png" class="goRight">';
				}
				else
				{
					$pdoResult = '<span style="color:orange;">disabled</span><img src="themes/default/images/help.png" class="goRight">';
				}
				/* Are we using MySQL? */
				$MySQL = '<span style="color:green;">enabled</span><img src="themes/default/images/green.png" class="goRight">';				
			?>
			<p>PHP Version is: <?php echo $phpVersion; ?></p>
			<p>PDO Extension is: <?php echo $pdoResult; ?></p>
			<hr>
			<p>MySQL Version is: <?php echo $sqlVersion; ?></p>
			<p>MySQL is: <?php echo $MySQL; ?></p>		
		</div>
		<div class="copyright">
			<p>Copyright &copy; <?php echo date("Y"); ?> | kLv<?php echo $logVersion; ?></p>
		</div>
	</body>
</html>

<?php
	/* PHP and MySQL versions over 5.0 are okay with me! */
	function seValid($vers)
	{
		if ($vers > 5.0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	/* Check for PDO */
	function gotPDO()
	{
		if (defined('PDO::ATTR_DRIVER_NAME'))
		{
			return true;
		}
		else
		{
			return false;
		}
	}