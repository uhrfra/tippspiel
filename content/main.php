<?php 
	include_once("../classes/Session.php");
	include_once("../classes/Matches.php");
	include_once("../classes/GUIBuilder.php");
	include_once("../classes/Game.php");
	include_once("../classes/Newsboard.php");

	include ("../layout/pre_content_stuff.php");

	GUIBuilder::buildBackgroundChangeScript();
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

    <section id="main-slider" class="no-margin">
        <div class="carousel slide wet-asphalt">
            <div class="carousel-inner">
                <div class="item active" style="background-image: url(../layout/images/bg_slider.jpg)">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="carousel-content" style="padding: 65px 0px 50px 35px;">
                                    <h2 class="animation animated-item-1"><?php echo TITLE;?></h2>
                                    <p class="animation animated-item-2"><?php echo TITLE_SECOND ?></p>
                            	</div>
                                <div class="carousel-content">
                                <div style="margin-bottom:6px">Hallo <?php echo $user->name ?>, willkommen beim Tippspiel.</div>
                                <div style="margin-bottom:6px">Dein derzeitiger Punktestand ist <?php $score = Game::getScore($userid, 0); echo $score; ?> Punkt<?php if( $score!=1)echo "e"?> und Du stehst auf Platz <?php echo Game::getHighscorePosition($userid, 0); ?> von <?php echo Game::getNumUsers(0); ?>.</div>
<?php
	$m = new Matches();
	if (!$m->started())
	{
		echo "<p>Bis zum Beginn des ersten Spiels kannst Du Deinen Meistertipp noch <a href='profile.php'>hier</a> &auml;ndern.</p>";
	}
?>



                        </div><!--/.col-->
                    </div><!--/.row-->
                </div><!--/.item-->
            </div><!--/.carousel-inner-->
        </div><!--/.carousel-->
    </section><!--/#main-slider-->

 

<div class="row">

<div class="col-md-6 col-sm-12">
    <h2>Die Top Ten <small><a href="highscore.php">(Tabelle anzeigen)</a></small></h2>
<?php
    GUIBuilder::buildHighscoreTable($userid,0, 1, 0, 1, "")
?>
</div>


<div class="col-md-6 col-sm-12">
  <h2> Vergangene und aktuelle Spiele <small><a href='view_matches.php?show=2'>(alle anzeigen)</a></small></h2>
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

    echo "<h2>Kommende Spiele <small><a href='view_matches.php?show=0'>(alle anzeigen)</a></small></h2>";
    GUIBuilder::buildTippForm($userid, null, "../content/main.php");
?>
</div>
</div>

<div class="row">
<div class="col-sm-12">
<?php
if (Newsboard::getNumEntriesSince($user->prevlogintime) > 0)
{
	echo "<h2>Neue Eintr&aumlge im Newsboard</h2>";
	GUIBuilder::buildNewsboardTableSince($user->prevlogintime);
}
?>
</div>
</div>

<?php 
	define("HINWEIS_SPIELER_FARBE", 1);
	define("HINWEIS_SPIELER_LINK", 1);
	define("HINWEIS_SPIEL_LINK", 1);
	GUIBuilder::buildFootnotes();

	include ("../layout/post_content_stuff.php"); 
?>
