<?php 
	include_once("../classes/Session.php");
	include_once("../classes/Matches.php");
	include_once("../classes/GUIBuilder.php");
	include_once("../classes/Game.php");

	include"../layout/pre_content_stuff.php";

	$session = new Session();
	$user = $session->getCurrentUser();
	$userid = $session->getCurrentUserId();


	$m = new Matches();
	$game = new Game();
	$gb = new GUIBuilder();


	if ($userid == null)
	{
                $gb->showNoAccessPage();
		include ("../layout/post_content_stuff.php");
		exit();
	}

	$onlywett = 0;

	if (isset($_GET['uattr1']) && $_GET['uattr1'] == 1)
	{
		$onlywett = 1;
		echo "<h1> Highscore der Wettbewerbsteilnehmer</h1>";
	}
	else
	{
		echo "<h1> Highscore </h1>";
	}

	$gb->buildHighscoreTable($userid, 1, 0, $onlywett, 1, "");

	define("HINWEIS_SPIELER_FARBE", 1);
	define("HINWEIS_SPIELER_LINK", 1);
	$gb->buildFootnotes();

	include "../layout/post_content_stuff.php"; 
?>
