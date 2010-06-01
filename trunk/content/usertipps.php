<?php 
	include_once("../classes/Session.php");
	include_once("../classes/GUIBuilder.php");
	include_once("../classes/Database.php");

	include ("../layout/pre_content_stuff.php");

	$session = new Session();
	$db = new Database();

	$userid = $session->getCurrentUserId();
	if ($userid == null)
	{
		GUIBuilder::showNoAccessPage();
		include ("../layout/post_content_stuff.php");
		exit();
	}
	$matchid = $_GET['spielid'];

	if (isset($_GET['ouid']))
	{
		$show_userid = $_GET['ouid'];
		$ou = $session->getUser($show_userid);
		 echo "<h1> Abgelaufene Tipps von ", $ou->name," </h1>";
		GUIBuilder::buildClosedGamesTable($show_userid, null, false);
	}
	else
	{
		$show_userid = $userid;
		 echo "<h1> Deine abgelaufenen Tipps </h1>";
		GUIBuilder::buildClosedGamesTable($show_userid, null, false);
	}

	define("HINWEIS_SPIEL_LINK", 1);
	GUIBuilder::buildFootnotes();

	include ("../layout/post_content_stuff.php");
?>
