<?php
	include_once("../classes/Session.php");
	$session = new Session();
	$session->logout();
	include ("../layout/pre_content_stuff.php"); 
?>


<h1> Logout </h1>

<p>
Du bist ausgeloggt. Bis zum n&auml;chsten Mal!<br><br>
Zur&uuml;ck zur <a href='../content/index.php'>Startseite</a>.
</p>

<?php 
	include ("../layout/post_content_stuff.php"); 
?>
