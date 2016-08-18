<!DOCTYPE html>
<?php
@session_start();
/*if($_SERVER["HTTPS"] != "on") {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
   exit();
}*/
?>

<html lang="en" data-ng-app="_raiffisenApp" winresize>
<head>
    <meta charset="UTF-8">
    <title>Otkupna aplikacija</title>
    <link rel="shortcut icon" href="public/img/logo_raiffaisen.ico" />
    <!-- Bootstrap CSS Modules -->
    <?php foreach($this->css as $key => $value):?>
    <link rel="stylesheet" type="text/css" href="<?=URL.$value?>">
    <?php endforeach;?>
    <script type="text/javascript">
        window.session_info = <?php echo json_encode($_SESSION);?>
    </script>

</head>
<body data-ng-controller="mainController as mc" >
<!--PRINT AREA-->
<div class="print_area">
    Hello print!
</div>
<!-- /PRINT AREA -->
<!-- Display Setup Panel -->
<div style="">

</div><!-- /Display Setup Panel -->
<!-- Main Wrapper -->
<div class="mainWrapper cf" style="overflow-x: hidden">
    <!-- Header -->
    <header class="appheader" data-ng-class="{'fixedh':headerFixed===true}" >
        <div id="logo-group">
            <span></span>
            <img src="public/img/images/raiffeisen_logo.png"   />
        </div>
        <div id="login-group cf" style="float:left;margin-top:10px">
            <div style="background:#6B7652;float:left;color:#FFF;border-radius:3px;padding:1px 4px;float:left;margin-right:4px">
                <i class="fa fa-user" style="font-size:23px"></i>
            </div>
            <div style="float:left;font-size:12px;line-height:1.21;color:#6B7652">
                <b><?=Session::get('user_name')?></b><br />
                <?=Session::get('role')?>
            </div>

        </div>
        <div id="edit-group">
            <span></span>
            <a href="<?=URL.'login/logout'?>"><i class="fa fa-sign-out fa-lg fa-border icon-grey" title="Odjava sa aplikacije"></i></a>
            <i class="fa fa-cog fa-lg fa-border icon-grey" title="Podešavanje teme"></i>
        </div>
    </header><!-- /Header -->
    <!-- Aside -->
    <div data-ng-class="{'aopen':menuOpen===true , 'aclose':menuOpen===false, 'fixedh':headerFixed===true, 'fixed':headerFixed===true}" class="aside"  >
        <!--<span style="font-family:'Open Sans', Helvetica, Arial, sans-serif;font-weight: 800;-webkit-text-stroke: 1px #6B7652;-webkit-text-fill-color: #FFF;text-shadow: 0px 0px 1px #6B7652 ;color:#fff;font-size:18px;text-rendering: optimizeLegibility;">Raiffeisen Agro</span>-->
        <ul data-ng-class="{'menu_b':menuOpen===true , 'menu_a':menuOpen===false}">
            <li><a href="#/dashboard" data-ng-class="menuClass('')"><i class="fa fa-home fa-lg"></i> <span>Dashboard</span></a></li>
            <li>
                <a href=""  data-ng-disabled="true" data-ng-class="menuClass(['pregled_prijema/pregled_prijema_merkantila','pregled_prijema/pregled_prijema_repromaterijal'])"><i class="fa fa-download fa-lg" ></i> <span>Ulaz robe</span></a>
                <ul class="submenu">
                    <li><a href="#/pregled_prijema/pregled_prijema_merkantila"><span>Merkantila</span></a></li>
                    <li><a href="#/pregled_prijema/pregled_prijema_repromaterijal"><span>Repromaterijal</span></a></li>
                </ul>
            </li>
            <li>
                <a href=""  data-ng-class="menuClass(['pregled_otpreme/pregled_otpreme_merkantila','pregled_otpreme/pregled_otpreme_repromaterijal'])"><i class="fa fa-upload fa-lg"></i> <span>Izlaz Robe</span></a>
                <ul class="submenu">
                    <li><a href="#/pregled_otpreme/pregled_otpreme_merkantila"><span>Merkantila</span></a></li>
                    <li><a href="#/pregled_otpreme/pregled_otpreme_repromaterijal"><span>Repromaterijal</span></a></li>
                </ul>
            </li>
            <li><a href="#/rezervacije" ng-class="menuClass('rezervacije')"><i class="fa fa-registered fa-lg"></i> <span>Rezervacija</span></a></li>
            <li>
                <a href="" data-ng-disabled="true" ng-class="menuClass(['stanja_magacina/merkantila','stanja_magacina/repromaterijal'])"><i class="fa fa fa-pie-chart fa-lg"></i> <span>Stanja magacina</span></a>
                <ul class="submenu">
                    <li><a href="#/stanja_magacina/merkantila"><span>Stanje Merkantila</span></a></li>
                    <li><a href="#/stanja_magacina/repromaterijal"><span>Stanje Repromaterijal</span></a></li>
                </ul>
            </li>

            <li class="close_open" ><a href="javascript:void(0)" class="close" ng-disabled="true" ng-click="openMenu();"><i ng-class="{'fa-arrow-circle-left':menuOpen===true, 'fa-arrow-circle-right':menuOpen===false}" class="fa  hit fa-lg"></i></a></li>
        </ul>
    </div><!-- /Aside -->
    <!-- Bside -->
    <div data-ng-class="{'bopen':menuOpen===true , 'bclose':menuOpen===false, 'fixedh':headerFixed===true}" class="bside"  >
        <div data-ng-view style="width:100%" class="slide-animation">

        </div>
    </div><!-- Bside -->
</div><!-- /Main Wrapper -->
</body>

<!-- jQuery JS Modules -->
<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js" ></script>
<script type="text/javascript" src="node_modules/jquery-ui/jquery-ui.min.js" ></script>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="node_modules/jquery-transform/jquery.transform2d.js"></script>
<script type="text/javascript" src="node_modules/underscore/underscore-min.js"></script>
<!-- Angular JS Modules -->
<?php foreach($this->angular_modules as $key => $value):?>
    <script type="text/javascript" src="<?=URL.$value?>"></script>
<?php endforeach;?>

<!-- Datatables JS Modules -->
<?php foreach($this->datatables_modules as $key => $value):?>
    <script type="text/javascript" src="<?=URL.$value?>"></script>
<?php endforeach;?>

<!-- App Module -->
<?php foreach($this->app_module as $key => $value):?>
    <script type="text/javascript" src="<?=URL.$value?>"></script>
<?php endforeach;?>

<!-- App Controller JS Modules -->
<?php foreach($this->controller_module as $key => $value):?>
    <script type="text/javascript" src="<?=URL.$value?>"></script>
<?php endforeach;?>


<!-- App Directives JS Modules -->
<?php foreach($this->directives_module as $key => $value):?>
    <script type="text/javascript" src="<?=URL.$value?>"></script>
<?php endforeach;?>

<!-- App Service JS Modules -->
<?php foreach($this->service_module as $key => $value):?>
    <script type="text/javascript" src="<?=URL.$value?>"></script>
<?php endforeach;?>

<!-- App Filters JS Modules -->
<?php foreach($this->filters_module as $key => $value):?>
    <script type="text/javascript" src="<?=URL.$value?>"></script>
<?php endforeach;?>


</html>