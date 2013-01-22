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
<!-- Lets us upgrade, downgrade and remove users - as well as list users! -->

<?php
	/* We can use this to restrict certain levels (5 and above) */
	if (!$user->onlyLevel(5, $user->getLevel($_SESSION['user'])))
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
				<b>Manage Users</b><br /><br />
				<!-- $user->listUsers(PAGE, AMOUNT_PER_PAGE, PAGINATE); -->
				<?php $user->listUsers($page, 3, true); ?>
			</p>
			<p align="center">
				<a href="?logout"><button class="mini">Logout</button></a>
			</p>
		</div>
	</body>
</html>