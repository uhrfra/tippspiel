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
		$result = mysqli_query($this->$dbcon, $query);
		if ($result == null)
		{
			throw new ExceptionDatabase("Query failed: ".$query." ".mysqli_error($this->$dbcon));
		}
		return $result;
	}
	
	public function queryRow($query)
	{
		$result = mysqli_query($this->$dbcon, $query);
		if ($result == null)
		{
			throw new ExceptionDatabase("Query failed: ".$query." ".mysqli_error($this->$dbcon));
		}
		return mysqli_fetch_row($result);
	}
	
	public function queryResult($query)
	{
		$result = mysqli_query($this->$dbcon, $query);
		if (!$result)
		{
			throw new ExceptionDatabase("Query failed: ".$query." ".mysqli_error($this->$dbcon));
		}
		$row = mysqli_fetch_row($result);
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
		$db = @ mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
		if (!$db)
		{
			throw new ExceptionDatabase("Cannot connect to database server.");
		}

		mysqli_query($db, 'CREATE DATABASE IF NOT EXISTS '.$name);
	}

	// Connect to sql database. Database access parameters are defined in config file.
	private function connect()
	{
		$this->$dbcon = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DBNAME);
		if (!$this->$dbcon)
		{
			throw new ExceptionDatabase("Cannot connect to database server.");
		}
	}

	 var $dbcon;
}

?>
