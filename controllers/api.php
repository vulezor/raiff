<?php
class Api extends Controller{
    public function __construct(){
        parent::__construct();
        Session::init();
        $logged = Session::get('loggedIn');
        $status = Session::get('role');
        if($logged == false && $status != 'administrator'){
            unset($logged);
            unset($status);
            Session::destroy();
            header('location: '.URL);
            die;
        }
    }
    /* warehouses */
    public function get_warehouses(){

    }

    /* places */
    public function get_places($id=null){
        $result = $this->model->get_values($id);
        print_r($result);
    }
}
?>