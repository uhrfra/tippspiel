<?php
    include_once("../classes/Session.php");
    include_once("../classes/Matches.php");
    include("../config/config.php");

    $session = new Session();
    $user = $session->getCurrentUser();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Euro-Tipper 2021</title>
    <link href="../layout/css/bootstrap.min.css" rel="stylesheet">
    <link href="../layout/css/prettyPhoto.css" rel="stylesheet">
    <link href="../layout/css/animate.css" rel="stylesheet">
    <link href="../layout/css/main.css" rel="stylesheet">
    <!--[if lt IE 9]>
        <script src="../layout/js/html5shiv.js"></script>
        <script src="../layout/js/respond.min.js"></script>
    <![endif]-->       
    <link rel="shortcut icon" href="../layout/images/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../layout/images/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../layout/images/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../layout/images/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../layout/images/ico/apple-touch-icon-57-precomposed.png">    
    <link rel="stylesheet" href="../layout/css/supersized.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="../layout/theme/supersized.shutter.css" type="text/css" media="screen" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
    <script type="text/javascript" src="../layout/js/supersized.3.2.7.min.js"></script>
    <script type="text/javascript" src="../layout/theme/supersized.shutter.min.js"></script>
    <script type="text/javascript">
        jQuery(function($){
            $.supersized({
                // Functionality
                slideshow         : 1, // Slideshow on/off
                autoplay          : 1, // Slideshow starts playing automatically
                start_slide       : 0, // Start slide (0 is random)
                stop_loop         : 0, // Pauses slideshow on last slide
                random            : 0, // Randomize slide order (Ignores start slide)
                slide_interval    : 9500, // Length between transitions
                transition        : 1, // 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
                transition_speed  : 3000, // Speed of transition
                new_window        : 0, // Image links open in new window/tab
                pause_hover       : 0, // Pause slideshow on hover
                keyboard_nav      : 0, // Keyboard navigation on/off
                performance       : 1, // 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)
                image_protect     : 1, // Disables image dragging and right click with Javascript

                // Size & Position									min_width		        :   0,	// Min width allowed (in pixels)
                min_height        : 0, // Min height allowed (in pixels)
                vertical_center   : 1, // Vertically center background
                horizontal_center : 1, // Horizontally center background
                fit_always        : 0, // Image will never exceed browser width or height (Ignores min. dimensions)
                fit_portrait      : 1, // Portrait images will not exceed browser height
                fit_landscape     : 0, // Landscape images will not exceed browser width

                // Components					
                slide_links          : 'false', // Individual links for each slide (Options: false, 'num', 'name', 'blank')
                thumb_links          : 0, // Individual thumb links for each slide
                thumbnail_navigation : 0, // Thumbnail navigation
                slides               : // Slideshow Images
                [
                    {image : '../layout/images/background.jpg'}
                ],

                // Theme Options
                progress_bar : 0, // Timer for each slide
                mouse_scrub  : 0
            });
        });
    </script>
</head><!--/head-->

<body>

<?php
    include("navi.php");
?>

    <div class="container shadow main">
        <div class="container">
