<?php
	include ("../layout/pre_content_stuff.php"); 
	include_once ("../classes/Newsboard.php");
	include_once ("../classes/GUIBuilder.php");
	include_once ("../classes/Session.php");

	$session = new Session();
	$userid = $session->getCurrentUserId();

	if ($userid == null)
	{
		GUIBuilder::showNoAccessPage();
		include ("../layout/post_content_stuff.php");
		exit();
	}

	if (isset($_POST['action']) && $_POST['action'] == "post_message")
	{
		$nb = new Newsboard();
		$nb->postMessage($userid, $_POST['message']);

	}

	if (isset($_POST['action']) && $_POST['action'] == "write_message")
	{			
		echo "<h1>Newsboard-Beitrag verfassen</h1>";
		echo "<form action='#' method='post'>";
		echo "<p style='text-align:center'>";
		echo "<textarea name='message' cols='70' rows='15' value = '$pretext'></textarea><br>";
		echo "<br>";
		echo "<input id='Button' type='submit' name='submit' value='Nachricht absenden'>";
		echo "<input type='hidden' name='action' value='post_message'>";
		echo "</p>";
		echo "</form>";
	}
	else
	{
		echo "<h1> Newsboard </h1>";
		echo "<p>Hier kann jeder seine Meinung oder wichtige Neuigkeiten loswerden:</p>";
		echo "<form action='#' method='post'>";
		echo "<input type='hidden' name='action' value = 'write_message'>";
		echo "<input id='Button' type='submit' name='submit' value = 'Nachricht verfassen'>";
		echo "</form>";

		GUIBuilder::buildNewsboardTable();
	}

	include ("../layout/post_content_stuff.php"); 
?>
