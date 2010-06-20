<?php 
	include_once("../classes/Session.php");
	include_once("../classes/Matches.php");
	include_once("../classes/GUIBuilder.php");
	include_once("../classes/Game.php");
	include_once("../classes/Newsboard.php");

	include ("../layout/pre_content_stuff.php");

	$session = new Session();

	$user = $session->getCurrentUser();
	$userid = $session->getCurrentUserId();

	if ($userid == null) {
	        GUIBuilder::showNoAccessPage();
		include ("../layout/post_content_stuff.php");
		exit();
	}
	
	// Commit tips into the database if the user has entered them directly at the main page:
	Game::insertPostedTipps($userid);


?>

 
<h1> &Uuml;bersicht</h1>

<div>

<p>
Hallo <?php echo $user->name ?>, willkommen beim Tippspiel.
</p>
<p style="margin:0em">
Dein derzeitiger Punktestand ist
<?php $score = Game::getScore($userid, 0); echo $score; ?> 
Punkt<?php if( $score!=1)echo "e"?> 
und Du stehst auf Platz <?php echo Game::getHighscorePosition($userid, 0); ?> 
von <?php echo Game::getNumUsers(0); ?>.<br>
</p>

<?php
	$m = new Matches();
	if (!$m->started())
	{
		echo "<p>Bis zum Beginn des ersten Spiels kannst Du Deinen Meistertipp noch �ndern.</p>";
	}


?>

<div style="margin-top: 1.5em; overflow:auto;";>

<div style="width:400px; float:right; margin: 0;">
<h2 style="margin-top:0"> Vergangene und aktuelle Spiele</h2>
<?php
$prevMatchday = $m->getPreviousMatchday();
if ($prevMatchday == null)
{
	echo "<p style='text-align:center'><it>(keine Spiele vorhanden)</it></p>";
}
else
{
	GUIBuilder::buildClosedGamesTable($userid, null, true);
}
?>


<?php
echo "<h2>Kommende Spiele</h2>";
GUIBuilder::buildTippForm($userid, null, "../content/main.php");


?>

</div>

<div style="margin: 0em 410px 0 0;">

<h2 style="margin-top:0">Die aktuelle Top-Ten-Liste:</h2>


<?php
GUIBuilder::buildHighscoreTable($userid,0, 1, 0, 1, "")
?>

<br>


</div>

<div style="clear:left; height:0"></div>

</div>

</div>

<p>
<?php
if (Newsboard::getNumEntriesSince($user->prevlogintime) > 0)
{
	echo "<h2>Neue Eintr�ge im Newsboard</h2>";
	GUIBuilder::buildNewsboardTableSince($user->prevlogintime);
}
?>
</p>

<?php 
	define("HINWEIS_SPIELER_FARBE", 1);
	define("HINWEIS_SPIELER_LINK", 1);
	define("HINWEIS_SPIEL_LINK", 1);
	GUIBuilder::buildFootnotes();

	include ("../layout/post_content_stuff.php"); 
?>
