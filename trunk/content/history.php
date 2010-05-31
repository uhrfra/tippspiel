<?php
        include_once("../classes/Session.php");
	include_once("../classes/GUIBuilder.php");

	include"../layout/pre_content_stuff.php";

	$session = new Session();
	$user = $session->getCurrentUser();
	$userid = $session->getCurrentUserId();

	if ($userid == null) {
		GUIBuilder::showNoAccessPage();
		include ("../layout/post_content_stuff.php");
		exit();
	}

?>

<h1> Historie </h1>

<?php
include ("../layout/post_content_stuff.php"); 
?>
