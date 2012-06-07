<?php 
	include_once("../classes/Session.php");
	include_once("../classes/GUIBuilder.php");

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

	$gb = new GUIBuilder();

	
	if (isset($_POST['action']))
{
		$db = new Database();
		if ($_POST['action'] == 'change_wettstate')
		{
			$state = $_POST['state'];
			$id = $_POST['id'];
			 $db->query("UPDATE `user` SET wettbewerb = '$state' WHERE id = '$id';");
				
		}
		else if ($_POST['action'] == 'attr1')
		{
			$state = $_POST['state'];
			$id = $_POST['id'];
			 $db->query("UPDATE `user` SET attr1 = '$state' WHERE id = '$id';");
				
		}
		else if ($_POST['action'] == 'attr2')
		{
			$state = $_POST['state'];
			$id = $_POST['id'];
			$db->query("UPDATE `user` SET attr2 = '$state' WHERE id = '$id';");
				
		}
		else if ($_POST['action'] == 'change_adminstate')
		{
			$state = $_POST['state'];
			$id = $_POST['id'];
			$db->query("UPDATE `user` SET adminlevel = '$state' WHERE id = '$id';");
		}
		else if ($_POST['action'] == 'set_stars')
		{
			$id = $_POST['id'];
			$starcount = $_POST['starcount'];
			$starmessage = $_POST['starmessage'];
			$db->query("UPDATE `user` SET starcount = '$starcount', starmessage = '$starmessage' WHERE id = '$id';");
		}
	
}
	?>


<h1> Wettbewerbsstatus setzen </h1>
<p>
<form id='Form' action='#' method='post'>
<?php
	$db = new Database();
	$sql = "SELECT id, name, wettbewerb, attr2, attr1 FROM user";
	$la = $db->query($sql);
	
	echo "<select name='id' size='1'>";
	while($row = mysql_fetch_row($la))
    {
	
	$ws = "";
	if ($row[2] == 1)
	{
		$ws = "(WETTBEWERB)";
	}
      echo "<option value='",$row[0], "'>",  $row[1], ": ", $ws, " </option>'";
	}
 
 echo "</select>";
?>
<select name='state' size='1'>
<option value='0'>kein Teilnehmer. </option>
<option value='1'>Teilnehmer am Wettbewerb </option>
</select>
<input type='hidden' name='action' value = 'change_wettstate'>
<input id='Button' type='submit' name='submit' value = 'Wettbewerbsstatus setzen'>
</form>
</p>

<h1> Meistersterne setzen </h1>
<p>
<?php
	$db = new Database();
	$sql = "SELECT id, name, starcount, starmessage FROM user";
	$la = $db->query($sql);
	
	echo "Folgende Meistersterne sind gesetzt:<br>";
	while($row = mysql_fetch_row($la))
    {
    	if ($row[2] > 0)
    	{
    		echo $row[1], "(", $row[2], ") : ", $row[3], "<br>";
    	}
    }
    
    echo "<form id='Form' action='#' method='post'>";
    echo "<select name='id' size='1'>";
    $la = $db->query($sql);
    while($row = mysql_fetch_row($la))
	{
		echo "<option value='",$row[0], "'>",  $row[1], " (", $row[2], ")</option>'";
	}
	echo "</select>";
	echo " Anzahl: ";
	echo "<input type ='text' size='2' name='starcount'>";
	echo " Text: ";
	echo "<input type ='text' size='25' name='starmessage'>";
	echo "<input type='hidden' name='action' value = 'set_stars'>";
	echo "<input id='Button' type='submit' name='submit' value = 'Meistersterne setzen'>";
	echo "</form>";
?>
	
</p>

<p>
<h1> Attribut 1 (bezahlt) setzen </h1>
<form id='Form' action='#' method='post'>
<?php
	$db = new Database();
	$sql = "SELECT id, name, wettbewerb, attr2, attr1 FROM user";
	$la = $db->query($sql);
	
	echo "<select name='id' size='1'>";
	while($row = mysql_fetch_row($la))
		{
		
		$payed = 0;

		if ($row[4] == 1)
		{
			$attr1 = " (1)";
		}
		else
		{
			$attr1 = " (0)";
		}
		  echo "<option value='",$row[0], "'>",  $row[1], $attr1, "</option>'";
	 }
 echo "</select>";
?>

<select name='state' size='1'>
<option value='0'>0 </option>
<option value='1'>1 </option>
</select>
<input type='hidden' name='action' value = 'change_attr1'>
<input id='Button' type='submit' name='submit' value = 'Attribut 1 setzen'>
</form>
</p>

<p>
<h1> Attribut 2 setzen </h1>
<form id='Form' action='#' method='post'>
<?php
	$db = new Database();
	$sql = "SELECT id, name, wettbewerb, attr2, attr1 FROM user";
	$la = $db->query($sql);
	
	echo "<select name='id' size='1'>";
	while($row = mysql_fetch_row($la))
		{
		
		$payed = 0;

		if ($row[3] == 1)
		{
			$attr2 = " (1)";
		}
		else
		{
			$attr2 = " (0)";
		}
		  echo "<option value='",$row[0], "'>",  $row[1], $attr2, "</option>'";
	 }
 echo "</select>";
?>

<select name='state' size='1'>
<option value='0'>0 </option>
<option value='1'>1 </option>
</select>
<input type='hidden' name='action' value = 'change_attr2'>
<input id='Button' type='submit' name='submit' value = 'Attribut 2 setzen'>
</form>
</p>

<?php
if ($user->adminlevel == 2)
{

echo "<h2> Adminstatus ändern </h2>";
echo "<p>";
echo "<form id='Form' action='#' method='post'>";

$db = new Database();
$sql = 'SELECT id, name, adminlevel FROM user';
$la = $db->query($sql);
echo "	<select name='id' size='1'>";
while($row = mysql_fetch_row($la))
 {
  echo " <option value='",$row[0], "'>",  $row[0], ": ", $row[1], " (", $row[2], ") </option>'";
}
	echo "</select>";
 

echo "<select name='state' size='1'>";
echo "<option value='1'>0 - kein Admin. </option>";
echo "<option value='1'>1 - Admin </option>";
echo "<option value='2'>2 - Admin </option>";
echo "</select>";
echo "<input type='hidden' name='action' value = 'change_adminstate'>";
echo "<input id='Button' type='submit' name='submit' value = 'Adminstatus setzen'>" ;
echo "</form>";

echo "</p>";

echo "<h2> Rundmail verfassen </h2>";
echo "<p>";
echo "(Test this!!!)";
GUIBuilder::buildMailToAllUsersLink();
echo "</p>";


echo "<h2> Benutzerpasswort ändern </h2>";
echo "<p>";
echo "(TODO)";
echo "</p>";


echo "<h2> Benutzer löschen </h2>";
echo "<p>";
echo "(TODO)";
echo "</p>";
}

include ("../layout/post_content_stuff.php");
?>
