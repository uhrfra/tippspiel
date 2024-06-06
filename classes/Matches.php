<?php

include_once("../config/config.php");
include_once("Database.php");
include_once("Exceptions.php");

define("MATCHSTATUS_OPEN", 0);
define("MATCHSTATUS_RUNNING", 1);
define("MATCHSTATUS_CLOSED", 2);

class MatchEvent
{
	public $id;
	public $teamid1;
	public $teamname1;
	public $teamid2;
	public $teamname2;
	public $datetime;
	public $matchdayid;
	public $matchdayname;
	public $gamestatus;
	public $goals1;
	public $goals2;
	
	public $tippgoals1;
	public $tippgoals2;
}

class Tip
{
	public $matchid;
	public $goals1;
	public $goals2;
}
	
	
class Matches
{
	private $db;
	public function Matches()
	{
		
	}
	
	public function started()
	{
		$stampsplit = preg_split("/[\.]|[:]|[,]/", GAMESTART);
		$stamp = mktime((int) $stampsplit[3], (int)  $stampsplit[4], 0, (int) $stampsplit[1], (int) $stampsplit[0], (int) $stampsplit[2]);
			
		if ($stamp > time())
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	
	// Returns the ID of the matchday with the most recent non-finished match.
	public function getCurrentMatchday()
	{
		$db = new Database();
		return $db->queryResult("SELECT matchdays.id FROM spiele, matchdays WHERE (spiele.matchday = matchdays.id AND spiele.datum > addtime(NOW(), SEC_TO_TIME(". TIMESHIFT. "))) ORDER BY spiele.datum ASC;");
	}
	
	// Returns the ID of the matchday after the current matchday or null if none exists.
	public function getNextMatchday()
	{
		$db = new Database();
		$result =  $db->query("SELECT matchdays.id FROM spiele, matchdays WHERE (spiele.matchday = matchdays.id AND spiele.datum > addtime(NOW(), SEC_TO_TIME(". TIMESHIFT. "))) ORDER BY spiele.datum ASC;");
		$currentmd = $this->getCurrentMatchday();
		while ($row = mysqli_fetch_row($result))
		{
			if ($row[0] != currentmd)
			{
				return $row[0];
			}
		}
		return -1;
	}
	
	// Returns the ID of the matchday before the current matchday or null if none exists.
	public function getPreviousMatchday()
	{
		$db = new Database();
		$result =  $db->query("SELECT matchdays.id FROM spiele, matchdays WHERE (spiele.matchday = matchdays.id AND spiele.datum < addtime(NOW(), SEC_TO_TIME(". TIMESHIFT. "))) ORDER BY spiele.datum DESC;");
		$currentmd = $this->getCurrentMatchday();
		while ($row = mysqli_fetch_row($result))
		{
			if ($row[0] != $currentmd)
			{
				return $row[0];
			}
		}
		return null;
	}
	
	

	public function getAllMatches()
	{
	$sql = "SELECT DATE_FORMAT(spiele.datum, '%d.%m.%y - %H:%i') AS datum,
  l1.id,
  l1.land,
  l2.id,
  l2.land,
  spiele.id
FROM (spiele LEFT JOIN `laender` AS l1 ON spiele.ms1 = l1.id) LEFT JOIN `laender` AS l2 ON spiele.ms2 = l2.id
ORDER BY spiele.datum;";

		$db = new Database;
		$result = $db->query($sql);
		$ret = array();
		while($row = mysqli_fetch_row($result))
		{
			$m = new MatchEvent();
			$m->datetime = $row[0];
			$m->teamid1 = $row[1];
			$m->teamname1 = $row[2];
			$m->teamid2 = $row[3];
			$m->teamname2 = $row[4];
			$m->id = $row[5];
			array_push($ret, $m);
		}
		return $ret;
	}
	
	// Returns the match with the bigest timestamp.
	public function getLastMatch()
	{
		return getLastMatch2(null);
	}
	
	public function getLastMatch2($datetime_format)
	{
		if ($datetime_format == null)
		{
			$datetime_format = "%d.%.m:%Y - %H:%i";
		}
		$sql = "SELECT DATE_FORMAT(spiele.datum, '$datetime_format') AS datum,
		  l1.id,
		  l1.land,
		  l2.id,
		  l2.land,
		  spiele.id
FROM (spiele LEFT JOIN `laender` AS l1 ON spiele.ms1 = l1.id) LEFT JOIN `laender` AS l2 ON spiele.ms2 = l2.id
ORDER BY spiele.datum DESC;";

		$db = new Database;
		$result = $db->query($sql);
		$ret = array();
		if($row = mysqli_fetch_row($result))
		{
			$m = new MatchEvent();
			$m->datetime = $row[0];
			$m->teamid1 = $row[1];
			$m->teamname1 = $row[2];
			$m->teamid2 = $row[3];
			$m->teamname2 = $row[4];
			$m->id = $row[5];
			return $m;
		}
		else
		{
			return null;
		}
	}
	
	public function getHighestMatchId()
	{
	$sql = "SELECT DATE_FORMAT(spiele.datum, '%d.%m.%y - %H:%i') AS datum,
		  l1.id,
		  l1.land,
		  l2.id,
		  l2.land,
		  spiele.id
FROM (spiele LEFT JOIN `laender` AS l1 ON spiele.ms1 = l1.id) LEFT JOIN `laender` AS l2 ON spiele.ms2 = l2.id
ORDER BY spiele.id DESC;";

		$db = new Database;
		$result = $db->query($sql);
		$ret = array();
		if($row = mysqli_fetch_row($result))
		{
			return $row[5];
		}
		else
		{
			return 0;
		}
	
	}
	public function addMatch(MatchEvent $m)
	{
		$db = new Database();
		//Add only if not exists!!!
		if ($db->queryRow("SELECT * FROM spiele WHERE ms1 = '$m->teamid1' AND ms2 = '$m->teamid2' AND datum = '$m->datetime';"))
		{
			throw new ExceptionMatch("Das Spiel wurde bereits eingetragen!");
		}
	
		// TODO: This regexp is not perfect and should be improved.
		$reg_exp="/\d{4}-\d{2}-\d{2} \d{2}:\d{2}/";
		if(!preg_match ($reg_exp, $m->datetime ))
		{
			throw new ExceptionMatch("Ung�ltige Eingabe f�r Datum/Uhrzeit.");
		}
			
		$query = "INSERT INTO spiele(ms1, ms2, datum, matchday) VALUES ('$m->teamid1', '$m->teamid2', '$m->datetime' , '$m->matchdayid')"; 
		$db->query($query);
	}

	public function addMatchByNames(MatchEvent $m)
	{
		$db = new Database();
		$m->teamid1 = $db->queryResult("SELECT id FROM laender WHERE land = '$m->teamname1';");
		if ($m->teamid1 == 0)
		{
			throw new ExceptionMatch("Unknown team name ".$m->teamname1.".");
		}

		$m->teamid2 = $db->queryResult("SELECT id FROM laender WHERE land = '$m->teamname2';");
		if ($m->teamid2 == 0)
		{
			throw new ExceptionMatch("Unknown team name ".$m->teamname2.".");
		}

		$m->matchdayid = $db->queryResult("SELECT id FROM matchdays WHERE name = '$m->matchdayname';");
		if ($m->matchdayid == 0)
		{
			throw new ExceptionMatch("Unknown matchday name ".$m->matchdayname.".");
		}

		$this->addMatch($m);
	}
	
	public function getAllOpenMatches($userid)
	{
		$sqlS4 = "SELECT DATE_FORMAT(spiele.datum, '%d.%m.%y - %H:%i') AS datum,
		IF(l1.land IS NULL, -1, l1.id),
	  IF(l1.land IS NULL, '<i>(nicht bekannt)</i>', l1.land),
	  IF(l2.land IS NULL, -1, l2.id),
	  IF(l2.land IS NULL, '<i>(nicht bekannt)</i>', l2.land),
	  IF(tipps.id IS NULL, '',
	    CONCAT(tipps.tore1, ':', tipps.tore2)) AS tipp,
	  spiele.id,
	  tipps.tore1,
	  tipps.tore2, mds.name
	  FROM (((spiele LEFT JOIN tipps ON spiele.id = tipps.spielid AND tipps.userid = '$userid')
	  LEFT JOIN `laender` AS l1 ON spiele.ms1 = l1.id) LEFT JOIN `laender` AS l2 ON spiele.ms2 = l2.id) 
	  LEFT JOIN `matchdays` AS  mds ON spiele.matchday = mds.id
	  WHERE (spiele.datum > addtime(NOW(), SEC_TO_TIME(". TIMESHIFT. ")))
	  ORDER BY spiele.datum;";
		$db = new Database;
		$result = $db->query($sqlS4);
		$ret = array();
		while($row = mysqli_fetch_row($result))
		{
			$m = new MatchEvent();
			$m->datetime = $row[0];
			$m->teamid1 = $row[1];
			$m->teamname1 = $row[2];
			$m->teamid2 = $row[3];
			$m->teamname2 = $row[4];
			$m->id = $row[6];
			$m->tippgoals1 = $row[7];
			$m->tippgoals2 = $row[8];
			$m->matchdayname = $row[9];
			array_push($ret, $m);
		}
		return $ret;
	}
	
	public function getOpenMatchesOfMatchday($userid, $matchdayid)
	{
		$sqlS4 = "SELECT DATE_FORMAT(spiele.datum, '%d.%m.%y - %H:%i') AS datum,
		IF(l1.land IS NULL, -1, l1.id),
	  IF(l1.land IS NULL, '<i>(nicht bekannt)</i>', l1.land),
	  IF(l2.land IS NULL, -1, l2.id),
	  IF(l2.land IS NULL, '<i>(nicht bekannt)</i>', l2.land),
	  IF(tipps.id IS NULL, '',
	    CONCAT(tipps.tore1, ':', tipps.tore2)) AS tipp,
	  spiele.id,
	  tipps.tore1,
	  tipps.tore2,
	  mds.name
FROM (((spiele LEFT JOIN tipps ON spiele.id = tipps.spielid AND tipps.userid = '$userid')
	  LEFT JOIN `laender` AS l1 ON spiele.ms1 = l1.id) LEFT JOIN `laender` AS l2 ON spiele.ms2 = l2.id)
	  LEFT JOIN `matchdays` AS  mds ON spiele.matchday = mds.id
WHERE (spiele.datum > addtime(NOW(), SEC_TO_TIME(". TIMESHIFT. ")) AND spiele.matchday = '$matchdayid') 
ORDER BY spiele.datum;";

		$db = new Database;
		$result = $db->query($sqlS4);
		$ret = array();
		while($row = mysqli_fetch_row($result))
		{
			$m = new MatchEvent();
			$m->datetime = $row[0];
			$m->teamid1 = $row[1];
			$m->teamname1 = $row[2];
			$m->teamid2 = $row[3];
			$m->teamname2 = $row[4];
			$m->id = $row[6];
			$m->tippgoals1 = $row[7];
			$m->tippgoals2 = $row[8];
			$m->matchdayname = $row[9];
			array_push($ret, $m);
		}
		return $ret;
	}
	
		public function getAllRunningMatches()
	{
	$sql = "SELECT DATE_FORMAT(spiele.datum, '%d.%m.%y - %H:%i') AS datum,
  l1.id,
  l1.land,
  l2.id,
  l2.land,
  spiele.id
FROM (spiele LEFT JOIN `laender` AS l1 ON spiele.ms1 = l1.id) LEFT JOIN `laender` AS l2 ON spiele.ms2 = l2.id
WHERE (spiele.status = 0 AND spiele.datum < addtime(NOW(), SEC_TO_TIME(". TIMESHIFT. ")))
ORDER BY spiele.datum;";

		$db = new Database;
		$result = $db->query($sql);
		$ret = array();
		while($row = mysqli_fetch_row($result))
		{
			$m = new MatchEvent();
			$m->datetime = $row[0];
			$m->teamid1 = $row[1];
			$m->teamname1 = $row[2];
			$m->teamid2 = $row[3];
			$m->teamname2 = $row[4];
			$m->id = $row[5];
			array_push($ret, $m);
		}
		return $ret;
	}
	
	
	
	public function setResult($matchid, $goals1, $goals2)
	{
		$db = new Database();
		$sql = "UPDATE spiele SET tore1 ='$goals1', tore2='$goals2', status= '1'  WHERE id = '$matchid'";
		echo $sql;
        $db->query($sql);
		$this->updateDB();
	}
	
	// Updates the statistics in the users and matches tables after a new result has been committed.
	// Uses a sql-transaction.
	public function updateDB()
	{
		$db = new Database();
		
		// Start transaction
		$db->query("BEGIN;");
		
		// D2) Anzahl richtiger Ergebnisse, richtiger Tendenzen und falscher Tipps pro User:
		//     | userid | anz_er | anz_tr | anz_f |
		if (DRAW_IS_TENDENCY)
		{
			$sqlD2 ="SELECT tipps.userid,
		  COUNT(IF(spiele.tore1 = tipps.tore1 AND spiele.tore2 = tipps.tore2,
		           spiele.id, null)) AS anz_er,
		  COUNT(IF((CAST(spiele.tore1 as signed) - CAST(spiele.tore2 as signed) = CAST(tipps.tore1 as signed) - CAST(tipps.tore2 as signed))
		            AND NOT (spiele.tore1 = tipps.tore1 AND spiele.tore2 = tipps.tore2)
		            AND NOT spiele.tore1 = spiele.tore2,
		           spiele.id, null)) AS anz_tr,
		  COUNT(IF(((spiele.tore1 > spiele.tore2 AND tipps.tore1 > tipps.tore2)
		             OR (spiele.tore1 = spiele.tore2 AND tipps.tore1 = tipps.tore2)
		             OR (spiele.tore1 < spiele.tore2 AND tipps.tore1 < tipps.tore2))
		            AND NOT (spiele.tore1 = tipps.tore1 AND spiele.tore2 = tipps.tore2) 
		            AND ((CAST(spiele.tore1 as signed) - CAST(spiele.tore2 as signed) != CAST(tipps.tore1 as signed) - CAST(tipps.tore2 as signed))
		                 OR (spiele.tore1 = spiele.tore2)),
		           spiele.id, null)) AS anz_sr,
		  COUNT(IF((spiele.tore1 > spiele.tore2 AND tipps.tore1 <= tipps.tore2)
		            OR (spiele.tore1 = spiele.tore2 AND tipps.tore1 != tipps.tore2)
		            OR (spiele.tore1 < spiele.tore2 AND tipps.tore1 >= tipps.tore2),
		          spiele.id, null)) AS anz_f
		FROM `spiele` INNER JOIN `tipps` ON spiele.id = tipps.spielid AND spiele.status > 0
		GROUP BY tipps.userid;";
		}
		else
		{
			$sqlD2 ="SELECT tipps.userid,
		  COUNT(IF(spiele.tore1 = tipps.tore1 AND spiele.tore2 = tipps.tore2,
		           spiele.id, null)) AS anz_er,
		  COUNT(IF((CAST(spiele.tore1 as signed) - CAST(spiele.tore2 as signed) = CAST(tipps.tore1 as signed) - CAST(tipps.tore2 as signed))
		            AND NOT (spiele.tore1 = tipps.tore1 AND spiele.tore2 = tipps.tore2),
		           spiele.id, null)) AS anz_tr,
		  COUNT(IF(((spiele.tore1 > spiele.tore2 AND tipps.tore1 > tipps.tore2)
		             OR (spiele.tore1 = spiele.tore2 AND tipps.tore1 = tipps.tore2)
		             OR (spiele.tore1 < spiele.tore2 AND tipps.tore1 < tipps.tore2))
		            AND NOT (spiele.tore1 = tipps.tore1 AND spiele.tore2 = tipps.tore2) 
		            AND (CAST(spiele.tore1 as signed) - CAST(spiele.tore2 as signed) != CAST(tipps.tore1 as signed) - CAST(tipps.tore2 as signed))
					AND NOT spiele.tore1 = spiele.tore2,
		           spiele.id, null)) AS anz_sr,
		  COUNT(IF((spiele.tore1 > spiele.tore2 AND tipps.tore1 <= tipps.tore2)
		            OR (spiele.tore1 = spiele.tore2 AND tipps.tore1 != tipps.tore2)
		            OR (spiele.tore1 < spiele.tore2 AND tipps.tore1 >= tipps.tore2),
		          spiele.id, null)) AS anz_f
		FROM `spiele` INNER JOIN `tipps` ON spiele.id = tipps.spielid AND spiele.status > 0
		GROUP BY tipps.userid;";

		}

		$query_result = $db->query($sqlD2);

		// Update user statistics
		 while ($row = mysqli_fetch_row($query_result)){
		   $userid = $row[0];
		   $anz_er = $row[1];
		   $anz_tr = $row[2];
		   $anz_sr = $row[3];
		   $anz_f = $row[4];
		   //D3) Statistik des Users $userid mit Hilfe der Variablen $anz_er, $anz_tr, $anz_f
			//    aktualisieren:
			$sqlD3 = "UPDATE `user`
			SET anz_er = '$anz_er',
			anz_tr = '$anz_tr',
			anz_sr = '$anz_sr',
			anz_f = '$anz_f'
			WHERE id = '$userid';";
		   $db->query($sqlD3);
		 }
 
 
		//D4) Anzahl richtiger Ergebnisse, richtiger Tendenzen und falscher Tipps f�r
		//    alle Spiele mit dem Status = 1:
		//     | spielid | anz_er | anz_tr | anz_f |
		$sqlD4 = "SELECT spiele.id,
		  COUNT(IF(spiele.tore1 = tipps.tore1 AND spiele.tore2 = tipps.tore2,
		           spiele.id, null)) AS anz_er,
	  COUNT(IF((CAST(spiele.tore1 as signed) - CAST(spiele.tore2 as signed) = CAST(tipps.tore1 as signed) - CAST(tipps.tore2 as signed))
		            AND NOT (spiele.tore1 = tipps.tore1 AND spiele.tore2 = tipps.tore2)
		            AND NOT spiele.tore1 = spiele.tore2,
		           spiele.id, null)) AS anz_tr,
		  COUNT(IF(((spiele.tore1 > spiele.tore2 AND tipps.tore1 > tipps.tore2)
		             OR (spiele.tore1 = spiele.tore2 AND tipps.tore1 = tipps.tore2)
		               OR (spiele.tore1 < spiele.tore2 AND tipps.tore1 < tipps.tore2))
		            AND NOT (spiele.tore1 = tipps.tore1 AND spiele.tore2 = tipps.tore2) 
		            AND ((CAST(spiele.tore1 as signed) - CAST(spiele.tore2 as signed) != CAST(tipps.tore1 as signed) - CAST(tipps.tore2 as signed))
		                 OR (spiele.tore1 = spiele.tore2)),
		           spiele.id, null)) AS anz_sr,
		  COUNT(IF((spiele.tore1 > spiele.tore2 AND tipps.tore1 <= tipps.tore2)
		            OR (spiele.tore1 = spiele.tore2 AND tipps.tore1 != tipps.tore2)
		            OR (spiele.tore1 < spiele.tore2 AND tipps.tore1 >= tipps.tore2),
		          spiele.id, null)) AS anz_f
		FROM `spiele` LEFT JOIN `tipps` ON spiele.id = tipps.spielid
		WHERE spiele.status = 1
		GROUP BY spiele.id;";
		$query_result = $db->query($sqlD4);
		 while ($row = mysqli_fetch_row($query_result)){
		   $spielid = $row[0];
		   $anz_er = $row[1];
		   $anz_tr = $row[2];
		   $anz_sr = $row[3];
		   $anz_f = $row[4];
		   $sqlD5 = "UPDATE `spiele`
			SET anz_er = $anz_er,
			anz_tr = $anz_tr,
			anz_sr = $anz_sr,
			anz_f = $anz_f,
			status = 2
			WHERE id = $spielid;";
		    $db->query($sqlD5);
		 }
 
		// End transaction
		$db->query("COMMIT;");
	}
	
	public function getNumberOfRemainingMatches()
	{
		$db = new Database;
		$result = $db->queryResult("SELECT COUNT(*) FROM `spiele` WHERE status > 0;");
		return TOTAL_MATCHES - $result;
	}
}
?>
