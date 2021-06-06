<?php
    $active_index = "";
    if(strpos($uri, "index"))
    {
        $active_index = "active";
    }

    $active_rules = "";
    if(strpos($uri, "rules"))
    {
        $active_rules = "active";
    }

    $active_signup = "";
    if(strpos($uri, "sign_up"))
    {
        $active_signup = "active";
    }

?>

    <header class="navbar navbar-inverse navbar-fixed-top shadow topmenu" role="banner">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" style="margin-bottom:-10px;" href="index.php"><img src="../layout/images/logo.png" alt=""></a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class= <?php echo "'$active_index'"; ?> ><a href="index.php">Login</a></li>
                    <li class= <?php echo "'$active_rules'"; ?> ><a href="rules.php">Regeln</a></li>
                    <li class= <?php echo "'$active_signup'"; ?> ><a href="sign_up.php">Neu Anmelden</a></li>
                </ul>
            </div>
        </div>
    </header><!--/header-->

