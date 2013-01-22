<?php
/**
 * @author Alex White (iAlex)
 * @version 1.0
 * @link http://projects.white.so/keyLogin/
 */
?>
<?php include('includes/check.php'); ?>
<!-- This is all you need, make sure it is right at the top of the page -->

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
				<b>My Profile</b><br /><br />
				<span style="float:left;">
					<img src="<?php echo $user->getGravatar($_SESSION['user']); ?>">
				</span>
				<br />
				<span style="float:left; padding-left:10px;">
					User: <?php echo $_SESSION['user']; ?>
				</span>
				<br />
				<span style="float:left; padding-left:10px;">
					<a href="">Change Password</a>
				</span>
			</p>
			<br />
			<p align="center">
				<a href="?logout"><button class="mini">Logout</button></a>
			</p>
		</div>
	</body>
</html>