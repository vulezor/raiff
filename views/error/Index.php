<!DOCTYPE html PUBLIC>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html;charset=utf-8" http-equiv="content-type" />
	<title>Error Page :(</title>
    <link rel="shortcut icon" href="" />
    <!-- style -->
	<link rel="stylesheet" type="text/css" href="<?=URL?>views/error/gray.css" />

    <!-- script -->
    <script src="<?=URL?>views/error/js/jquery-1.2.6.min.js" type="text/javascript"></script>
    <script src="<?=URL?>views/error/js/jquery.jparallax.js" type="text/javascript" ></script>
    <script src="<?=URL?>views/error/js/script.js" type="text/javascript" ></script>
    <script src="<?=URL?>views/error/js/panel.js" type="text/javascript" ></script>
	<script src="<?=URL?>views/error/js/styleswitcher.js" type="text/javascript" ></script>



</head>
<body>


<div id="wrapper"><!--START:#wrapper-->
	<div id="center_wrap"><!--START:#center_wrap-->
		<!--<div id="logo"><a href="#"><img alt="Website Name" src="img/logo.png"/></a></div><!--:#logo-->
			<div id="menu"><!--START:#menu-->
			  <ul id="navigation">
			   <!--<li><a href="#">Home</a></li>
				<li><a href="#">About</a></li>
				<li><a href="#">Services</a></li>
				<li><a href="#">Blog</a></li>
				<li><a href="#pop-up" name="dialog">Contact</a></li> -->
			  </ul>

			  <div id="search"><!--START:#search-->
			   <form action="#" method="get">
				<fieldset>
					<!--<input class="search-input" type="text" title="Search" name="s" id="s" value="Search" onblur="if (this.value == ''){this.value = 'Search'; }" onfocus="if (this.value == 'Search') {this.value = '';}" /> -->
				</fieldset>
				</form>
			  </div><!--END:#search-->
			</div><!--END:#menu-->

		<div id="parallax" class="clear"> <!--START:#parallax -->
        	<div style="width: 1137px; height: 256px;">
                <img style="position:absolute; top:460px; left:23px; z-index:9;" alt="" src="<?=URL?>views/error/img/notfound.png"/>
            </div>
            <div style="width: 1020px; height: 309px;">
                <img style="position:absolute; top:140px; left:202px;" alt="" src="<?=URL?>views/error/img/404.png"/>
            </div>
            <div style="width: 1090px; height: 470px;">
                <img style="position:absolute; top:390px; left:355px;" alt="" src="<?=URL?>views/error/img/file.png"/>
            </div>
			<div style="width: 1900px; height: 470px;">
				<img style="position:absolute; top:55px; left:600px; z-index:9999;" alt="" src="<?=URL?>views/error/img/bee.png"/>
			</div>
		</div><!--END:#parallax -->
 		<div class="devider_top"></div>
		<div id="txt"><!--START:#txt-->
					 <p>Žao nam je, stranica koju zahtevate ne postoji!</p>
        </div><!--END:#txt-->
        <div class="devider_bottom"></div>

		<div id="footer"><!--START:#footer-->
					 <p> Copyright &copy; 2014. All Right Reserved. Design by: <a href="#">Zoran Vulanović</a></p>
		</div><!--END:#footer-->
	</div><!--END:#center_wrap -->
</div><!--END:#wrapper -->


</body>
</html>