<?php
	// No content in here. This is just a redirection to the main page.
	
	include_once("../classes/Session.php");

	$session = new Session();
	try
	{
		$session->login($_POST['login'], $_POST['passwort']);
	}
	catch(ExceptionInvalidUser $e)
	{
		include ("../layout/pre_content_stuff.php");
		echo "<h1>Fehler</h1>";
		echo "Login fehlgeschlagen: ", $e->getMessage(), "\n";
		echo "<a id='link' href='index.php'> Zur&uumlck zur Startseite </a>";
		include ("../layout/post_content_stuff.php");
		exit;
	}
	catch(Exception $e)
	{
		include ("../layout/pre_content_stuff.php");
		echo "<h1>Fehler</h1>";
		echo "Fehler:", $e->getMessage(), "\n";
		echo "<a id='link' href='index.php'> Zur&uumlck zur Startseite </a>";
		include ("../layout/post_content_stuff.php");
		exit;
	}
    
	include "main.php";
?>


