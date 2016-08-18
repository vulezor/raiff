<!doctype html>
<?php
/*if($_SERVER["HTTPS"] != "on") {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
   exit();
}*/
?>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Document</title>
    <?php foreach($this->css as $key=>$value): ?>
        <link rel="stylesheet" href="<?=URL.$value?>" type="text/css"/>
    <?php endforeach; ?>
    <style>

        html{
            height:100%
        }

        body{
            font-family:arial;
            color:#666666;
            height:100%;
            margin:0px;
            /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#abaf80+1,4e5940+100 */
            background: #c6c3c2; /* Old browsers */
            background: -moz-radial-gradient(center, ellipse cover, #c6c3c2 1%, #9c9998 100%); /* FF3.6+ */
            background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(1%, #c6c3c2), color-stop(100%, #9c9998)); /* Chrome,Safari4+ */
            background: -webkit-radial-gradient(center, ellipse cover, #c6c3c2 1%, #9c9998 100%); /* Chrome10+,Safari5.1+ */
            background: -o-radial-gradient(center, ellipse cover, #c6c3c2 1%, #9c9998 100%); /* Opera 12+ */
            background: -ms-radial-gradient(center, ellipse cover, #c6c3c2 1%, #9c9998 100%); /* IE10+ */
            background: radial-gradient(ellipse at center, #c6c3c2 1%, #9c9998 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#abaf80', endColorstr='#4e5940',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
        }

        .cf:before,
        .cf:after {
            content: " ";
            display: table;
        }

        .cf:after {
            clear: both;
        }


        .cf {
            *zoom: 1;
        }
        .login_wrapper{
            border:1px solid #999;
            position:absolute;
            left:50%;top:50%;
            width:0px;/*420px*/
            margin-left:0px;/*-220px*/
            padding:15px 15px 0px 15px;
            margin-top:0px;/*-82px*/
            background:#fff;
            height:0px;/*163px*/
            opacity:0;
        }
        .set_overflow{
            overflow:hidden;
        }

        .full_left{
            margin-left:-100%;
            opacity:0
        }

        .full_right{
            margin-left:100%;
            opacity:0
        }

        .login_header{
            background-color:#829aa8;;
            font-size:20px;
            color:#fff;
            margin-bottom:20px;
            padding-left:5px
        }
        .login_container{
            border:1px solid  #768995;
            -webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
            -moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
            box-shadow: 0px 0px 0px 0px rgba(0,0,0,0.75);
            border-radius:0px;
            max-width:500px;
            margin:0px auto;
            position:relative;
            top:50%;
            background:#FFFFFF;

            margin-top:-100px;
        }
        .login_body{
            margin:0px 20px 20px 0px;

        }
        .login_header{
            border-bottom:1px solid #999
        }


        .login_side{
            float:left;
            position:relative;
            width:64%;
            padding:10px;
            background:#F2F2F2;
            height:100%;
            border-radius:4px 0px 0px 4px;
            border-right:1px solid #FFF;
            height:100%;
            padding-bottom:22px;
        }

        .logo_side{
            width:36%;
            position:relative;
            border-left:1px solid #ccc;
            max-height:100%;
            height:100%;
            min-height:100%;
            float:left;
        }

        .loginform{
            width:100%
        }
        .btn-login-color{
            background-color:#829aa8;
            border:1px solid  #768995;
        }
        .loginform>div{
            float:left;
            line-height:42px;
            font-size: 14px;
            margin-top:10px
        }
        .loginform>div input[type="text"], .loginform>div input[type="password"]{
            border-radius:3px;
            margin:0px;
            border:none;
            outline:none;
            padding:7px 20px;
            font-size:14px;
            font-weight:normal;
            width:100%;
        }
        .loginform>div:nth-child(1){
            width:35%
        }
        .loginform>div:nth-child(2){
            width:65%
        }
        /*label {
            border-radius:3px;
            display:inline-block;
            margin:0px;padding:0px;

            line-height:normal;
            vertical-align:middle;
            width:100%;

        }*/
        .login{
            background:#AAAAAA !important;
            border:1px solid #8C8C8C !important;
            width:100px;
            text-shadow: 0px 1px #525252;
        }
        .login:active{
            background:#AAAAAA !important;
            border:1px solid #8C8C8C !important;
        }



    </style>
</head>
<body>

    <!--<div class="login_container">
        <div class="login_header">Login</div>
        <form class="form-horizontal" role="form" action="<?=URL?>login/login" method="post">
            <div class="login_body">
                <section class="form-group">
                        <label class="col-sm-5 control-label" for="username">Username:</label>
                        <div class="col-sm-7"><input type="text" id="username" name="username" class="form-control"  /></div>
                </section>

                <section class="form-group">
                    <label class="col-sm-5 control-label" for="password">Password:</label>
                    <div class="col-sm-7"><input type="password" id="password" name="password" class="form-control" /></div>
                </section>
                <section class="form-group">
                    <div class="col-sm-7 col-sm-offset-5 ">
                        <button type="submit" class="btn btn-primary btn-sm btn-login-color pull-right" >Login</button>
                    </div>
                </section>
            </div>
        </form>
    </div>-->
    <!-- LOGIN FORM-->
    <div style="background:#EEEEEE;position:absolute;width:100%;min-height:100%;z-index:1000">
        <div class="login_wrapper smart-form">
            <form  id="login_form" class="form-horizontal" method="post" name="login_form" role="form" action="<?=URL?>login/login" method="post" >
                <div class="row">
                    <section class="col col-sm-4 set_overflow" style="position:relative" >
                        <label class="full_right">
                            <img src="public/img/raiffeisen_blogo.png" width="105" style="margin:0 auto;display:block" >
                        </label>
                    </section>
                    <div class="col col-sm-8">
                        <div class="row">
                            <section class="col col-sm-12 fixsection set_overflow" >
                                <label class="input full_left" for="username">
                                    <i class="icon-prepend fa fa-user"></i>
                                    <input type="text" name="username" placeholder="Username"  id="username"/>
                                </label>
                            </section>
                        </div>
                        <div class="row">
                            <section class="col col-sm-12 fixsection set_overflow" >
                                <label class="input full_left" for="password">
                                    <i class="icon-prepend fa fa-key"></i>
                                    <input type="password" name="password" placeholder="password"  id="password"/>
                                </label>
                            </section>
                        </div>
                        <div class="row">
                            <section class="col-sm-offset-6 col col-sm-6 fixsection set_overflow">
                                <label class="input full_left">
                                    <button type="submit" class="btn btn-sm btn-primary pull-left" id="unos_robe" style="margin:0px;" >Potvrdi</button>
                                </label>
                            </section>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--/LOGIN FORM END-->
    <script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js" ></script>
    <script type="text/javascript" src="node_modules/jquery-ui/jquery-ui.min.js" ></script>
    <script type="text/javascript" src="node_modules/jquery-transform/jquery.transform2d.js"></script>
<script>
    $(".login_wrapper").animate({'width':'420px','margin-left':'-220px','margin-top':'-82px','height':'163px','opacity':1}, 400, 'easeOutCubic');
    $("#login_form section label").delay("400").animate({'margin-left':'0px','opacity':1}, 400, 'easeOutCubic');
</script>
</body>
</html>
