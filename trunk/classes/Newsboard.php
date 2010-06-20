<?php

include_once("../config/config.php");
include_once("Database.php");
include_once("Exceptions.php");



class Newsboard
{
	
	public static function postMessage($userid, $message)
	{
		$sql = "INSERT INTO newsboard (userid, datum, text) VALUES ('$userid', NOW(), '$message');";
	    $db = new Database();
		$db->query($sql);
	
	}
	
	public static function getNumEntriesSince($datetime)
	{
		$sql = "SELECT COUNT(*) FROM newsboard WHERE datum > '$datetime';";
	    $db = new Database();
		return $db->queryResult($sql);
	}
}

?>