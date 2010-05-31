<?php
	include_once("../classes/Session.php");
	$session = new Session();
	$session->logout();

	include("../layout/pre_content_stuff.php");
?>

<h1> Willkommen...   </h1>
<p>
...beim Tippspiel.<br><br>
</p>
<p>
Wenn Du schon ein registrierter Benutzer bist, kannst Du Dich hier einloggen:

<form  id='Form' action='sign_in.php' method='post'>
<table>
<tr><td> Login: </t>
<td> <input name='login' type='text' size='10' maxlength='32'></td></tr>
<tr><td> Passwort: </td>
<td> <input name='passwort' type='password' size='10' maxlength='32'></td></tr>
</table>
<input id='Button' type='submit' name='submit' value='LOGIN'>
</form>
</p>
<br><br>

<p>
Falls Du noch nicht angemeldet bist, wird es höchste Zeit! <a
href='../content/sign_up.php5'>Hier</a> gehts zur Anmeldung. <br> 
Vorher kannst Du <a href='../content/rules.php5?svr=1'>hier</a> die 
<a href='../content/rules.php5?svr=1'>Spielregeln</a> lesen.
<br><br>
 Viel Spaß bei Tippen!
 
 
</p>
<?php
	include("../layout/post_content_stuff.php");
?>
