<?php

include_once("../config/config.php");
include_once("Exceptions.php");

class Database
{
	public function DataBase()
	{
		$this->connect();
	
	}
	
	public function query($query)
	{
		$result = mysql_query($query);
		if ($result == null)
		{
			throw new ExceptionDatabase("Query failed: ".$query." ".mysql_error());
		}
		return $result;
	}
	
	public function queryRow($query)
	{
		$result = mysql_query($query);
		if ($result == null)
		{
			throw new ExceptionDatabase("Query failed: ".$query." ".mysql_error());
		}
		return mysql_fetch_row($result);
	}
	
	public function queryResult($query)
	{
		$result = mysql_query($query);
		if (!$result)
		{
			throw new ExceptionDatabase("Query failed: ".$query." ".mysql_error());
		}
		$row = mysql_fetch_row($result);
		return $row[0];
	}
	

	public function multiQuery($queries)
	{
		$tok = strtok($queries, ";");
		while ($tok !== false)
		{
			$this->Query($tok);
			$tok = strtok(";");
		}
	}
	
	public static function createDatabase($name)
	{
		$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
		if (!$db)
		{
			throw new ExceptionDatabase("Cannot connect to database server.");
		}

		if (mysql_query('CREATE DATABASE IF NOT EXISTS '.$name, $db))
		{
			return;
		}
		else
		{
			throw new ExceptionDatabase('Database '.$name.' could not be created. Probably it already exists?');
		}
	}
	// Connect to sql database. Database access parameters are defined in config file.
	private function connect()
	{
		$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
		if (!$db)
		{
			throw new ExceptionDatabase("Cannot connect to database server.");
		}
		$dbcon = mysql_select_db(DB_DBNAME);
		if (!$dbcon)
		{
			throw new ExceptionDatabase("Database could not be found.");
		}	
	}

	 var $dbcon;



}

?>