<?php 

	echo "<div id='Navigation'>";
	
	if ($user == null)
	{
		// Navigation, if not logged in
		echo "<ul id='Navigation1'>";
		echo "  <li><a href='index.php'>Login</a></li>";
		echo "  <li><a href='rules.php'>Spielregeln</a></li>";
		echo "  <li><a href='sign_up.php'>Neu anmelden</a></li>";
		echo "</ul>";
	}
	else
	{
		// Main site, profile and matches
		echo "<ul id='Navigation1'>";
		echo "  <li><a href='main.php'>&Uuml;bersicht</a></li>";
		echo "  <li><a href='profile.php'>Deine Daten</a></li>";
		echo "</ul>";
		echo "<ul id='Navigation2'>";
		echo "  <li><a href='view_matches.php?show=0'>Ausstehende Tipps</a></li>";
		echo "  <li><a href='view_matches.php?show=2'>Abgelaufene Tipps</a></li>";
		echo "</ul>";
		
		// Highscore
		echo "<ul id='Navigation1'>";
		echo "  <li><a href='highscore.php'>Highscore</a></li>";
		echo "</ul>";
		
		// Additional link for contest participants
		if ( $user->wettbewerb > 0)
		{
			echo "<ul id='Navigation2'>";
			echo "<li><a href='highscore.php?uattr1=1'>Wettbewerb</a></li>";
			echo "</ul>";
		}
		
		// Statistics
		echo "<ul id='Navigation1'>";
		echo "  <li>Statistiken</li>";
		echo "</ul>";
		echo "<ul id='Navigation2'>";
		echo "  <li><a href='champion_tipps.php'>Meistertipps</a></li>";
		echo "  <li><a href='history.php'>Historie</a></li>";
		echo "<li><a href='alltime_highscore.php'>Ewige Tabelle</a></li>";
		echo "</ul>";
		
		// additional navigation for admins
		if ( $user->adminlevel > 0 ) {
			echo "<ul id='Navigation1'>";
			echo "  <li>Administration</li>";
			echo "</ul>";
			echo "<ul id='Navigation2'>";
			echo "<li><a href='admin_matches.php'>Spiele</a></li>";
			
			// additional link for level 2 admins
			if ( $user->adminlevel == 2 ) {
				echo "<li><a href='admin_users.php'>Benutzer</a></li>";
			}
			echo "</ul>";
		}
		
		echo "<ul id='Navigation1'>";
		echo "  <li><a href='newsboard.php'>Newsboard</a></li>";
		echo "  <li><a href='rules.php'>Spielregeln</a></li>";
		echo "  <li><a href='logout.php' style='color:yellow;'>Logout</a></li>";
		echo "</ul>";
	}
		
	echo "</div>";
?>
