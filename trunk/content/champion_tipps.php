<?php 
	include_once("../classes/Session.php");
	include_once("../classes/Matches.php");
	include_once("../classes/GUIBuilder.php");
	include_once("../classes/Game.php");

	include"../layout/pre_content_stuff.php";

	$session = new Session();
	$user = $session->getCurrentUser();
	$userid = $session->getCurrentUserId();

	if ($userid == null) {
                $gb->showNoAccessPage();
		include ("../layout/post_content_stuff.php");
		exit();
						 
	}

	$m = new Matches();

	$champtip = Game::getChamptip($userid);

	echo "<h1> Meistertipps </h1>";

	if ($m->started())
	{
		if ($champtip == null)
		{
			echo "<p>";	
			echo "Du hast keinen Meistertipp abgegeben.<br>";
			echo "</p>";	
		}
		else
		{
			echo "<p>";	
			echo "Dein Meistertipp ist ", $champtip->name;
			if ($champtip->instatus == 0)
			{
				echo " und ist noch im Wettbewerb.";
			}
			else if ($champtip->instatus == -1)
			{
				echo " und ist leider schon aus dem Wettbewerb ausgeschieden.";
			}
			else if ($champtip->instatus == 1)
			{
				echo " und ist der Sieger des Wettbewerbs. Herzlichen Glückwunsch!";
			}
			echo "</p>";	
		}
		echo "<p>";	
		echo "Hier wird angezeigt, wie oft jedes Land als Sieger getippt wurde. Wenn das Land ausgeschieden ist, werden auch die jeweiligen Spielernamen aufgedeckt.";
		
		echo "</p>";	
		GUIBuilder::buildChamptipTable();
	}
	else{
		echo "<p>Hier werden später die Meistertipps aller ausgeschiedenen Mannschaften angezeigt.</p>";
		$selecttip = 0;
		if ($champtip == null)
		{
			echo "<p>Du hast noch keinen Meistertipp abgegeben. Solange das Turnier noch nicht begonnen hat kannst Du ihn unter 
					<a href='../content/profile.php'>Deine Daten</a> ändern.</p>";
		}
		else
		{
			echo "<p>Dein Meistertipp ist ", $champtip->name,". Bis zu Beginn des Turniers kannst Du diesen noch unter <a href='../content/profile.php'>Deine Daten</a> ändern.</p>";
			$selecttip = $champtip->id;
		}
	}

	include "../layout/post_content_stuff.php"; 
?>
