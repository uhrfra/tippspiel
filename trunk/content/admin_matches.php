<?php 
	include_once("../classes/Session.php");
	include_once("../classes/GUIBuilder.php");
	include_once("../classes/Matches.php");
	include_once("../classes/Exceptions.php");
	include_once("../classes/Database.php");

	include ("../layout/pre_content_stuff.php");

	$session = new Session();

	$user = $session->getCurrentUser();


	if ($userid == null)
	{
		// TODO: ERROR
	}
	
	if ($user->adminlevel == 0)
	{
		include ("../layout/post_content_stuff.php");
		exit();
	}
	

	$matches = new Matches();
	$db = new Database();

if (isset($_POST['action']))
{
	if ($_POST['action'] == 'add_game')
	{
		try
		{
			$m = new Match();
			$m->teamid1 = $_POST['team1'];
			$m->teamid2 = $_POST['team2'];
			$m->matchdayid = $_POST['matchday'];
			$m->datetime = $_POST['datetime'];
			$matches->addMatch($m);
		}
		catch (ExceptionMatch $e)
		{
			echo "Spiel nicht hinzugefügt: ", $e->getMessage(), "<br>";
			}
			
	}
	else if ($_POST['action'] == 'set_result')
	{
		$matches->setResult($_POST['matchid'], $_POST['goals1'], $_POST['goals2']);
	}
	
	else if ($_POST['action'] == 'set_teamstatus'){
   
	$status = $_POST['status'];
	$teamid = $_POST['team'];
    $sql = "UPDATE laender SET meisterstatus ='$status' WHERE id='$teamid';";
    
     $db->query($sql);
     $landname = $db->queryResult("SELECT land FROM laender WHERE id = '$teamid'");
	 switch($status)
	 {
		case 0:
		$statustxt = "noch drin";
		break;
		case -1:
		$statustxt = "ausgeschieden";
		break;
		case 1:
		$statustxt = "Sieger";
		break;
		}
  }
}
?>


<p>
<h1> Spiel hinzufügen </h1>

<form id='Form' action='#' method='post'>
<table>
<?php 
$lastdate = "";
$lasttime = "";
$lastmatch = $matches->GetLastMatch2("%Y-%m-%d %H:%i");
$newid = $matches->GetHighestMatchId() +1;
if ($lastmatch)
{
	$lasttime = $lastmatch->datetime;
}

echo "<tr><td>Mannschaften:</td><td>";
GUIBuilder::buildDropdownSelect('team1', 'SELECT land, id FROM laender ORDER BY land;', 0);
echo "  :  ";
GUIBuilder::buildDropdownSelect('team2', 'SELECT land, id FROM laender ORDER BY land;', 0);
echo "</td></tr><tr><td>Spieltag:</td><td>";
GUIBuilder::buildDropdownSelect('matchday', 'SELECT name, id FROM matchdays;', 0);
echo "</td></tr><tr><td>Datum, Uhrzeit (YYYY-MM-TT HH:MM):</td>";
echo "<td><input type ='text' name='datetime' value ='$lasttime'></td></tr>";

echo "<input type='hidden' name='action' value = 'add_game'>";
echo "<input type='hidden' name='newmatchid' value = '$newid'>";
?>
<tr><td><input id='Button' type='submit' name='submit' value = 'Spiel hinzufügen'></td><td></td>
</table>
</form>

</p>


<p>
<h2> Ergebnis eintragen </h2>

<form id='Form' action='#' method='post'>
<select name='matchid'>
	<?php 	
		$ms = $matches->getAllRunningMatches();
		foreach ( $ms as $m)
		{
			echo "<option value='$m->id'> $m->teamname1 - $m->teamname2, $m->datetime</option>";
		}	
		?>
		</select>;
		<input type ='text' size='2' name='goals1'>
		 : 
		<input type ='text' size='2' name='goals2'>
		

<input type='hidden' name='action' value = 'set_result'>
<input id='Button' type='submit' name='submit' value = 'Ergebnis eintragen'>
</form>

</p>


<p>
<h2> Spieldaten ändern </h2>
(TODO)
</p>


<p>
<h2> Landstatus ändern </h2>


<form id='Form' action='#' method='post'>
Land:
<?php 
	GUIBuilder::buildDropdownSelect('team', 'SELECT land, id FROM laender ORDER BY land;', 0);
?> 

 Status: 
<select name='status' size='1'>
<option value='0'>noch drin </option>
 <option value='-1'>ausgeschieden </option>
<option value='1'>Sieger </option>
</select>
<input type='hidden' name='action' value = 'set_teamstatus'>
<input id='Button' type='submit' name='submit' value = 'Landstatus ändern'>

</form>

</p>
<br>

<?php include ("../layout/post_content_stuff.php"); ?>
