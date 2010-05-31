<html>
<head>
<title>Tipper Setup</title>
</head>

<body>

<?php


include_once ("../config/config.php");
include_once ("../classes/Database.php");
include ("create_tables.sql");
include ("data.php");

switch ($_POST[nextstep])
{
	case 1:
		step1();
		break;
	case 2:
		step2();
		break;
	default:
		step0();
}


function step0()
{
	echo "This will setup a new game. Make sure that the configuration is properly set up in the conig/config.php file.<br>
		  To start the setup press this button:<br>
		  <form action='#' method='post'>
		  <input type='hidden' name='nextstep' value='1'>
		  <input type='submit' name='ok' value='Start setup'>;
		  </form>";
}

function step1()
{
	try
	{
		echo "Creating Database...<br>";
		Database::CreateDatabase(DB_DBNAME);
		echo "Database created.";
	}
	catch(ExceptionDatabase $e)
	{
		echo "Database Error: ", $e->getMessage(), "\n";
		die();
	}
	catch(Exception $e)
	{
		echo "Error:<br>";
		die();
	}
	
	try
	{
		echo "Creating database tables...<br>";
		$db = new Database();
		$db->MultiQuery(CREATE_TABLE_QUERY);
		echo "Database tables created.";
		echo "To continue filling in default values defined in data.php :<br>
		  <form action='#' method='post'>
		  <input type='hidden' name='nextstep' value='2'>
		  <input type='submit' name='ok' value='CLICK HERE'>;
		  </form>";
	}
	catch(ExceptionDatabase $e)
	{
		echo "Database Error: ", $e->getMessage(), "\n";
	}
	catch(Exception $e)
	{
		echo "Error:<br>";
	}
}

function step2()
{
	global $teams;
	global $matchdays;
	global $adminusers;

	$db = new Database();
	
	if (!isset($adminusers))
	{
		echo "No admin useres defined in data file! Thats bad. At least one admin user is necessary!\n";
		die();
	}
	else
	{
		foreach($adminusers AS $adminuser)
		{
			$pwmd5 = md5($adminuser[1]);
			$db->Query("INSERT INTO `user` (`login`, `passwort`,`name`, `email`, `adminlevel`, `wettbewerb`) VALUES " .
				"('$adminuser[0]', '$pwmd5','$adminuser[2]', '$adminuser[3]', 2, 0);");
			echo "Added admin user $adminuser[2] <br>";
		}
	
	}
	
	if (!isset($teams))
	{
		echo "No teams defined in data file. <br>";
	}
	else
	{
		foreach($teams AS $team)
		{
			$db->Query("INSERT INTO `laender` (`land`, `feedname`, `meisterstatus`) VALUES ('$team[0]', '$team[1]', 0);");
			echo "Added team $team[0] <br>";
		}
	}
	
	if (!isset($matchdays))
	{
		echo "No matchdays defined in data file. <br>";
	}
	else
	{
		foreach($matchdays AS $matchday)
		{
			$db->Query("INSERT INTO `matchdays` (`name`) VALUES ('$matchday[0]');");
			echo "Added matchday $matchday[0] <br>";
		}
	
	}

}
	
?>



</body>
</html>