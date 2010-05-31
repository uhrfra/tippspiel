<?php 
	include_once("../classes/Session.php");
	include_once("../classes/Matches.php");
	include_once("../classes/GUIBuilder.php");
	include_once("../classes/Game.php");

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
		echo "Bis zum Beginn des ersten Spiels kannst Du Deinen Meistertipp noch ändern.<br>";
	}

	echo "<br>";

?>

<div style="width:400px; float:right; margin: 0 0 1.5em 0">



<h2> Vergangene und aktuelle Spiele</h2>
<?php
$prevMatchday = $m->getPreviousMatchday();
if ($prevMatchday == null)
{
	echo "<it>(keine Spiele vorhanden)</it>";
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

<h2>Die aktuelle Top-Ten-Liste:</h2>


<?php
GUIBuilder::buildHighscoreTable($userid,0, 1, 0, 1, "")
?>

<br>


</div>


</div>


<?php 
	define("HINWEIS_SPIELER_FARBE", 1);
	define("HINWEIS_SPIELER_LINK", 1);
	define("HINWEIS_SPIEL_LINK", 1);
	GUIBuilder::buildFootnotes();

	include ("../layout/post_content_stuff.php"); 
?>
