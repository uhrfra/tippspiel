<?php
	include_once("../classes/Session.php");
	$session = new Session();
	$session->logout();

	include("../layout/pre_content_stuff.php");
	
	if ($_GET['token'] != null)
	{
		if ($_POST['aktion'] == "resetted_pw")
		{
			resetted_pw();
		}
		else
		{
			reset_pw("Im folgenden Formular kannst Du Dir ein neues Passwort vergeben.");
		}
	}
	
	
	include("../layout/post_content_stuff.php");
	
function reset_pw($message)
{
	echo "<h1> Neues Passwort vergeben </h1>";
	echo "<p>";
	echo $message;
	echo "<form  id='Form' action='password_reset.php?token=".$_GET['token']."' method='post'>";
	echo "<table>";
	echo "<tr><td> Login: </td>";
	echo "<td> <input name='login' type='text' size='10' maxlength='10'></td></tr>";
	echo "<tr><td> Neues Passwort: </td>";
	echo "<td> <input name='new_pw' type='password' size='10' maxlength='32'></td></tr>";
	echo "<tr><td> Passwort wiederholden: </td>";
	echo "<td> <input name='new_pw_confirm' type='password' size='10' maxlength='32'></td></tr>";
	echo "</table>";
	echo "<input id='Button' type='submit' name='submit' value='NEUES PASSWORT SETZEN'>";
	echo "<input type = 'hidden' name = 'aktion' value = 'resetted_pw'>";
	echo "</form>";
	echo "</p>";
}


function resetted_pw()
{
	try
	{
		Session::resetUserPassword($_POST['login'], $_GET['token'], $_POST['new_pw'], $_POST['new_pw_confirm']);
	}
	catch(ExceptionInvalidUser $e)
	{
		echo reset_pw($e->getMessage());
		return;
	}
	catch(ExceptionSession $e)
	{
		echo "Entschuldigung, es ist ein Fehler aufgetreten. Bitte fordere erneut eine Passwortänderung an.";
		return;
	}
	echo "Alles klar, Dein Passwort wurde erfolgreich geändert.";
}
?>