<?php

include_once("Database.php");
include_once("Exceptions.php");

class User
{
	public $name = '';
	public $email = '';
	public $adminlevel = 0;
	public $wettbewerb = 0;
	public $attr1 = 0;
	public $attr2 = 0;
	public $attr3 = 0;
	public $logintime = '';
	public $prevlogintime = '';
}

class Session
{
	public function Session()
	{
	}

	// Inserts a new user into the db. Checks if all user entries are valid.
	// \throws ExceptionInvalidUser if the user entries are invalid.
	public static function createUser($login, $password, $password_confirm, $User)
	{

		$db = new Database();

		// Check lengths
		if (strlen($login) < 3 || strlen($login) > 30)
		{
			throw new ExceptionInvalidUser("Login zwischen 3 und 30 Zeichen lang sein.");
		}
		
		if (strlen($password) < 5 || strlen($password) > 50)
		{
			throw new ExceptionInvalidUser("Password muss mindesten fünf Zeichen lang sein.");
		}
		
		if (strlen($User->name) < 5 || strlen($User->name) > 50)
		{
			throw new ExceptionInvalidUser("Name muss zwischen 5 und 32 Zeichen lang sein.");
		}
		
		if (strlen($User->email) > 50)
		{
			throw new ExceptionInvalidUser("E-Mail zu lang (maximal 50 Zeichen.");
		}
		
		if ($db->QueryResult("SELECT * FROM user WHERE login = '$login';") != null)
		{
			throw new ExceptionInvalidUser("Login ungültig.");
		}

		
		
		// Check if both password strings are equal.
		if ($password != $password_confirm)
		{
			throw new ExceptionInvalidUser("Die Eingaben für das Passwort unterscheiden sich.");
		}

		// Check user name
		
		if ($db->QueryResult("SELECT * FROM user WHERE name = '$User->name';") != null)
		{
			throw new ExceptionInvalidUser("Name ungültig.");
		}

		// Check email
		if (strlen($User->email) == 0 ||
			substr_count($User->email, '@') != 1 ||
			substr_count($User->email, '.') < 1)
			{
				throw new ExceptionInvalidUser("E-Mail-Adresse ungültig.");
			}

		// Insert values into database
		$pwmd5 = md5($password);
		$db->query("INSERT INTO `user` (`login`, `passwort`,`name`, `email`, `adminlevel`, `wettbewerb`, `attr1`, `attr2`, `attr3`) VALUES " .
				"('$login', '$pwmd5','$User->name', '$User->email', 0, $User->wettbewerb, $User->attr1, $User->attr2, $User->attr3);");
				
		return Session::getUserIdByLoginAndEmail($login, $User->email);
	}
	
	public static function changeUserPassword($userid, $oldpassword, $newpassword, $newpassword2)
	{
		$db = new Database();

		// Check password
		if (strlen($newpassword) < 5 || strlen($newpassword) > 50)
		{
			throw new ExceptionInvalidUser("Password muss mindesten fünf Zeichen lang sein.");
		}

		if ($newpassword != $newpassword2)
		{
			throw new ExceptionInvalidUser("Die Eingaben für das neue Passwort unterscheiden sich.");
		}
		
		$r = $db->queryResult("SELECT passwort FROM user WHERE id= '$userid';");
		
		if ($r != md5($oldpassword))
		{
			throw new ExceptionInvalidUser("Altes Passwort ist falsch.");
		}
		$pwmd5 = md5($newpassword);
		$db->query("UPDATE user SET passwort = '$pwmd5' WHERE id = '$userid';");
		
	}
	
