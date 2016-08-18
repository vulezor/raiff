<?php
/*
* Name of file: Bootstrap V1.0
* Author: Zoran Vulanovic
* Adress: Danila Bojovica 131
* City:  21460 Vrbas,
* Region:Vojvodina
* Country Srbija
* tel: +381 60 505 15 78
*/
class Bootstrap {

    private $_url = null;
    private $_controller = null;


    //constructing object
    function __construct(){
      $this->_getURL();
      if(empty($this->_url[0])){
        $this->_loadDefaultController();
        return false;
      }
      $this->_loadCallController();
      $this->_callControlerMethod();
    }

    //Get URL and filter it and set it like private array of this scope
    private function _getURL() {
      $url = isset($_GET['url']) ? $_GET['url'] : null;
      $url = filter_var($url, FILTER_SANITIZE_URL);
      $url = rtrim($url, '/');
      $this->_url = explode('/', $url);
    }

    //loading default controller. Set default controller in config file
    private function _loadDefaultController() {
      require 'controllers/' . DEFAULT_CONTROLLER . '.php';
	  $controller_class = DEFAULT_CONTROLLER;
      $this->_controller = new $controller_class;
	  $this->_controller->loadModel($controller_class);
      $this->_controller->index();
      return false;
    }

    //Call controller from url
    private function _loadCallController() {
     $file = 'controllers/'  . $this->_url[0] . '.php';
     if(file_exists($file)){
       require $file;
       $this->_controller = new $this->_url[0];
       $this->_controller->loadModel($this->_url[0]);
     } else {
      $this->_error();
		return false;
    }
  }


    private function _callControlerMethod() {
     if(isset($this->_url[2])){
       if(method_exists($this->_controller, $this->_url[1])){
         $this->_controller->{$this->_url[1]}($this->_url[2]);
       } else {
         $this->_error();
          return false;
       }
     } else {
       if(isset($this->_url[1])){
        if(method_exists($this->_controller, $this->_url[1])){
          $this->_controller->{$this->_url[1]}();
        } else {
          $this->_error();
          return false;
        }
      } else {
       $this->_controller->index();
     }
   }
  }

  //Error page. You can set new error page in config file
  private function _error() {
    require 'controllers/' . ERROR404 . '.php';
    $this->_controller = new Error();
    $this->_controller->index();
    exit;
  }
}
?>