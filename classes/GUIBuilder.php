<?php

include_once("../config/config.php");
include_once("Database.php");
include_once("Exceptions.php");
include_once("Game.php");
include_once("Session.php");



class GUIBuilder
{	
	// Builds a html form select of size 1 using the defined query.
	// The first item of the result is displayed, the second is used as the select value.
	public static function buildDropdownSelect($name, $query, $selectitem)
	// No exception is thrown. On a database exception an empty select is created.
	{
	
		echo "<select name='$name' size='1'>";
		try
		{
			$db = new Database();
			$result = $db->query($query);
			$coUnter = 0;
			while ($result != null && $row = mysql_fetch_row($result))
			{
				if (count($row) > 1)
				{
					if ($counter == $selectitem)
					{
						echo "<option selected value='$row[1]'>$row[0]</option>";
					}
					else
					{
						echo "<option value='$row[1]'>$row[0]</option>";
					}
				}
				else if (count($row) == 1)
				{
					if ($counter == $selectitem)
					{
						echo "<option selected>$row[0]</option>";
					}
					else
					{
						echo "<option>$row[0]</option>";
					}
				}
				$counter++;
			}
		}
		catch (Exception $e)
		{
			// Do not handle the exception.
		}
		echo "</select>";
	}
	
	public static function buildTippForm($userid, $matchday, $sitelink)
	{
		$m = new Matches();
		if ($matchday == null)
		{
			$openmatches = $m->getAllOpenMatches($userid);
		}
		else
		{
			$openmatches = $m->getOpenMatchesOfMatchday($userid, $matchday);
		}
		if ($openmatches == null)
		{
			echo "<p style='text-align:center'><it>(keine offenen Spiele)</it></p>";
			return;
		}
		echo "<form action='$sitelink' method='post'>";
		echo "<div style='text-align:center'>"; // table centering for IEs
		echo "<table id=\"Highscore\">";

		if ($sitelink == "../content/main.php")
			echo "<tr><th>Datum</th><th>Spiel</th><th>Dein Tipp</th><tr>";
		else
			echo "<tr><th>Datum</th><th>Info</th><th>Spiel</th><th>Dein Tipp</th><tr>";
		$count = 0;	
		foreach ($openmatches as $match)
		{
			echo "<tr><td>", $match->datetime,"</td>";
			if ($sitelink != "../content/main.php")
				echo "<td style='text-align:center'>", $match->matchdayname, "</td>";
			echo "<td style='text-align:center'>", $match->teamname1, " - ", $match->teamname2, "</td>";
			echo "<td style='text-align:center'>";
			echo "<input type ='text' size='2' name='tip1[]' value ='$match->tippgoals1' style='text-align:center;font-size:0.8em' onkeypress='setColor(this);'>";
			echo " : ";
			echo "<input type ='text' size='2' name='tip2[]' value ='$match->tippgoals2' style='text-align:center;font-size:0.8em' onkeypress='setColor(this);'>";
			echo "<input type ='hidden' name='tipmatchid[]' value='$match->id'>";
			echo "</td>";
			echo "</tr>";
			$count++;
			if ($sitelink == '../content/main.php' && $count >= 4)
			  break;
		}
		echo "<tr><td style='background-color:transparent'></td><td style='background-color:transparent'></td>";
		if ($sitelink != "../content/main.php")
			echo "<td style='background-color:transparent'></td>";
		echo "<td style='background-color:transparent; text-align:center'>";
		echo "<input type ='hidden' name='md' value='$matchday'>";
		echo "<input type='submit' name='submit' value = 'TIPPEN'> ";
		echo "</td>";
		echo "</table>";
		echo "</div>";
		echo "</form>";
	}
	
