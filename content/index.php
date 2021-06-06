<?php
	include_once("../classes/Session.php");
	$session = new Session();
	$session->logout();

	include("../layout/pre_content_stuff.php");
?>

    <section id="main-slider" class="no-margin">
        <div class="carousel slide wet-asphalt">
            <div class="carousel-inner">
                <div class="item active" style="background-image: url(../layout/images/bg_slider.jpg)">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="carousel-content" style="padding: 65px 0px 50px 35px;">
<h2> Willkommen...   </h2>
<h3>...zum <?php echo TITLE ?></h3><br>
</p>
<p>
Bereits registrierte Tipper k&ouml;nnen sich hier einloggen:

<form action='sign_in.php' method='post'>
<table id='Form'>
<tr><td> Login: </td>
<td> <input name='login' type='text' size='10' maxlength='30'></td>
<td></td></tr>
<tr><td> Passwort: </td>
<td> <input name='passwort' type='password' size='10' maxlength='32'></td>
<td><a id="link" style="font-size: 0.8em" href='../content/password_request.php'>Passwort vergessen?</a></td></tr>
</table>
<input id='Button' type='submit' name='submit' value='LOGIN'>
</form>
</p>

                        </div><!--/.col-->
                    </div><!--/.row-->
                </div><!--/.item-->
            </div><!--/.carousel-inner-->
        </div><!--/.carousel-->
    </section><!--/#main-slider-->

<div>
<p>
Falls Du noch nicht angemeldet bist, wird es h&ouml;chste Zeit! <a
id="link" href='../content/sign_up.php'>Hier</a> gehts zur Anmeldung.
</p>
<p>
Vorher kannst Du die <a id="link" href='../content/rules.php?svr=1'>Spielregeln</a> lesen.
</p>
<p>
 Viel Spa&szlig; beim Tippen!<br>
</p>
</div>

</p>
<?php
	include("../layout/post_content_stuff.php");
?>
