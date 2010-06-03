<?php 
	include_once("../classes/Session.php");
	include_once("../classes/Matches.php");
	include ("../config/config.php");

	$session = new Session();
	$user = $session->getCurrentUser();
?>


<html>

<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<title>Tippspiel</title>
<link rel="stylesheet" type="text/css" href="../layout/stylesheet.css">
</head>

<body>
<div id="Titel"></div>
<div id="Seite">


<?php
  include("../layout/navi.php");
?>

<div id="Inhalt">


