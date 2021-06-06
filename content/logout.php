<?php
	include_once("../classes/Session.php");
	$session = new Session();
	$session->logout();
	include ("../layout/pre_content_stuff.php"); 
?>

    <section id="main-slider" class="no-margin">
        <div class="carousel slide wet-asphalt">
            <div class="carousel-inner">
                <div class="item active" style="background-image: url(../layout/images/bg_slider.jpg)">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="carousel-content" style="padding: 65px 0px 50px 35px;">
<h2> Logout </h2>
<p>
Du bist ausgeloggt. Bis zum n&auml;chsten Mal!<br><br>
Zur&uuml;ck zur <a id="link" href='../content/index.php'>Startseite</a>.
</p>
                        </div><!--/.col-->
                    </div><!--/.row-->
                </div><!--/.item-->
            </div><!--/.carousel-inner-->
        </div><!--/.carousel-->
    </section><!--/#main-slider-->

<?php 
	include ("../layout/post_content_stuff.php"); 
?>
