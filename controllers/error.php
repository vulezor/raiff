<?php
  class Error extends Controller {
    public function __construct(){
      parent::__construct();
    }

    public function index(){
      $this->view->msg = 'Ova stranica ne postoji!';
      $this->view->render('error/index', true);
    }
  }
?>