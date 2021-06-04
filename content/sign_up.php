<?php

	include_once("../classes/Session.php");
	include_once("../classes/Matches.php");
	include_once("../classes/GUIBuilder.php");
	include_once("../classes/Game.php");

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
  echo "Hier kannst Du Dich zum Tippspiel anmelden:";
  echo "</p>";


  echo "<form id='Form' action='sign_up.php' method='post'>";
  echo " <table id='Form'>";

  echo " <tr>";
  echo "  <td> Login: </td>";
  echo "  <td> <input name='login' type='text' size='10' maxlength='30'></td>";
  echo "<td></td>";
  echo " </tr>";
  
  echo " <tr>";
  echo "  <td> Passwort: </td>";
  echo "  <td> <input name='passwort' type='password' size='20' maxlength='32'></td>";
  echo "<td></td>";
  echo " </tr>";
  
  echo " <tr>";
  echo "  <td> Passwort wiederholen: </td>";
  echo "  <td> <input name='passwort_confirm' type='password' size='20' maxlength='32'></td>";
  echo "<td></td>";
  echo " </tr>";

  echo " <tr>";
  echo "  <td> Name: </td>";
  echo "  <td> <input name='name' type='text' size='20' maxlength='50'></td><td>(Bitte Vor- und Nachname)</td>";
  echo " </tr>";

  echo " <tr>";
  echo "  <td> E-Mail: </td>";
  echo "  <td> <input name='email' type='text' size='20' maxlength='50'></td>";
  echo "<td></td>";
  echo " </tr>";
  
  $m = new Matches();
  if (!$m->started())
  {
	  echo "<tr><td>Meistertipp:</td><td>";
	  GUIBuilder::buildDropdownSelect('champtip', 'SELECT land, id FROM laender ORDER BY land;', 0);
	  echo "</td><td>(Der Meistertipp kann noch bis zum Beginn des ersten Spiels ge&auml;ndert werden.)</td></tr>";
  
  }
  echo "</table>";
  echo "<p>";
  echo "<input id='Button' type='submit' name='submit' value='ANMELDEN''>";
  echo "<input type = 'hidden' name = 'aktion' value = 'sign_up'>";
  echo "</p>";
  echo "</form>";	

} // end function show_form()

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

		$uid = Session::createUser($_POST["login"], $_POST["passwort"], $_POST["passwort_confirm"], $u);
	
		$m = new Matches();
		if (!$m->started())
		{
			Game::setChamptip($uid, $_POST['champtip']);
		}

		echo "Du bist angemeldet, herzlich willkommen beim Tippspiel. Viel Gl&uuml;ck beim Tippen!";
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
