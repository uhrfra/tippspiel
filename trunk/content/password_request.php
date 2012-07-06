<?php
	include_once("../classes/Session.php");
	$session = new Session();
	$session->logout();

	include_once("../config/config.php");
	include("../layout/pre_content_stuff.php");
	
	
	if ($_POST['aktion'] == "requested")
	{
	    requested();
	}
	else
	{
		request();
	}
	
	include("../layout/post_content_stuff.php");
	
	

function request()
{
	echo "<h1> Passwort zurücksetzen </h1>";
	echo "<p>";
	echo "Falls Du Dein Passwort vergessen hast, kannst Du ein neues Passwort anforden. Dazu musst Du Dein Login und die E-Mail-Adresse, mit der Du Dich angemeldet hast, angeben.";
	echo "<form  id='Form' action='password_request.php' method='post'>";
	echo "<table>";
	echo "<tr><td> Login: </td>";
	echo "<td> <input name='login' type='text' size='10' maxlength='30'></td></tr>";
	echo "<tr><td> E-Mail: </td>";
	echo "<td> <input name='email' type='text' size='10' maxlength='50'></td></tr>";
	echo "</table>";
	echo "<input id='Button' type='submit' name='submit' value='NEUES PASSWORT ANFORDERN'>";
	echo "<input type = 'hidden' name = 'aktion' value = 'requested'>";
	echo "</form>";
	echo "</p>";
}

function requested()
{
	$u = Session::getUserIdByLoginAndEmail($_POST['login'], $_POST['email']);
	if ($u == null)
	{
		echo "Feher: Login oder E-Mail konnte nicht gefunden werden.";
		return;
	}
	
	$usr = Session::getUser($u);
	
	mt_srand((double) microtime()*1000000);
	$token = md5(str_replace(".","",$REMOTE_ADDR) + mt_rand(100000, 999999));
	
	$link=PASSWORD_RESET_URL."?token=".$token;
	
	$mailtext = "Hallo,
	
Du hast beim Tippspiel eine Passwortänderung beantragt.
Bitte rufe zur Passwortänderung folgende Seite auf:
$link
Dort kannst Du Dir ein neues Passwort vergeben.

Grüße vom Tipper-Team!



Diese Mail wurde automatisch generiert. Bitte antworte deshalb nicht
auf diese Mail.";
	
	if (mail($usr->email, "Tippspiel Passwortänderung", $mailtext,"from:passwortaenderung@kontextfreitippspiel.de") == false)
	{
		echo "Fehler beim Mailversand.";
		return;
	}
	Session::setPasswordResetToken($u, $token);
	echo "Es wurde eine E-Mail an die Adresse ".$usr->email." versandt.";
	echo "In dieser ist ein Link zum Ändern Deines Passwortes angegeben.";
	
}
?>