	public static function buildOpenGamesTable($matchday, $sitelink)
	{
		$m = new Matches();
		if ($matchday == null)
		{
			$openmatches = $m->getAllOpenMatches($userid);
		}
		else
		{
			$openmatches = $m->getOpenMatchesOfMatchday($userid, $matchday);
		}
		if ($openmatches == null)
		{
			echo "<p style='text-align:center'><it>(keine offenen Spiele)</it></p>";
			return;
		}

		echo "<tr><th>Datum</th><th>Info</th><th>Spiel</th><tr>";
		
		foreach ($openmatches as $match)
		{
			echo "<tr><td>", $match->datetime,"</td>";
			echo "<td>", $match->matchdayname, "</td>";
			echo "<td>", $match->teamname1, " - ", $match->teamname2, "</td>";
			echo "</tr>";
		}
		echo "<tr><td></td><td></td><td></td><td>";
		echo "<input type ='hidden' name='md' value='$matchday'>";
		echo "</table>";
	}
	
	// \param userid Current userid.
	// \param show_fav Show favtipp or "(noch drin)"
	// \param topten Show only top ten.
	public static function buildHighscoreTable($userid, $show_fav, $topten, $onlywett, $user_link, $link)
	{
		$db = new Database();
		
		if ($topten == 0)
		{
			$maxplace = $db->queryResult("SELECT COUNT(*) FROM `user`;");
		}
		else
		{
			$maxplace = 10;
		}
		
		$score_champtip = SCORE_CHAMPTIP; // Champion tip is right.
		$score_result = SCORE_RESULT; // Result is tipped right.
		$score_diff = SCORE_DIFF; // Difference is tipped right.
		$score_tendency = SCORE_TENDENCY; // Tendency is tipped right.
		$score_draw_result = SCORE_DRAW_RESULT; // Game ended draw and result is tipped right.
		$score_draw_tendency = SCORE_DRAW_TENDENCY; // Game ended draw and tendency is right.
		
		$cur_uid = Session::getCurrentUserId();
			
		// H1) Top-Ten
//     | Platz | Name | Punkte | E | T | S | F | (Meistertip bzw. "noch drin" |
//       Spieler macht beim Wettbewerb mit (=1) oder nicht (=0) | Userid |
$sqlH1 = "SELECT COUNT(*) as platz,
	   u1.name,
	   (IF(laender.meisterstatus = 1, $score_champtip, 0) + u1.anz_er * $score_result + u1.anz_tr * $score_diff + u1.anz_sr * $score_tendency) AS punkte,
	   u1.anz_er, u1.anz_tr, u1.anz_sr, u1.anz_f, 
	   if(laender.meisterstatus = 0, '<i>noch drin</i>', laender.land) AS meistertipp,
	   u1.wettbewerb,
	   u1.id,
	   (u1.anz_er + u1.anz_tr + u1.anz_sr + u1.anz_f) AS anz_tipps,
	   laender.meisterstatus,
	   (u1.anz_er * $score_result + u1.anz_tr * $score_diff + u1.anz_sr * $score_tendency) AS pkt_o_mt 
FROM ((`user` AS u1 LEFT JOIN `laender` ON u1.meistertip = laender.id) JOIN `user` as u2)
	     LEFT JOIN `laender` AS `l2` ON u2.meistertip = l2.id
WHERE ";

if ($onlywett == 1)
{
$sqlH1 = $sqlH1 . "u1.wettbewerb > 0 AND u2.wettbewerb > 0 AND ";
}
$sqlH1 = $sqlH1 . "(((IF(laender.meisterstatus = 1, $score_champtip, 0) + u1.anz_er * $score_result + u1.anz_tr * $score_diff + u1.anz_sr * $score_tendency)
	       < (IF(l2.meisterstatus = 1, $score_champtip, 0) + u2.anz_er * $score_result + u2.anz_tr * $score_diff + u2.anz_sr * $score_tendency))
	  OR (u1.id = u2.id))
GROUP BY u1.name, u1.anz_er, u1.anz_tr, u1.anz_sr, u1.anz_f, u1.wettbewerb, punkte, anz_tipps
HAVING platz <= $maxplace OR u1.id = $cur_uid
ORDER BY punkte DESC, pkt_o_mt DESC, u1.anz_er DESC, u1.anz_tr DESC, u1.anz_sr DESC;";

 $query_result = $db->query($sqlH1);
  echo "<div style='text-align:center'>"; // table centering for IEs
  echo"<table id=\"Highscore\">