	public static function resetUserPassword($login, $token, $newpassword, $newpassword2)
	{
		$db = new Database();

		// Check password
		if (strlen(trim ($newpassword)) < 5 || strlen($newpassword) > 50)
		{
			throw new ExceptionInvalidUser("Passwort muss zwischen 5 und 50 Zeichen lang sein.");
		}

		if ($newpassword != $newpassword2)
		{
			throw new ExceptionInvalidUser("Die Eingaben für das neue Passwort unterscheiden sich.");
		}
		
		$userid = $db->queryResult("SELECT id FROM user WHERE login = '$login';");
		if ($userid == null)
		{
			throw new ExceptionInvalidUser("Falsches Login.");
		}
		
		$userid = $db->queryResult("SELECT id FROM user WHERE login = '$login' AND pwresettoken = '$token';");
		if ($userid == null)
		{
			throw new ExceptionSession("Invalid token.");
		}
		$pwmd5 = md5($newpassword);
		$db->query("UPDATE user SET passwort = '$pwmd5' WHERE id = '$userid';");
		$db->query("UPDATE user SET pwresettoken = '' WHERE id = '$userid';");
	}
	
	public static function changeUserEmail($userid, $email)
	{
		$db = new Database();
			// Check email
		if (strlen($email) == 0 ||
			substr_count($email, '@') != 1 ||
			substr_count($email, '.') < 1)
			{
				throw new ExceptionInvalidUser("E-Mail-Adresse ungültig.");
			}
		$db->query("UPDATE user SET email = '$email' WHERE id = '$userid';");
		
	}
	
	
	
	// Create a new session for the given user.
	// Since a cookie is created this function must be called BEFORE ANY HTML OUTPUT!
	// \throws ExceptionInvalidUser if login or password cannot be found in the database. 
	// \throws ExceptionProgram if the cookie could not be set due to html output before this call
	public function login($login, $password)
	{
		$pwmd5 = md5($password);
		
		// Check if user exists in database.
		$db = new Database();
		$res = $db->queryRow("SELECT login, passwort, id, name, logintime FROM user WHERE login = '$login';");
		if ($res == null || $res[0] != $login)
		{
			throw new ExceptionInvalidUser("Falscher Benutzername.");
		}
		
		if ($res[1] != $pwmd5)
		{
			throw new ExceptionInvalidUser("Falsches Passwort.");
		}
		
		// Create a random session id.
		mt_srand((double) microtime()*1000000);
		$sessionid = md5(str_replace(".","",$REMOTE_ADDR) + mt_rand(100000, 999999));
		
		$sessiontime = MAX_SESSION_TIME;
		// Write session id into the database
		$db->query("INSERT INTO sessions(sessionid,validstamp,userid) VALUES('$sessionid',CURRENT_TIMESTAMP + INTERVAL $sessiontime MINUTE, '$res[2]');");
			
		// Create cookie with session id
		if (!setcookie("tippersession", $sessionid))
		{
			throw new ExceptionProgram("Cookie could not be set.");
		}
		
		Session::$current_uid = $res[2];
		
		// Reset password reset token (it it not needed any more, if the user knows his password).
		$db->query("UPDATE user SET pwresettoken = '' WHERE id = '$res[2]';");
		
		// Update values prevloginime and logintime of the user
		$db->query("UPDATE user SET prevlogintime = '$res[4]' WHERE id = '$res[2]';");
		$db->query("UPDATE user SET logintime = CURRENT_TIMESTAMP WHERE id = '$res[2]';");
		
		// Finally...
		$this->cleanupExpiredSessions();
		
		
	}
	
	// Removes current sessionid from database and destroyes cookie.
	// Since the cookie is destroyed this function must be called BEFORE ANY HTML OUTPUT!
	// However, no exception is thrown if deleting the cookie fails.
	public function logout()
	{
		if (isset($_COOKIE['tippersession']))
		{
			$db = new Database();
			// Delete session from db.
			// This query also deletes expired sessions
			$sid = $_COOKIE['tippersession'];
			$db->query("DELETE FROM sessions WHERE sessionid = '$sid' OR validstamp < NOW();");
			// Delete cookie
			setcookie("tippersession", "");
			Session::$current_uid = -1;
		}
	}
	
