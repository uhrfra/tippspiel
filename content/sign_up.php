<?php

	include_once("../classes/Session.php");

	include ("../layout/pre_content_stuff.php");

	if ($_POST['aktion'] == "sign_up")
	{
	    sign_up();
	}
	else
	{
		show_form();
	}

function show_form()
{
  echo "<h1>Neu Anmelden</h1>";
  echo "<p>";
  echo "Um am Tippspiel teilzunehmen, muss folgendes Formular ausgefüllt werden:";
  echo "</p>";


  echo "<form id='Form' action='sign_up.php' method='post'>";
  echo " <table>";

  echo " <tr>";
  echo "  <td> Login: </td>";
  echo "  <td> <input name='login' type='text' size='10' maxlength='10'></td>";
  echo " </tr>";
  
  echo " <tr>";
  echo "  <td> Passwort: </td>";
  echo "  <td> <input name='passwort' type='password' size='10' maxlength='32'></td>";
  echo " </tr>";

  echo " <tr>";
  echo "  <td> Name: </td>";
  echo "  <td> <input name='name' type='text' size='20' maxlength='30'></td><td>(Bitte Vor- und Nachname)</td>";
  echo " </tr>";

  echo " <tr>";
  echo "  <td> E-Mail: </td>";
  echo "  <td> <input name='email' type='text' size='20' maxlength='30'></td>";
  echo " </tr>";
  echo "</table>";
  echo "<p>";
  echo "<input id='Button' type='submit' name='submit' value='ANMELDEN''>";
  echo "<input type = 'hidden' name = 'aktion' value = 'sign_up'>";
  echo "</p>";
  echo "</form>";

} // end function formular()

function sign_up()
{
	try
	{
		$u = new User();
		$u->name = $_POST["name"];
		$u->email = $_POST["email"];
		$u->adminlevel = 0;
		$u->wettbewerb = 0;
		$u->attr1 = 0;
		$u->attr2 = 0;

		Session::createUser($_POST["login"], $_POST["passwort"], $u);
	

		echo "Du bist angemeldet, herzlich willkommen beim Tippspiel. Viel Glück beim Tippen!";
		echo "<form action='index.php' method='post'>";
		echo "<input type='submit' name='submit' value='JETZT EINLOGGEN''>";
		echo "</form>";

	}
	catch(ExceptionInvalidUser $e)
	{
		echo "Falsche Eingabe: ", $e->getMessage(), "\n";
		echo "<form action='sign_up.php' method='post'>";
	  	echo "<input type='submit' name='submit' value='NEU ANMELDEN''>";
	 	echo "<input type = 'hidden' name = 'aktion' value = 'show form'>";
	 	echo "</form>";
	}
	catch(Exception $e)
	{
		echo "Fehler:", $e->getMessage(), "\n";
		echo "<form action='sign_up.php' method='post'>";
	  	echo "<input type='submit' name='submit' value='NEU ANMELDEN''>";
	 	echo "<input type = 'hidden' name = 'aktion' value = 'show form'>";
	 	echo "</form>";
	}
}

include ("../layout/post_content_stuff.php");

?>
