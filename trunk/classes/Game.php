<?php

include_once("../config/config.php");
include_once("Database.php");
include_once("Exceptions.php");
include_once("Logging.php");


class Champtip
{
	public $id;
	public $name;
	public $instatus;
}

class TipStats
{
	public $numResultRight;
	public $scoreResultRight;
	public $numDiffRight;
	public $scoreDiffRight;
	public $numTendencyRight;
	public $scoreTendencyRight;
	public $numWrong;
	public $champtip;
	public $statusChamptip;
	public $scoreChamptip;
}

class Game
{
	
	// Returns the score of the user
	public static function getScore($userid)
	{
		$score_champtip = SCORE_CHAMPTIP; // Champion tip is right.
		$score_result = SCORE_RESULT; // Result is tipped right.
		$score_diff = SCORE_DIFF; // Difference is tipped right.
		$score_tendency = SCORE_TENDENCY; // Tendency is tipped right.
		$score_draw_result = SCORE_DRAW_RESULT; // Game ended draw and result is tipped right.
		$score_draw_tendency = SCORE_DRAW_TENDENCY; // Game ended draw and tendency is right.
		$sqlU1 = "SELECT (anz_er * $score_result + anz_tr * $score_diff + anz_sr * $score_tendency) as punkte
			FROM `user`
			WHERE user.id = '$userid';";
		$db = new Database();
		return $db->queryResult($sqlU1);
	
	}
	// Returns the highscor position of the user.
	public static function getHighscorePosition($userid, $onlywett)
	{
		$score_champtip = SCORE_CHAMPTIP; // Champion tip is right.
		$score_result = SCORE_RESULT; // Result is tipped right.
		$score_diff = SCORE_DIFF; // Difference is tipped right.
		$score_tendency = SCORE_TENDENCY; // Tendency is tipped right.
		$score_draw_result = SCORE_DRAW_RESULT; // Game ended draw and result is tipped right.
		$score_draw_tendency = SCORE_DRAW_TENDENCY; // Game ended draw and tendency is right.
		$sqlU2 = "SELECT COUNT(*) as platz
			FROM ((`user` AS u1 LEFT JOIN `laender` ON u1.meistertip = laender.id) JOIN `user` as u2)
			LEFT JOIN `laender` AS l2 ON u2.meistertip = l2.id
			WHERE u1.id = '$userid'";
		if  ($onlywett)
		{
			$sqlU2 = $sqlU2 . " AND u2.wettbewerb > 0 ";
		}
		
		$sqlU2 = $sqlU2 . "	AND ((IF(laender.meisterstatus = 1, $score_champtip, 0) + u1.anz_er * $score_result + u1.anz_tr * $score_diff + 	u1.anz_sr * $score_tendency)
			< (IF(l2.meisterstatus = 1, $score_champtip, 0) + u2.anz_er * $score_result + u2.anz_tr * $score_diff + u2.anz_sr * $score_tendency)
			OR (u1.id = u2.id));";

	
		$db = new Database();
		return $db->queryResult($sqlU2);
	}
	
	public static function getNumUsers($onlywett)
	{
		$db = new Database();
		if ($onlywett)
		{
			return $db->queryResult("SELECT COUNT(*) FROM `user` WHERE wettbewerb > 0;");
		}
		else
		{
			return $db->queryResult("SELECT COUNT(*) FROM `user`;");
		}
	}
	
	public static function insertPostedTipps($userid)
	{
		if (isset($_POST['tipmatchid']))
		{
			for($k = 0; $k < sizeof($_POST['tipmatchid']); $k++)
			{
				try
				{
					if ($_POST['tip1'][$k] != '' && $_POST['tip2'][$k] != '')
					{
						Game::insertTip($userid, $_POST['tipmatchid'][$k], $_POST['tip1'][$k], $_POST['tip2'][$k]);
					}
				}
				catch (LoggingException $e)
				{
					echo "Tippen fehlgeschlagen: ", $e->getMessage;
				}
			}
		}
	}
	
	public static function insertTip($userid, $matchid, $goals1, $goals2)
	{
	

		$db = new Database();
		$ts = TIMESHIFT;
		//Check if tipping is still allowed.
		if ($db->queryResult("SELECT COUNT(id) FROM spiele WHERE id = '$matchid' AND datum > (NOW() + INTERVAL '$ts' SECOND);") == 0)
		{
			throw new ExceptionTip("Das Spiel hat bereits begonnen. Tippen nicht mehr möglich!");
		}
		//Check if a tip for this match already exists
		$result = $db->query("SELECT id FROM tipps WHERE userid = '$userid' AND spielid = '$matchid';");
		if ($row = mysql_fetch_row($result))
		{
			$tipid = $row[0];
			$db->query("UPDATE tipps SET tore1 = '$goals1', tore2 = '$goals2' WHERE id = '$tipid';");
			Logging::logDebug("User ". $userid. " updated tip ". $tipid. ", match ". $matchid. " to ". $goals1. " : ". $goals2. " - ");
		}
		else
		{
			$db->query("INSERT INTO `tipps` (`userid`, `spielid`,`tore1`, `tore2`) VALUES ('$userid', '$matchid','$goals1', '$goals2');");
			Logging::logDebug("User ". $userid. " inserted tip of match ". $matchid. ": ". $goals1." : ". $goals2. " - ");
		}
		$qr = $db->queryResult("SELECT userid FROM tipps WHERE userid = '$userid' AND spielid = '$matchid' AND tore1 = '$goals1' AND tore2 = '$goals2';");
		if ($qr == $userid)
		{
			Logging::logDebug("Tip ". $goals1. " : ". $goals2. " ok.");
		}
		else
		{
			Logging::logDebug(" Tip error!!!");
		}
		
		if (defined(LOGFILE_DEBUG))
		{
			$qr = $db->queryResult("SELECT COUNT(*) FROM tipps");
			Logging::logDebug("Total tips = ". $qr. "\n");
		}
	}
	
	public static function getChamptip($userid)
	{
	   $sqlU5 = "SELECT laender.land, laender.meisterstatus, laender.id
				FROM user, laender
				WHERE user.id = '$userid' AND user.meistertip = laender.id;";
		$db = new Database();
		$result = $db->query($sqlU5);
		if (result == null)
		{
			return null;
		}
		if ($row = mysql_fetch_row($result))
		{
			$ct = new Champtip;
			$ct->name = $row[0];
			$ct->instatus = $row[1];
			$ct->id = $row[2];
			return $ct;
		}
		else
		{
			return null;
		}
	}
	
	public static function setChamptip($userid, $teamid)
	{
		$db = new Database();
		$db->query("UPDATE user set meistertip = '$teamid' WHERE id = '$userid';");
	}
	
	public static function getTipStats($userid)
	{
		$sqlU5 = "SELECT anz_er, anz_tr, anz_sr, anz_f
		FROM user
		WHERE user.id = '$userid'";
	
		$db = new Database();
		$row = $db->queryRow($sqlU5);
	
		$ut = new TipStats;
		
		$ut->numResultRight = $row[0];
		$ut->scoreResultRight = $row[0] * SCORE_RESULT;
		$ut->numDiffRight = $row[1];
		$ut->scoreDiffRight = $row[1] * SCORE_DIFF;
		$ut->numTendencyRight = $row[2];
		$ut->scoreTendencyRight = $row[2] * SCORE_TENDENCY;
		
		$ct = Game::getChamptip($userid);
		$ut->statusChamptip = $ct->instatus;
		if ($ct->id == 0)
		{
			$ut->champtip = "(nicht getippt)";
		}
		else
		{
			$ut->champtip = $ct->name;
		}
		
		if ($ct->instatus == 2)
		{
			$ut->scoreChamptip = SCORE_CHAMPTIP;
		}
		else
		{
			$ut->scoreChamptip = 0;
		}
	return $ut;
	
	}
}