<tr>
<th>Pl.</th>
<th align='left'>Name</th>";
if ($show_fav == 1)
	echo "<th>Punkte</th>";
else
	echo "<th>Pkt.</th>";
echo "
<th>E</th>
<th>T</th>
<th>S</th>
<th>F</th>";
  if ($show_fav == 1){
    echo "<th>Meistertipp</th>";
  }
  echo "</tr>";
 
  while ($row = mysql_fetch_row($query_result)){
    echo "<tr>";
    // insert "..." before user entry
    if ($maxplace < $row[0]) {
      echo "<td></td><td style='text-align:center; font-size:1.2em'>...</td><td></td><td></td><td></td><td></td><td></td>";
      if ($show_fav == 1)
        echo "<td></td>";
      echo "</tr><tr>";
    }

    for ($i = 0; $i < 7; $i++){
      if ($i == 0 || $i ==2){
	echo "<td align = 'right'><b>", $row[$i], "</b></td>";
	
      }
      else{
	if ($i == 1){
	  if ($user_link == 1){
	    echo "<td style='text-align:left'><b>";
	    if ($row[9] == $cur_uid) {
	      echo "<a id='User' ";
	    } else if ($row[8] > 0) {
	      echo "<a id='Party' ";
	    } else {
	      echo "<a id='Gast' ";
	    }
	    echo "href='usertipps.php?ouid=", $row[9], "&retlink=",$link,"'>", $row[1], "</a></b></td>";
	  }
	  else{
	    echo "<td style='text-align:left'><b>", $row[1], "</b></td>";
	  }
	}
	else{
	  echo "<td>", $row[$i], "</td>";
	}
      }
    }
    if ($show_fav == 1){
		$champbonus = SCORE_CHAMPTIP;
      if ($row[7] == ""){
	echo "<td style='text-align:center'><i>(nicht getippt)</i>  </td>";
      }
      else if ($champbonus > 0){
	echo "<td style='text-align:center'>";
	if ($row[11] == 0)
	  echo "noch drin (+ ", $champbonus, ")";
	else if ($row[11] == -1)
	  echo "<i>(", $row[7], ")</i>";
	else if ($row[11] == 1)
	  echo "<b>", $row[7], " (+", $champbonus, "!)</b>";
	echo "</td>";
      } else {
	echo "<td style='text-align:center'>", $row[7], "</td>";
      }
    }
    echo "</tr>";
  }
  echo" </table><br>";
  echo "<table id='Legende'>";
  echo "<tr><td style='text-align:right'>E = Ergebnis richtig</td>";
  echo "    <td style='width:5px'></td>"; 
  echo "    <td style='text-align:left'>  T = Tordifferenz richtig</td></tr>";
  echo "<tr><td style='text-align:right'>S = Sieger richtig</td>";
  echo "    <td style='width:5px'></td>"; 
  echo "    <td style='text-align:left'>F = falsch getippt</td></tr>";
  echo "</table>";
  echo "</div>";

 }
 
 function buildClosedGamesTable($userid, $matchday, $small){ 
 
 
// S2) Alle vergangenen Spiele.$userid ist der aktuelle Benutzer.
//     | Datum | Info | Spiel | Ergebnis | Tipp | %E | %T | %S | %F | Tore1 | Tore2 | TipTore1 |
//     | TipTore2 | Spielid
$sqlS2 ="SELECT DATE_FORMAT(spiele.datum, '%d.%m. - %H:%i') AS datum,
  md.name,
  CONCAT(l1.land, ' - ', l2.land) AS begegnung,
  IF(spiele.status = 0, '-:-',
    CONCAT(spiele.tore1, ':', spiele.tore2)) AS ergebnis,
  IF(tipps.id IS NULL, '-:-',
    CONCAT(tipps.tore1, ':', tipps.tore2)) AS tipp,
  IF (spiele.anz_er + spiele.anz_tr + spiele.anz_sr + spiele.anz_f = 0, '-',
       CONCAT(ROUND(spiele.anz_er * 100 /
        (spiele.anz_er + spiele.anz_tr + spiele.anz_sr + spiele.anz_f)), ' %')) AS proz_er,
  IF (spiele.anz_er + spiele.anz_tr + spiele.anz_sr + spiele.anz_f = 0, '-',
       CONCAT(ROUND(spiele.anz_tr * 100 /
        (spiele.anz_er + spiele.anz_tr + spiele.anz_sr + spiele.anz_f)), ' %')) AS proz_tr,
  IF (spiele.anz_er + spiele.anz_tr + spiele.anz_sr + spiele.anz_f = 0, '-',
       CONCAT(ROUND(spiele.anz_sr * 100 /
        (spiele.anz_er + spiele.anz_tr + spiele.anz_sr + spiele.anz_f)), ' %')) AS proz_sr,
  IF (spiele.anz_er + spiele.anz_tr + spiele.anz_sr + spiele.anz_f = 0, '-',
       CONCAT(ROUND(spiele.anz_f * 100 /
        (spiele.anz_er + spiele.anz_tr + spiele.anz_sr + spiele.anz_f)), ' %')) AS proz_f,
  spiele.tore1, spiele.tore2, tipps.tore1, tipps.tore2, spiele.id
FROM (((spiele LEFT JOIN tipps ON spiele.id = tipps.spielid AND tipps.userid = $userid)
  LEFT JOIN `laender` AS l1 ON spiele.ms1 = l1.id) LEFT JOIN `laender` AS l2 ON spiele.ms2 = l2.id)
  LEFT JOIN `matchdays` AS md ON spiele.matchday = md.id
  WHERE (spiele.datum < addtime(NOW(), SEC_TO_TIME(". TIMESHIFT ."))";
  if ($matchday != null)
  {
	$sqlS2 = $sqlS2 . "AND md.id = $matchday ";
  }
  $sqlS2 = $sqlS2 . ") ORDER BY spiele.datum;";

$db = new Database();
 $query_result = $db->query($sqlS2);
  echo "  <div style='text-align:center'>"; // table centering for IEs
  echo "  <table id=\"Highscore\">
<tr>
<th>Datum</th>";
  if ($small == false)
	echo "<th>Info</th>";
  echo "<th>Spiel</th>
<th>Erg.</th>
<th>Tipp</th>";
  if ($small == false)
	echo "<th>E</th>
<th>T</th>
<th>S</th>
<th>F</th>";
echo "</tr>";
 
  $numRows = mysql_num_rows($query_result);

  if ($numRows == 0){
    echo "<tr> <td colspan = 9><i> (keine) </i></td> <tr>";
  }

  if ($small == true)
    for($i = 0; $i < $numRows - 4; $i++) {
      $row = mysql_fetch_row($query_result); 
    }

  while ($row = mysql_fetch_row($query_result)){
    echo "<tr>";


    // Berechnung der Farbe f�r den Tipp. ---- 
    if ($row[4] == '-:-'){
      $ec = GUIBuilder::$col_notipp;
    }
    else if ($row[3] == '-:-'){
      $ec = GUIBuilder::$col_notset;
    }
    else if ($row[9] == $row[11] && $row[10] == $row[12]){
      $ec = GUIBuilder::$col_er;
    }
    else{
      $ergebnisdiff = $row[9] - $row[10];
      $tippdiff = $row[11] - $row[12];
      if ($ergebnisdiff == $tippdiff && $tippdiff) {
      	$ec = GUIBuilder::$col_tr;
      }
      else if (($ergebnisdiff > 0 && $tippdiff > 0)||
	  ($ergebnisdiff == 0 && $tippdiff == 0)||
	  ($ergebnisdiff < 0 && $tippdiff < 0)){
	$ec = GUIBuilder::$col_sr;
      }
      else{
	$ec = GUIBuilder::$col_f;
      }
    }

    for ($i = 0; $i < 9; $i++){
      if ($small == true && ($i==1 || ($i>=5 && $i<=8)))
      	continue;
      else if ($i == 1 && $small == false)
      	echo "<td style='text-align:center'>", $row[$i], "</td>";
      else if ($i == 2){
        echo "<td style='text-align:center; font-size:0.77em'><b><a style='color:#000066' href = 'matchtipps.php?spielid=", $row[13],"'> ", $row[$i], "</a></b></td>";
      }
      else if ($i ==3){
	echo "<td style='text-align:center'><b>", $row[$i], "</b></td>";
      }
      else if ($i == 4){
	echo "<td style='text-align:center; background-color:", $ec, "'><b>", $row[$i], "</b></td>";
      }
      else{
	echo "<td>", $row[$i], "</td>";
      }
    }
    echo "</tr>";
  }
  echo" </table><br>";
  echo" </div>";
}

function buildChamptipTable(){
  $sqlH4 = "SELECT `land`,
  COUNT(user.id) AS tipps,
  laender.meisterstatus as status, laender.id
FROM laender LEFT JOIN user ON laender.id = user.meistertip
GROUP BY `land`, status;";
  echo "<div style='text-align:center'>"; // table centering for IEs
  echo"  <table id=\"Highscore\">
<tr>
<th>Land</th>
<th>Tipps</th>
<th>getippt von</th></tr>";
	
	$db = new Database();
	$query_result = $db->query($sqlH4);

	while ($row = mysql_fetch_row($query_result)){
    if ($row[2] == 0){
      echo "<tr>";
      echo "<td style='text-align:left'>", $row[0], "</td>";
      echo "<td style='text-align:center'> ? </td>";
      echo "<td style='text-align:left'><i> (noch drin) </i></td>";
      echo "</tr>";
    }
    else{
      if ($row[2] == -1)
        echo "<tr style='font-style:italic'>";
      else
        echo "<tr style='font-weight:bold; color: #DD0000'>";
      echo "<td style='text-align:left'>", $row[0], "</td>";
      echo "<td style='text-align:center'> $row[1] </td>";
      echo "<td style='text-align:left'>";
      $landid = $row[3];
      $qr2 = $db->query("SELECT user.name FROM user, laender WHERE user.meistertip = laender.id AND laender.id = '$landid';");
      if (mysql_num_rows($qr2) == 0){
	echo "-";
      }
      else{
	$row2 = mysql_fetch_row($qr2);
	if ($row2){
	  echo $row2[0];
	}
	while ($row2 = mysql_fetch_row($qr2)){
	  echo "<br>",$row2[0];
	}
      }
      echo "</td>";
      echo "</tr>";
    }
    
  }
  echo "</table>";
  echo "</div>";
}

// Erstellt eine Tabelle mit den Tipps aller User zu einem Spiel
function buildMatchtipps($matchid, $userid)
{ 
  
  $game = new Game();
  $db = new Database();
  // S9) Tipps aller User zum Spiel $spielid
//     | Name | Tipp | UserID | Wettb.? | SpielStatus | Richtig? | Tor-Diff-Diff | Tor-Anz-Diff |
//     (Wettb.?: macht_mit = 1, nicht = 0)
//     (Richtig?:  Ergebnis = -2, Tendenz = -1, Falsch = 0)
//     (Tor-Diff-Diff: Differenz der Tordifferenzen von Tipp und Ergebnis)
//     (Tor-Anz-Diff: Differenz der geschossenen Tore von Tipp und Ergebnis)
$sqlS9 = "SELECT user.name, CONCAT(tipps.tore1, ':', tipps.tore2) AS ergebnis,
  user.id, user.wettbewerb, spiele.status,
  IF(spiele.tore1 = tipps.tore1 AND spiele.tore2 = tipps.tore2, -3,
     (IF(spiele.tore1 - spiele.tore2 = tipps.tore1 - tipps.tore2
           AND spiele.tore1 <> spiele.tore2, -2,
         IF(((spiele.tore1 > spiele.tore2 AND tipps.tore1 > tipps.tore2)
              OR (spiele.tore1 = spiele.tore2 AND tipps.tore1 = tipps.tore2)
              OR (spiele.tore1 < spiele.tore2 AND tipps.tore1 < tipps.tore2)), -1, 0)))) AS tippRichtig,
  ABS(spiele.tore1 - tipps.tore1 - spiele.tore2 + tipps.tore2) AS torDiff,
  ABS(spiele.tore1 + spiele.tore2 - tipps.tore1 - tipps.tore2) AS anzToreDiff
FROM (user JOIN tipps ON user.id = tipps.userid)
  JOIN spiele ON tipps.spielid = spiele.id
WHERE spiele.id = '$matchid' AND spiele.datum < addtime(NOW(), SEC_TO_TIME(". TIMESHIFT ."))
ORDER BY tippRichtig, torDiff, anzToreDiff, (tipps.tore1+tipps.tore2) DESC, tipps.tore2, tipps.id;";

  echo "<div style='text-align:center'>"; // table centering for IEs
  echo"  <table id=\"Highscore\">
<tr>
<th>Name</th>
<th>Tipp</th>
<th>Platz</th>
</tr>";
 
  $query_result = $db->query($sqlS9); 
  if (mysql_num_rows($query_result) == 0){
    echo "<tr> <td colspan = 8><i> (keine) </i></td> <tr>";
  }
  while ($row = mysql_fetch_row($query_result)){
    if ($row[2] == $cur_uid){
      echo "<tr bgcolor=", GUIBuilder::$col_curusr, ">";
    }
    else{
      if ($row[3] > 0){
	echo "<tr bgcolor=", GUIBuilder::$col_wett, ">";
      }
      else{
	echo "<tr>";
      }
    }

    // Berechnung der Farbe f�r den Tipp. ---- 
    if ($row[4] < 2){
     $ec = $col_notset;
    }
    else if ($row[5] == -3){
      $ec = GUIBuilder::$col_er;
    }
    else if ($row[5] == -2){
      $ec = GUIBuilder::$col_tr;
    }
    else if ($row[5] == -1){
      $ec = GUIBuilder::$col_sr;
    }
    else{
      $ec = GUIBuilder::$col_f;
      }
    // ----------------------------------------
    $retlink = "gametipps.php?spielid=".$matchid;
    echo "<td style='text-align: left'><b><a style='color:#000066' href = 'usertipps.php?ouid=", $row[2], "&retlink=",$retlink,"'>", $row[0], "</a></b></td>";
    //echo "<td>", $row[0], "</td>";
    echo "<td style='text-align: center; background-color:", $ec, "'><b>", $row[1], "</b></td>";
    echo "<td style='text-align: center'><b>", $game->getHighscorePosition($row[2], 0), "</b></td>";
    echo "</tr>";
  }
  echo" </table><br>";
  echo" </div>";
}

public static function buildNewsboardTable()
{
	$sql = "SELECT user.name, 
	DATE_FORMAT(newsboard.datum, '%d.%m.%y um %H:%i') AS datum, 
	newsboard.text, newsboard.id
	FROM user, newsboard
	WHERE user.id = newsboard.userid
	ORDER BY newsboard.datum DESC;";
	$db = new Database();
	$query_result = $db->query($sql);

	$fid = 0;
	echo "<div style='text-align:center'>"; // table centering for IEs
	echo "<table id=\"Highscore\">";
	while ($row = mysql_fetch_row($query_result))
	{
	  if ($fid == 0){
			$fid = $row[3];
	  }
	  $row[2] = str_replace("<","&lt;", $row[2]);
	  $row[2] = str_replace(">","&gt;", $row[2]);
	  echo "<tr> <th><b> ", $row[0], " schrieb am ", $row[1], " : </b></th></tr>";
	  echo "<tr><td style='width:600px'><pre width=80>", $row[2], "</pre></td></tr>";
	}

	echo "</table>";
	echo "</div>";
	
}

public static function buildNewsboardTableSince($datetime)
{
	$sql = "SELECT user.name, 
	DATE_FORMAT(newsboard.datum, '%d.%m.%y um %H:%i') AS datum, 
	newsboard.text, newsboard.id
	FROM user, newsboard
	WHERE user.id = newsboard.userid AND 
	newsboard.datum > '$datetime'
	ORDER BY newsboard.datum DESC;";
	$db = new Database();
	$query_result = $db->query($sql);

	$fid = 0;
	echo "<div style='text-align:center'>"; // table centering for IEs
	echo "<table id=\"Highscore\">";
	while ($row = mysql_fetch_row($query_result))
	{
	  if ($fid == 0){
			$fid = $row[3];
	  }
	  $row[2] = str_replace("<","&lt;", $row[2]);
	  $row[2] = str_replace(">","&gt;", $row[2]);
	  echo "<tr> <th><b> ", $row[0], " schrieb am ", $row[1], " : </b></th></tr>";
	  echo "<tr><td style='width:600px'><pre width=80>", $row[2], "</pre></td></tr>";
	}

	echo "</table>";
	echo "</div>";
	
}

	public static function buildMailToAllUsersLink()
	{
		$sql = "select email from user;";
	
		$db = new database();
		$query_result = $db->query($sql);

		 echo "<a href='mailto:";
			
			while ($row = mysql_fetch_row($query_result))
			  echo $row[0], "; ";
			
		echo "'>hier klicken</a>";
	}


	public static function showNoAccessPage()
	{
		echo "<h1>Fehler</h1>";

		echo "<p>Du bist zur Zeit nicht eingeloggt und hast keinen Zugriff auf diese Seite.</p>";
		echo "<p><a href='index.php'>Zur&uuml;ck zur Startseite</a>.</p>";
	}


	public static function buildFootnotes()
	{
	        echo "<div style='clear:right'></div>";

	        echo "<p><br></p>";

	        echo "<img src='../layout/bg_navi2.gif' width='100%' height='2px'>";

	        echo "<div style='font-size:0.8em'>";

	        echo "<p>";
	        echo "Hinweise:";
	        echo "</p>";
	        echo "<ul>";

	        if (defined("HINWEIS_SPIELER_FARBE"))
	                echo "<li style='margin:0.3em'> In der Highscore wird der eigene Name rot hervorgehoben und die Namen der Wettbewerbs-Teilnehmer sind blau eingef&auml;rbt. </li>";
	        if (defined("HINWEIS_SPIELER_LINK"))
	                echo "<li style='margin:0.3em'> Auf den Namen eines Spielers klicken, um dessen Tipps f&uuml;r die abgelaufenen Spiele zu sehen. </li>";
	        if (defined("HINWEIS_SPIEL_LINK"))
	                echo "<li style='margin:0.3em'> Auf eine laufende oder beendete Spielbegegnung klicken, um sich alle Tipps zu diesem Spiel anzeigen zu lassen. </li>";
	        if (defined("HINWEIS_ETSF_PROZENT"))
	                echo "<li style='margin:0.3em'> Die Prozentangaben bei abgelaufenen Spielen zeigen an, wieviele Mitspieler f&uuml;r diese Begegnung das richtige Ergebnis (E), die richtige Tordifferenz (T), den richtigen Sieger (S) oder den falschen Sieger (F) getippt haben. </li>";

	        echo "</ul>";
	
	        echo "</div>";
	}

	public static function buildBackgroundChangeScript()
	{
		echo "<script type='text/javascript'>
			function setColor (control) 
			{
				if (control.style) 
				{
					if (control.value != control.defaultValue) 
					{
						control.style.backgroundColor = '#F5A9A9';
					}
					else 
					{
						control.style.backgroundColor = '';
					}
				}
			}
			</script>";
	}

	// Color definitions
	private static $col_er = "#61ff3c"; // In Tabelle: Ergebnis richtig
	private static $col_tr = "#66d1ff"; // In Tabelle: Tordifferenz richtig
	private	static $col_sr = "#ffff32"; // In Tabelle: Sieger richtig
	private	static $col_f  = "#ff6655"; // In Tabelle: Tipp falsch
	private static $col_notipp = "#e8e8e8"; // In Tabelle: Benutzer hat f�r dieses Spiel gar nicht getippt
	private static $col_wett = "#FFFFB5"; // In Tabelle: Benutzer, die beim Wettbewerb mitmachen.
	private	static $col_curusr = "#8BD084"; // In Tabelle: Aktueller Benutzer
	private	static $col_notset = "#AAAAAA"; // Spiel noch nicht eingegeben
	
}
?>
