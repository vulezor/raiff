<?php
class Reservation_Api extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function empty_load()
    {
        echo json_encode(array());
    }

    private function check_logedIn($session_id)
    {
        Session::set_session_id($session_id);
        Session::init();
        $logged = Session::get('loggedIn');
        $status = Session::get('role');
        if ($logged == false && $status != 'magacioner') {
            unset($logged);
            unset($status);
            Session::destroy();
            return array('login' => 0);
        } else {
            return array('login' => 1);
        }
    }
    //----------------------------------------------------------------------------------------------------------------------------------------------------------
    public function get_wearehouses(){
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {
            header('Content-Type: application/json');
            $sql = 'SELECT * FROM wearehouses ORDER BY wearehouse_name ASC';
            $result = $this->model->get_values($sql, $id=null);
            echo json_encode($result);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }
    }

    private function check_logedIn_admin()
    {
        Session::init();
        $logged = Session::get('loggedIn');
        $status = Session::get('role');
        if ($logged == false  && $status != 'Administrator') {
            unset($logged);
            unset($status);
            Session::destroy();
            return array('login' => 0);
        } else {
            return array('login' => 1);
        }
    }

    public function set_reservation(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        header('Content-Type: application/json');
        $date = new DateTime();
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            foreach($data->orders as $obj){
                $rezervacija= array(
                    "user_id"          => Session::get('user_id'),
                    "wearehouse_id"    => $data->wearehouse_id,
                    "client_id"        => $data->client_id,
                    "date"             => $date->format('Y-m-d H:i:s'),
                    "sort_of_goods_id" => $obj->sort_of_goods_id,
                    "type_of_goods_id" => $obj->type_of_goods_id,
                    "goods_id"         => $obj->goods_id,
                    "kolicina"         => $obj->quantity
                );
                $this->model->set_values('reservation',  $rezervacija);
            }

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }

    }

    public function get_reservation(){
        header('Content-Type: application/json');
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $sql = "SELECT
                        reservation.reservation_id,
                        CONCAT(users.name, ' ', users.surname) as user_name,
                        DATE(reservation.date) as datum,
                        clients.firm_name,
                        sort_of_goods.goods_sort,
                        type_of_goods.goods_type,
                        goods.goods_name,
                        reservation.kolicina,
                        CONCAT(type_of_measurement_unit.measurement_name,' ','(',type_of_measurement_unit.measurement_unit,')') as measurement_unit,
                        wearehouses.wearehouse_name,
                        reservation.realizovana,
                        reservation.stornirana,
                        @curRow := @curRow + 1 AS row_number
                    FROM
                        reservation
                    INNER JOIN (SELECT @curRow := 0) r
                    LEFT JOIN users ON
                        users.user_id = reservation.user_id
                    LEFT JOIN wearehouses ON
                        wearehouses.wearehouse_id = reservation.wearehouse_id
                    LEFT JOIN clients ON
                        clients.client_id = reservation.client_id
                    LEFT JOIN sort_of_goods ON
                        sort_of_goods.sort_of_goods_id = reservation.sort_of_goods_id
                    LEFT JOIN type_of_goods ON
                        type_of_goods.type_of_goods_id = reservation.type_of_goods_id
                    LEFT JOIN goods ON
                        goods.goods_id = reservation.goods_id
                    LEFT JOIN type_of_measurement_unit ON
                        type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                    WHERE reservation.stornirana='n'  ORDER BY reservation.reservation_id ASC";//AND reservation.realizovana='n'
            $result = $this->model->get_values($sql, $id=null);
            echo json_encode($result);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //----------------------------------------------------------------------------------------------------------------------------------------------------

    public function storniraj_dokument()
    {
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
       // print_r($data);return false;
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $table = 'reservation';
            $date = new DateTime();
            $new_data = array(
                'stornirana'       => 'y',
                'storna_napomena'  => $data->napomena,
                'stornirana_datum' => $date = $date->format('Y-m-d H:i:s'),
                "stornirao_id"     => Session::get('user_id')
            );
            $where = 'reservation_id="' . $data->reservation_id . '"';
            $this->model->update_values($table, $new_data, $where);
            header('Content-Type: application/json');
            echo json_encode(array('success' => 1));
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }
}
?>