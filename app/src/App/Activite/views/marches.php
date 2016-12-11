<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Metrix</title>

    <link rel="stylesheet" type="text/css" href="/lib/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/lib/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/css/app.css">
</head>
<body>

 <!-- side bar -->
<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="index.html" class="site_title"><i class="fa fa-dashboard"></i> <span>Tableau de bord</span></a>
    <div class="side_links">
        <li>
            <a>
                <i class="fa fa-eye"></i> Evolutions des marchés
            </a><br />

            <a href="http://smartapp.dev/activite/marches/20161001_20161031/jour/precipitations"> > Au jour + précipitations</a><br />

<br />

            <a href="http://smartapp.dev/activite/marches/20161001_20161031/jour/temperaturemax"> > Au jour + température max</a><br />
<br /><br />
            <a href="http://smartapp.dev/activite/marches/20150101_20150228/semaine"> > Soldes hiver 2015</a>
            <a href="http://smartapp.dev/activite/marches/20160101_20160228/semaine"> > Soldes hiver 2016</a>

<br /><br />

            <a href="http://smartapp.dev/activite/marches/20150603_20150830/semaine"> > Soldes été 2015</a>
            <a href="http://smartapp.dev/activite/marches/20160602_20160831/semaine"> > Soldes été 2016</a>

            <br /><br />
            <a href="http://smartapp.dev/activite/marches/20150201_20150216/jour">
            Saint-Valentin 2015
            </a>
            <a href="http://smartapp.dev/activite/marches/20160201_20160216/jour">
            Saint-Valentin 2016
            </a>
        </li>

    </div>
</div>

<!-- core de la page avec top navbar -->
<div id="main" style="margin-left: 250px">
    <ul class="nav navbar-nav name_custom">
        <span class="hamb" onclick="openNav()"><i class="fa fa-bars"></i></span>
        <li>
            <a href="#">Admin</a>
        </li>
    </ul>

    <!-- core de la page sous la navbar grise -->
    <!-- <div id="chart" style="width: 800px; height: 600px;"></div> -->
    <div class="content" style="padding-top: 100px;">
    <?php
    echo $chart->render('simple-custom-id');
    ?>
    </div>
</div>


<script type="text/javascript" src="/lib/js/jquery.js"></script>
<script type="text/javascript" src="/lib/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/js/app.js"></script>
<script>
</script>
</body>
</html>