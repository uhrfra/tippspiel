<html>
<head>
<title>Tipper Setup</title>
</head>

<body>

<?php


include_once ("../config/config.php");
include_once ("../classes/Database.php");
include_once("../classes/Matches.php");
include ("create_tables.sql");

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
		echo "Now database game is setup:<br>
		  <form action='#' method='post'>
		  <p>Adminlogin:<br><input name='adminlogin' type='text' size='30'></p>
		  <p>Adminpassword:<br><input name='adminpw' type='text' size='30'></p>
		  <p>Adminname:<br><input name='adminname' type='text' size='30'></p>
		  <p> Enter teams linewise: </p>
		  <textarea name='teams' cols='80' rows='10'></textarea>
		  <p> Enter groups linewise: </p>
   	   	<textarea name='groups' cols='80' rows='10'></textarea>
          <p> Enter matches linewise with the format: YYYY-MM-DD HH:MM; Group; Team1 : Team2</p>
          <textarea name='matches' cols='80' rows='20'></textarea>
   		  <input type='hidden' name='nextstep' value='2'>
     <p></p>
	 <input type='submit' name='ok' value='OK'>
  </p>
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
	$db = new Database();
	$matchesobj = new Matches();
	
	$userCount = $db->queryResult("SELECT COUNT(*) FROM user;");

	if ($userCount > 0)
	{
		echo $userCount;
		echo "Users already set up.";
		die();
	}
	else
	{

		$adminlogin = $_POST['adminlogin'];
		$adminname = $_POST['adminname'];
		$pwmd5 = md5($_POST['adminpw']);
		$db->Query("INSERT INTO `user` (`login`, `passwort`,`name`, `email`, `adminlevel`, `wettbewerb`) VALUES ('$adminlogin', '$pwmd5','$adminname', '', 2, 0);");
		echo "Added admin user $adminuser[2] <br>";
	}
	
	$teams = $_POST['teams'];
	foreach(preg_split('~[\r\n]+~', $teams) as $team)
	{
		$db->Query("INSERT INTO `laender` (`land`, `feedname`, `meisterstatus`) VALUES ('$team', '$team', 0);");
		echo "Added team $team <br>";
	}

	$groups = $_POST['groups'];
	foreach(preg_split('~[\r\n]+~', $groups) as $group)
	{
		$db->Query("INSERT INTO `matchdays` (`name`) VALUES ('$group');");
		echo "Added matchday $group <br>";
	}

	$matches = $_POST['matches'];
	foreach(preg_split('~[\r\n]+~', $matches) as $match)
	{
		$matchitems = preg_split('/[;]/', $match);
		try
		{
			$m = new Match();
			$m->teamname1 = trim($matchitems[2]);
			$m->teamname2 = trim($matchitems[3]);
			$m->matchdayid = trim($matchitems[1]);
			$m->datetime = trim($matchitems[0]);
			$matchesobj->addMatchByNames($m);
			echo "Added match: ", $match,"<br>";
		}
		catch (ExceptionMatch $e)
		{
			echo "Error adding match ", " datetime = (", $matchitems[0], ") group = (", $matchitems[1], ") team1 = (", $matchitems[2], ") team2 = (", $matchitems[3], ") <br>";
		}
	}
}
	
?>



</body>
</html>