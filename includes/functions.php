<?php
/**
 * @author Alex White (iAlex)
 * @version 1.0
 * @link http://projects.white.so/keyLogin/
 */
?>
<?php
	/* Play around with the features of keyLogin */
	
	/* Upgrade the users level by 1 */
	if (isset($_GET['upgrade']))
	{
		/* Upgrade the user by 1 */
		if (!empty($_GET['user']))
		{
			$user->upgrade($_GET['user']);
		}
		else
		{
			$user->upgrade($_SESSION['user']);
		}
	}
	/* Downgrade the users level by 1 */
	if (isset($_GET['downgrade']))
	{
		/* Downgrade the user by 1 */
		if (!empty($_GET['user']))
		{
			$user->downgrade($_GET['user']);
		}
		else
		{
			$user->downgrade($_SESSION['user']);
		}
	}
	/* Erase the user completely, stops you from removing yourself */
	if (isset($_GET['erase']))
	{
		/* Remove the user */
		if (!empty($_GET['user']) && $_GET['user'] != $_SESSION['user'])
		{
			$user->erase($_GET['user']);
		}
	}
	/* Used for the pagination in listing users */
	if (empty($_GET['page']))
	{
		$page = 1;
	}
	else
	{
		$page = $_GET['page'];
	}
?>