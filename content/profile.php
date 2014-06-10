<?php 
	include_once("../classes/Session.php");
	include_once("../classes/Matches.php");
	include_once("../classes/Exceptions.php");
	include_once("../classes/Game.php");
	include_once("../classes/GUIBuilder.php");

	include ("../layout/pre_content_stuff.php");

	$session = new Session();
	$userid = $session->getCurrentUserid();
	
	if ($userid == null)
	{
                GUIBuilder::showNoAccessPage();
		include ("../layout/post_content_stuff.php");
		exit();
	}
	
	switch($_POST['action'])
	{
		case "change_pw":
			change_pw();
			break;
		case "changed_pw":
			if (changed_pw($userid))
			{
				show_profile($userid);
			}
			else
			{
				change_pw();
			}
			break;
		case "change_email":
			change_email();
			break;
		case "changed_email":
			if (changed_email($userid))
			{
				show_profile($userid);
			}
			else
			{
				change_email();
			}
			break;
		case "change_champtip":
			change_champtip($userid);
			break;
		case "changed_champtip":
			changed_champtip($userid);
			show_profile($userid);
			break;
		default:
			show_profile($userid);
	}
	
	include("../layout/post_content_stuff.php");
	


function show_profile($userid)
{
	$session = new Session();
	$user = $session->getCurrentUser();
	$game = new Game();
	$login = $session->getCurrentUserLogin();
	
	echo "<div style='width: 370px; float:right; margin: 0 0 1.5em 0'>";
	echo "<h1> Deine Benutzerdaten </h1>";
	echo "<p>";
	echo "<table id='Highscore'>";
	echo "<tr><th>Login: </th><td style='text-align:left'>", $login, "</td></tr>";
	echo "<tr><th>Name: </th><td style='text-align:left'>", $user->name, "</td></tr>";
	echo "<tr><th>E-Mail: </th><td style='text-align:left'>", $user->email, "</td></tr>";
	echo "</table>";
	echo "</p>";
	echo "</div>";
	echo "<div style='margin: 0 380px 0 0;'>";
	echo "<h1> Deine Punkte </h1>";
	echo "<p>";

	$m = new Matches();
	$ct = $game->getChamptip($userid);
	if (!$m->started())
	{
		if ($ct == null)
		{
			echo "Du hast noch keinen Meistertipp abgegeben! Hier kannst Du Deinen Tipp wählen:<br>";
		}
		else
		{
			echo "Dein Meistertipp ist <b>". $ct->name."</b>.<br>";
			echo "Bis zum Anpfiff des Er&ouml;ffungsspieles, kannst Du den Meistertipp noch ändern.<br>";
		}
		echo "<form action='#' method='post'>";
		echo "<input type='submit' name='submit' value='Meistertipp ändern'>";
		echo "<input type = 'hidden' name = 'action' value = 'change_champtip'>";
		echo "</form>";
	}
	else
	{
		$score = $game->getScore($userid);
		$ts = $game->getTipStats($userid);
		echo "<table id='Highscore'>";
		echo "<tr>";
		echo " <th>Was?</th>";
		echo " <th>Wie oft?</th>";
		echo " <th>Punkte</th>";
		echo "</tr>";
		echo "<tr>";
		echo " <td style='text-align:left'><b>richtiges Ergebnis</b></td>";
		echo " <td style='text-align:center'>", $ts->numResultRight," </td><td style='text-align:right'>", $ts->scoreResultRight, "</td></tr>";
		echo "<tr>";
		echo " <td style='text-align:left'><b>richtige Tordifferenz</b></td>";
		echo " <td style='text-align:center'>", $ts->numDiffRight,"</td><td style='text-align:right'>", $ts->scoreDiffRight, "</td>";
		echo "</tr>";
		echo "<tr>";
		echo " <td style='text-align:left'><b>richtiger Sieger</b></td>";
		echo " <td style='text-align:center'>", $ts->numTendencyRight, "</td><td style='text-align:right'>", $ts->scoreTendencyRight, "</td>";
		echo "</tr>";
		echo "<tr>";
		echo " <td style='text-align:left'><b>Meistertipp</b></td>";
		echo " <td style='text-align:center'>", $ts->champtip, "</td><td align='right'>";
		if ($ts->statusChamptip == 0)
		    echo "(+ ", SCORE_CHAMPTIP, "?)";
		else if ($ts->statusChamptip == -1)
		    echo "0";
		else
		    echo $ts->scoreChamptip;
		echo " </td>";
		echo "</tr>";
		echo "<tr>";
		echo " <td style='text-align:left'><b>Gesamt</b></td>";
		echo " <td>&nbsp;</td><td align='right'>", $score, "</td>";
		echo "</tr>";
		echo "</table>";

		echo "<br></p>";
		echo "<p>Du stehst auf Platz <b>", $game->getHighscorePosition($userid, 0), "</b> von ", $game->getNumUsers(0), ".</p>";
		if ($user->wettbewerb == 1)
		{
			echo "<p>Beim Wettbewerb stehst du auf Platz ", $game->getHighscorePosition($userid, 1), " von ", $game->getNumUsers(1), ".</p>";
		}
		
		$numRemainingMatches = $m->getNumberOfRemainingMatches();
		echo "<p>Es stehen noch ", $numRemainingMatches , " von ", TOTAL_MATCHES, " Spielen aus, in denen du noch maximal ", 
			$numRemainingMatches * SCORE_RESULT, " Punkte holen kannst.</p>";
	}
	echo "</div>";
	echo "<p style='clear:right; margin:0'>&nbsp; </p>";
	echo "<div style='width: 370px; float:right; margin: 0 0 1.5em 0'>";
	echo "<h1>E-Mail-Adresse &auml;ndern</h1>";
	echo "<form action='#' method='post'>";
	echo "<p style='text-align:center'>";
	echo "<input id='Button' type='submit' name='submit' value='E-Mail-Adresse ändern'>";
	echo "<input type = 'hidden' name = 'action' value = 'change_email'>";
	echo "</p>";
	echo "</form>";
	echo "</div>";
	echo "<div style='margin: 0 380px 0 0;'>";
	echo "<h1>Passwort &auml;ndern </h1>";
	echo "<form  action='#' method='post'>";
	echo "<p style='text-align:center'>";
	echo "<input id='Button' type='submit' name='submit' value='Passwort ändern''>";
	echo "<input type = 'hidden' name = 'action' value = 'change_pw'>";
	echo "</p>";

echo "</form>";

echo "</div>";
}

