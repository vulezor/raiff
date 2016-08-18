<?php
ob_start();
   require 'config/config.php';
   require 'libs/libs.php';
  require 'libs/ssp_class.php';
  //require 'libs/MakeImg.php';
 // require 'libs/ssp_class.php';
  // spl_autoload_register
  function __autoload($class){
    //echo $class;
      require LIBS . $class . ".php";
  }
 /* require 'libs/Bootstrap.php';
  require 'libs/Controller.php';
  require 'libs/Model.php';
  require 'libs/View.php';
  //Sessions hendler
  require 'libs/libs.php';
  require 'libs/Database.php';
  require 'libs/Session.php';
  require 'libs/Hash.php';*/

  $app = new Bootstrap();

  
  ob_end_flush();
?>
