<?php
/**
 * @author Alex White (iAlex)
 * @version 1.0
 * @link http://projects.white.so/keyLogin/
 */
?>
<?php include('includes/check.php'); ?>
<!-- This is all you need, make sure it is right at the top of the page -->

<?php include('includes/functions.php'); ?>
<!-- Lets us upgrade, downgrade and remove users -->

<?php
	/* We can use this to restrict certain levels (0 and above) */
	if (!$user->onlyLevel(0, $user->getLevel($_SESSION['user'])))
	{
		die($error->returnError(10));
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
			<p align="center">
				<b>Test Page</b><br /><br />
				<span style="float:left;">
					<img src="<?php echo $log->getGravatar($_SESSION['user']); ?>">
				</span>
				<br />
				<span style="float:left; padding-left:10px;">
					User: <?php echo $_SESSION['user']; ?>
				</span>
			</p>
			<br />
			<p align="center">
				<a href="?logout"><button class="mini">Logout</button></a>
			</p>
		</div>
		
		<div class="container">
			<p align="center">
				<b>User Control</b><br /><br />
				<a href="?upgrade"><button class="mini">Upgrade</button></a>
				<a href="?downgrade"><button class="mini">Downgrade</button></a>
				<br /><br />
				You are level <?php echo $user->getLevel($_SESSION['user']); ?>
			</p>
		</div>
		
		<div class="container">
			<p align="center">
				<b>User List</b><br /><br />
				<?php $user->listUsers($page, 3, true); ?>
			</p>
		</div>
	</body>
</html>