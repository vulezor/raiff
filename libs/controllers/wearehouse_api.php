<?php
class Wearehouse_Api extends Controller{
    public function __construct(){
        parent::__construct();
        //ajax::ajaxCheck();
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

    public function index(){

    }
    /* wearehouses */
    public function get_wearehouse($id=null){
        if($id===null){
            $sql = 'SELECT wearehouses.*, CONCAT(places.place_name, " ", places.post_number) AS place FROM wearehouses
                    INNER JOIN places ON (places.place_id = wearehouses.place_id)
                    ORDER BY wearehouse_name';
        } else {
            $sql = 'SELECT * FROM wearehouses WHERE wearehouse_id= :wearehouse_id ORDER BY wearehouse_id ASC';
            $id = array(':wearehouse_id'=>$id);
        }

        $result = $this->model->get_values($sql, $id);
        header('Content-Type: application/json');
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    /* wearehouse */
    public function insert_wearehouse(){
        var_dump(data);
        $data = json_decode(file_get_contents("php://input"));
        $table = 'wearehouses';
        $obj  = array(
            'wearehouse_name'=> strip_tags($data->wearehouse_name),
            'wearehouse_address'=> strip_tags($data->wearehouse_address),
            'place_id'=>strip_tags($data->selectedPlaceId),
            'scale_type'=>strip_tags($data->selectedModel),
            'scale_port'=>strip_tags($data->selectedPort),
            'longitude'=>strip_tags($data->longitude),
            'latitude'=>strip_tags($data->latitude),
        );
        $result = $this->model->set_values($table, $obj);
        echo (int) $result;
    }


    /* wearehouse */
    public function update_wearehouse($id){
        $data = json_decode(file_get_contents("php://input"));
        //update($table, $data, $where)
        $table = 'wearehouses';
        $data = array('bruto_polje'=>$data->bruto_polje);
        $where = 'wearehouse_id='.$id;
        $this->model->update_values($table, $data, $where);
    }


}
?>