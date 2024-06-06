<?php

include_once("../config/config.php");


// A helper class for logging.
class Logging
{
	public static function logException($message, $file, $line, $logmsg = "", $code = 0)
	{
		$fp = fopen(LOGFILE_EXCEPTION, "a");
		if ($fp)
		{
			if (strlen($logmessage) == 0)
			{
				// If no logmessage is set, write exception message to logfile.
				fputs($fp, date("d.m.Y h:i:s", time())." : Exception ".$message.", ".$code." at ".$file.", Line ".$line."\n");
			}
			else
			{
				fputs($fp, date("d.m.Y h:i:s", time())." : Exception ".$message.", ".$code." at ".$file.", Line ".$line."\n");
			}
			fclose($fp);
		}
	}
	
	public static function logDebug($message)
	{
		if (!defined(LOGFILE_DEBUG))
		{
			return;
		}
		
		$fp = fopen(LOGFILE_DEBUG, "a");
		if ($fp)
		{
			fputs($fp, date("d.m.Y h:i:s", time())." : ".$message."\n");
			fclose($fp);
		}
	}
}

?>