function change_pw()
{
	echo "<h1> Passwort &auml;ndern </h1>";
	echo "<form action='#' method='post'>";
	echo " <table id='Form' style='margin-left:auto; margin-right:auto'>";
	echo " <tr><td> Altes Passwort: </td>";
	echo " <td> <input name='oldpassword' type='password' size='30' maxlength='32'></td></tr>";
	echo " <tr><td> Neues Passwort: </td>";
	echo " <td> <input name='newpassword' type='password' size='30' maxlength='32'></td></tr>";
	echo " <tr><td> Neues Passwort erneut: </td>";
	echo " <td> <input name='newpassword2' type='password' size='30' maxlength='32'></td></tr>";
	echo "</table>";
	echo "<p style='text-align:center'>";
	echo "<input type='submit' name='submit' value='Passwort ändern'>";
	echo "<input type = 'hidden' name = 'action' value = 'changed_pw'>";
	echo "</p>";
	echo "</form>";
}

function changed_pw($userid)
{
	try
	{
		Session::changeUserPassword($userid, $_POST['oldpassword'], $_POST['newpassword'], $_POST['newpassword2']);
	}
	catch(ExceptionInvalidUser $e)
	{
		echo "Fehler: ", $e->getMessage(), "<br>";
		echo "Das Passwort wurde nicht geändert!<br>";
		echo "Bitte erneut versuchen: <br><br>";
		return false;
	}
	return true;
}

function change_email()
{
	echo "<h1> E-Mail-Adresse &auml;ndern </h1>";
	echo "<form action='#' method='post'>";
	echo " <table id='Form' style='margin-left:auto; margin-right:auto'>";
	echo " <tr><td> Neue E-Mail-Adresse: </td>";
	echo " <td> <input name='newemail' type='text' size='30' maxlength='50'></td></tr>";
	echo "</table>";
	echo "<p style='text-align:center'>";
	echo "<input type='submit' name='submit' value='E-Mail-Adresse ändern''>";
	echo "<input type = 'hidden' name = 'action' value = 'changed_email'>";
	echo "</p>";
	echo "</form>";
}

function changed_email($userid)
{
	try
	{
		Session::changeUserEmail($userid, $_POST['newemail']);
	}
	catch(ExceptionInvalidUser $e)
	{
		echo "Fehler: ", $e->getMessage(), "<br>";
		echo "Die E-Mail-Adresse wurde nicht geändert!<br>";
		echo "Bitte erneut versuchen: <br><br>";
		return false;
	}
	return true;
}


function change_champtip($userid)
{
	$ct = Game::getChamptip($userid);
	echo "<form action='#' method='post'>";
	echo "<table>";
	echo "<tr><td>Meistertipp:</td><td>";
	GUIBuilder::buildDropdownSelect('champtip', 'SELECT land, id FROM laender ORDER BY land;', $ct->id - 1);
	echo "<input type='hidden' name='action' value = 'changed_champtip'>";
	echo "<tr><td><input type='submit' name='submit' value = 'Meistertipp wählen'></td><td></td>";
	echo "</table>";
	echo "</form>";
}

function changed_champtip($userid)
{
	$m = new Matches();
	if (!$m->started())
	{
		Game::setChamptip($userid, $_POST['champtip']);
	}
	return true;
}

?>
