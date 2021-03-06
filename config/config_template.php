<?php
// Database
define("DB_HOST", "localhost");
define("DB_USER", "user");
define("DB_PASSWORD", "password");
define("DB_DBNAME", "dbname");

// Logfiles
define("LOGFILE_EXCEPTION", "../log/exceptions.log");
define("LOGFILE_DEBUG", "../log/debug.log"); // Comment this line out to disable debug logging.

// Startdate of the matches, format (DD.MM.YYYY, HH:MM)
define("GAMESTART", "07.06.2008, 18:00");

// Scores for the tipps
define("SCORE_RESULT", 4); // Result is tipped right.
define("SCORE_DIFF", 3); // Difference is tipped right.
define("SCORE_TENDENCY", 2); // Tendency is tipped right.
define("DRAW_IS_TENDENCY", 1); // Select 1 if a tip on draw should count as right tendency if match was draw but different as tip.  
define("SCORE_CHAMPTIP", 8);

//Session
define("MAX_SESSION_TIME", 100); // in minutes
define("TIMESHIFT", 0); // in seconds, this value is added to the system clock to compensate a timeshift

define("PASSWORD_RESET_URL", "http://tippspiel.kontextfrei.de/wm2010/content/password_reset.php");

define("TOTAL_MATCHES", 31);

//GUI text
define("TITLE", "Tippspiel");
define("TITLE_SECOND", "zur Europameisterschaft 2020");
?>
