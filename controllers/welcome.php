<?php
class Welcome extends Controller
{
    public function __construct(){	
		parent::__construct();
	}
	
	public function index(){
		$this->view->render('welcome/index', true);
	}
}
?>