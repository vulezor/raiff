<?php

class Place_Api extends Controller{
    public function __construct(){
        parent::__construct();
       //ajax::ajaxCheck();
       /* Session::init();
        $logged = Session::get('loggedIn');
        $status = Session::get('role');
        if($logged == false && $status != 'administrator'){
            unset($logged);
            unset($status);
            Session::destroy();
            header('location: '.URL);
            die;
        }*/
    }

    /* places */
    public function get_places($id=null){
        if($id===null){
            $sql = 'SELECT * FROM places ORDER BY place_name';
        } else {
            $sql = 'SELECT * FROM places WHERE place_id= :place_id';
        }
        $result = $this->model->get_values($sql, $id);
        header('Content-Type: application/json');
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    /* places */
    public function insert_places(){
        $data = json_decode(file_get_contents("php://input"));
        $place_name = strip_tags(trim($data->place_name));
        $post_number = strip_tags(trim($data->post_number));
        $table = 'places';
        $obj  = array(
            'place_name'=> $place_name,
            'post_number'=> $post_number
        );
        $check_sql = 'SELECT * FROM places WHERE post_number= :post_number AND place_name= :place_name';
        $check_obj  = array(
            ':post_number'=> $post_number,
            ':place_name'=> $place_name,
        );

        $exists = $this->model->check_exists($check_sql, $check_obj);
        if($exists){
            echo json_encode(array('success'=>$exists), JSON_NUMERIC_CHECK);
            return false;
        }
        $result = $this->model->set_values($table, $obj);
        header('Content-Type: application/json');
        echo json_encode(array('success'=>$exists,'result'=>$result), JSON_NUMERIC_CHECK);

    }



}

?>