	// This function can be used to identifiy the user of the current session after login.
	// It also resets the session timer to 2 hours.
	// \returns The user id of the current session or null if the user could not be logged in.
	public function getCurrentUserId()
	{
	
	
		if (Session::$current_uid != -1)
		{
			return Session::$current_uid;
		}
			
		if (!isset($_COOKIE["tippersession"]))
		{
			return null;
		}
		$current_sid = $_COOKIE["tippersession"];
		
		$db = new Database();
		$res = $db->queryResult("SELECT userid FROM sessions WHERE sessionid = '$current_sid' AND validstamp > NOW();");
		if ($res == null)
		{
			return null;
		}
		$sessiontime = MAX_SESSION_TIME;
		$db->query("UPDATE sessions SET validstamp =  CURRENT_TIMESTAMP + INTERVAL $sessiontime MINUTE WHERE sessionid = '$current_sid';");
			
		$current_uid = $res;
		return $res;
	}
	
	// This function can be used to identifiy the user of the current session after login.
	// It also resets the session timer to 2 hours.
	// \returns The user id of the current session or null if the user could not be logged in.
	public function getCurrentUser()
	{
		$userid = $this->getCurrentUserId();
		if ($userid == null)
		{
			return null;
		}
	
		$user = new User();
		$db = new Database();
		$res = $db->queryRow("SELECT name, email, adminlevel, wettbewerb, attr1, attr2, attr3, logintime, prevlogintime FROM user WHERE id = '$userid';");
		$user->name = $res[0];
		$user->email = $res[1];
		$user->adminlevel = $res[2];
		$user->wettbewerb = $res[3];
		$user->attr1 = $res[4];
		$user->attr2 = $res[5];
		$user->attr3 = $res[6];
		$user->logintime = $res[7];
		$user->prevlogintime = $res[8];
		return $user;
	}
	
	   public function getCurrentUserLogin()
	   {
			   $userid = $this->getCurrentUserId();
			   if ($userid == null)
			   {
					   return null;
			   }
			   $db = new Database();
			   return $res = $db->queryResult("SELECT login FROM user WHERE id = '$userid';");
	   }

	public function getUser($userid)
	{
		
		if ($userid == null)
		{
			return null;
		}
	
		$user = new User();
		$db = new Database();
		$res = $db->queryRow("SELECT name, email, adminlevel, wettbewerb, attr1, attr2, attr3, logintime, prevlogintime FROM user WHERE id = '$userid';");
		$user->name = $res[0];
		$user->email = $res[1];
		$user->adminlevel = $res[2];
		$user->wettbewerb = $res[3];
		$user->attr1 = $res[4];
		$user->attr2 = $res[5];
		$user->attr3 = $res[6];
		$user->logintime = $res[7];
		$user->prevlogintime = $res[8];
		return $user;
	}
	
	public static function getUserIdByLoginAndEmail($login, $email)
	{
		$user = new User();
		$db = new Database();
		$res = $db->queryRow("SELECT id FROM user WHERE login = '$login' and email = '$email';");
		
		if ($res == null)
		{
			return null;
		}
		
		return $res[0];
	}
	
	public static function setPasswordResetToken($userid, $token)
	{
		$db = new Database();
		$db->query("UPDATE user SET pwresettoken = '$token' WHERE id = '$userid';");
	}
	
	private function cleanupExpiredSessions()
	{
		$db = new Database();
		$db->query("DELETE FROM sessions WHERE validstamp < NOW();");
	}
	
	private static $current_uid = -1;


	public function showNoAccessPage()
	{
		echo "<h1>Fehler</h1>";

		echo "<p>Du bist zur Zeit nicht eingeloggt und hast keinen Zugriff auf diese Seite.</p>";
		echo "<p><a href='index.php'>Zur&uuml;ck zur Startseite</a>.</p>";

	}
}
?>
