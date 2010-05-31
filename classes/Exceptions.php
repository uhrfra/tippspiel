<?php

include_once("../config/config.php");
include_once("Logging.php");


// An exception that logs when it is thrown. The logfile is specified in config.
// All further exceptions should be derivated from this class.
class LoggingException extends Exception
{

	
	public function __construct($message, $logmsg = "", $code = 0)
	{
		parent::__construct($message, $code);
		
		Logging::logException($message, $this->getFile(), $this->getLine(), $logmsg, $code);
	}
}

class ExceptionDatabase extends LoggingException
{
	public function __construct($message, $logmessage = "", $code = 0)
	{
		parent::__construct($message, $logmessage, $code);
	}
}

class ExceptionInvalidUser extends LoggingException
{
	public function __construct($message, $logmessage = "", $code = 0)
	{
		parent::__construct($message, $logmessage, $code);
	}
}

class ExceptionSession extends LoggingException
{
	public function __construct($message, $logmessage = "", $code = 0)
	{
		parent::__construct($message, $logmessage, $code);
	}
}

class ExceptionProgram extends LoggingException
{
	public function __construct($message, $logmessage = "", $code = 0)
	{
		parent::__construct($message, $logmessage, $code);
	}
}

class ExceptionTip extends LoggingException
{
	public function __construct($message, $logmessage = "", $code = 0)
	{
		parent::__construct($message, $logmessage, $code);
	}
}

class ExceptionMatch extends LoggingException
{
	public function __construct($message, $logmessage = "", $code = 0)
	{
		parent::__construct($message, $logmessage, $code);
	}
}

?>