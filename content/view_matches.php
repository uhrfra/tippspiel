<?php 
	include_once("../classes/Session.php");
	include_once("../classes/Matches.php");
	include_once("../classes/GUIBuilder.php");
	include_once("../classes/Game.php");

	include ("../layout/pre_content_stuff.php");

	$session = new Session();
	$matches = new Matches();
	$gb = new GUIBuilder();
	$game = new Game();
	$user = $session->getCurrentUser();
	$userid = $session->getCurrentUserId();

	if ($userid == null)
	{
                $gb->showNoAccessPage();
		include ("../layout/post_content_stuff.php");
		exit();
	}

	$show = -1;
	$md = null;

	if (isset($_GET['show'])) {$show = $_GET['show'];}
	if (isset($_POST['show'])) {$show = $_POST['show'];}
	if (isset($_GET['md'])) {$md = $_GET['md'];}
	if (isset($_POST['md'])) {$md = $_POST['md'];}

	if ($md == null)
	{
		$md = $matches->getCurrentMatchday();
	}
	
	if ($show == MATCHSTATUS_OPEN)
	{
		echo "<h1>Ausstehende Tipps</h1>";
		echo "<p>Hier werden alle Spiele angezeigt, auf die Du tippen kannst.<br></p>";
		// Insert tips into db
		$game->insertPostedTipps($userid);
	
	
		$gb->buildTippForm($userid, null, "#");
	
	}
	else if ($show == MATCHSTATUS_CLOSED)
	{
		echo "<h1>Abgelaufene Tipps</h1>";
		echo "<p>Hier werden alle Spiele angezeigt, die bereits beendet sind.<br></p>";
		$gb->buildClosedGamesTable($userid, null, false);
		define("HINWEIS_SPIEL_LINK", 1);
		define("HINWEIS_ETSF_PROZENT", 1);
		$gb->buildFootnotes();
	}
	else
	{
		echo "<h1>Spieltage</h1>";
		echo "Bitte Spieltag auswählen: <form action='view_matches.php' method='post'>";
		$gb->buildDropdownSelect('md', 'SELECT name, id FROM matchdays ORDER BY id;', $md-1);
		if ($show != null)
		{
			echo "<input type='hidden' name='show' value = '$show'>";
		}
		echo "<input type='submit' name='submit' value = ' auswählen'>";
		echo "</form><br>";
		echo "<h3> Ausstehende Tipps an diesem Spieltag</h3><br>";
		$gb->buildTippForm($userid, $md);
		echo "<h3> Abgelaufene Tipps an diesem Spieltag:</h3>";
		$gb->buildClosedGamesTable($userid, $md, false);
		
	}

	include ("../layout/post_content_stuff.php"); 
?>
