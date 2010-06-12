<?php 
	include_once("../classes/Session.php");
	include_once("../classes/GUIBuilder.php");
	include_once("../classes/Database.php");

	include ("../layout/pre_content_stuff.php");

	$session = new Session();
	$db = new Database();

	$userid = $session->getCurrentUserId();
	$user = $session->getCurrentUser();
	$matchid = $_GET['spielid'];

	if ($userid == null)
	{
		GUIBuilder::showNoAccessPage();
		include ("../layout/post_content_stuff.php");
		exit();
	}

	$sqlS10 = "SELECT CONCAT(l1.land, ' - ', l2.land, ' (',
             IF(spiele.status < 2, '-:-', CONCAT(spiele.tore1, ':', spiele.tore2)),')')
		FROM (spiele LEFT JOIN `laender` AS l1 ON spiele.ms1 = l1.id) 
		 LEFT JOIN `laender` AS l2 ON spiele.ms2 = l2.id
		WHERE spiele.id = '$matchid';";

	echo "<h1> Tipps zum Spiel ", $db->queryResult($sqlS10), "</h1>";    

	GUIBuilder::buildMatchtipps($matchid, $userid);

	define("HINWEIS_SPIELER_LINK",1);
	GUIBuilder::buildFootnotes();

	include ("../layout/post_content_stuff.php"); 
?>
