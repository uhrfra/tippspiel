<?php
    $active_main = "";
    $active_user = "";
    $active_tables = "";
    $active_admin = "";
    $active_news = "";
    $active_rules = "";

    if(strpos($uri, "main"))
    {
        $active_main = "active";
    }

    $active_tables = "";
    if(strpos($uri, "view_matches") || strpos($uri, "profile"))
    {
        $active_user = "active";
    }

    $active_tables = "";
    if(strpos($uri, "highscore") || strpos($uri, "champion_tipps") || strpos($uri, "history"))
    {
        $active_tables = "active";
    }

    $active_admin = "";
    if(strpos($uri, "admin_"))
    {
        $active_admin = "active";
    }

    $active_news = "";
    if(strpos($uri, "newsboard"))
    {
        $active_news = "active";
    }

    $active_rules = "";
    if(strpos($uri, "rules"))
    {
        $active_rules = "active";
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
                <a class="navbar-brand" style="margin-bottom:-10px;" href="main.php"><img src="../layout/images/logo.png" alt=""></a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class= <?php echo "'$active_main'"; ?> ><a href="main.php">&Uuml;bersicht</a></li>
                    <li class= <?php echo "'dropdown $active_user'"; ?> >
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Deine Daten <img src="../layout/images/dropdown.png" alt=""></a>
                        <ul class= "dropdown-menu" >
                            <li><a href="view_matches.php?show=0">Ausstehende Tipps</a></li>
                            <li><a href="view_matches.php?show=2">Abgelaufene Tipps</a></li>
                            <li><a href="profile.php">Profil</a></li>
                        </ul>
                    </li>
                    <li class= <?php echo "'dropdown $active_tables'"; ?> >
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tabellen <img src="../layout/images/dropdown.png" alt=""></a>
                        <ul class="dropdown-menu">
                            <li><a href="highscore.php">Tabelle</a></li>
                            <li><a href="champion_tipps.php">Meistertipps</a></li>
                            <li><a href="user_comparison.php">Direkter Vergleich</a></li>
                            <li><a href="history.php">Historie  </a></li>
                            <li><a href="alltime_highscore.php">Ewige Tabelle  </a></li>
                        </ul>
                    </li>
<?php
    if ($user->adminlevel > 0) {
?>
                    <li class= <?php echo "'dropdown $active_admin'"; ?> >
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administration <img src="../layout/images/dropdown.png" alt=""></a>
                        <ul class="dropdown-menu">
                            <li><a href="admin_matches.php">Spiele</a></li>
<?php
    if ($user->adminlevel == 2) {
?>
                            <li><a href="admin_users.php">Benutzer</a></li>
<?php
    }
?>
                        </ul>
                    </li>
<?php
    }
?>
                    <li class= <?php echo "'$active_news'"; ?> ><a href="newsboard.php">Newsboard</a></li>
                    <li class= <?php echo "'$active_rules'"; ?> ><a href="rules.php">Regeln</a></li>
                    <li><a id="link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </header><!--/header-->

