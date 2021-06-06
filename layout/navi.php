<?php
    $uri = $_SERVER["REQUEST_URI"];
    $beg = strrpos($uri, "/") + 1;
    $end = strrpos($uri, ".php");
    $page = substr($uri, $beg, $end-$beg);

    if ($user == null)
    {
        include("navi_logged_out.php");
    }
    else
    {
        include("navi_logged_in.php");
    }
?>

