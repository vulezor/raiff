<?php
class User_Api extends Controller{

    public function __construct(){
        parent::__construct();
    }

    public function get_users($id=null){
        if($id==null){
            $sql = 'SELECT users.user_id,
                    IF(users.wearehouse_id IS NULL OR users.wearehouse_id=0,"/",wearehouses.wearehouse_name ) AS wearehouse,
                    CONCAT(users.name, " ", users.surname) AS user_name, CONCAT(users.brlk, " ", users.sup) AS supbrlk, users.address, users.jmbg, users.email,
                    CONCAT(places.place_name, " ", places.post_number) AS place, users.role , users.active, @curRow := @curRow + 1 AS row_number
                    FROM users
                    INNER JOIN (SELECT @curRow := 0) r
                    INNER JOIN places ON (places.place_id = users.place_id)
                    LEFT JOIN wearehouses ON (wearehouses.wearehouse_id = users.wearehouse_id)
                    ORDER BY user_id ASC';
        } else {
            $sql = 'SELECT users.*, CONCAT(places.place_name, " ", places.post_number) AS place FROM users
                    INNER JOIN places ON (places.place_id = users.place_id)
                    WHERE user_id= :user_id';
            $id = array(':user_id'=>$id);
        }
        $result = $this->model->get_values($sql, $id);
        header('Content-Type: application/json');
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //------------------------------------------------------------------------------------------------------------------------------------------

    /* wearehouse */
    public function insert_user(){
        $data = json_decode(file_get_contents("php://input"));
        $table = 'users';
        $obj  = array(
            'name'=> strip_tags($data->name),
            'surname'=> strip_tags($data->surname),
            'address'=>strip_tags($data->address),
            'place_id'=>strip_tags($data->selectedPlaceId),
            'brlk'=>strip_tags($data->brlk),
            'sup'=>strip_tags($data->sup),
            'jmbg'=>strip_tags($data->jmbg),
            'email'=>strip_tags($data->email),
            'username'=>strip_tags($data->username),
            'password'=>strip_tags($data->password),
            'password_enc'=>Hash::create('sha1', strip_tags(trim($data->password)), HESH_SALT),
            'role'=>strip_tags($data->role),
            'set_date'=>date("Y-m-d H:i:s")
        );
        if(strip_tags($data->role)==='Magacioner'){
            $obj['wearehouse_id'] = strip_tags($data->selectedWarehouseId);
        }
        header('Content-Type: application/json');
        $check_jmbg = 'SELECT * FROM users WHERE jmbg= :jmbg';
        $check_jmbgobj = array(':jmbg'=>strip_tags(trim($data->jmbg)));
        $exists = $this->model->check_exists($check_jmbg, $check_jmbgobj);

        if($exists){
            echo json_encode(array('success'=>$exists, 'error_msg'=>'Osoba sa unetim JMBG brojem već postoji u bazi podataka!', 'field'=>'jmbg'), JSON_NUMERIC_CHECK);
            return false;
        }

        $check_username = 'SELECT * FROM users WHERE username= :username';
        $check_usernameobj = array(':username'=>strip_tags(trim(strip_tags($data->username))));
        $exists = $this->model->check_exists($check_username, $check_usernameobj);

        if($exists){
            echo json_encode(array('success'=>$exists, 'error_msg'=>'Korisničko ime "'.strip_tags(trim(strip_tags($data->username))).'" koje ste ukucali je zauzeto.<br /> Molim vas promenite korisničko ime za korisnika ' . strip_tags(trim($data->name)) . ' ' .strip_tags(trim($data->surname)).'!', 'field'=>'username'), JSON_NUMERIC_CHECK);
            return false;
        }

        $user_id = $this->model->set_values($table, $obj);
        header('Content-Type: application/json');
        echo json_encode(array('success'=>$exists,'result'=>$user_id), JSON_NUMERIC_CHECK);
    }

    //------------------------------------------------------------------------------------------------------------------------------------------
    public function update_activity($id){
        $data = json_decode(file_get_contents("php://input"));
        $obj= array('active'=>$data->active);
        $table = 'users';
        $where = 'user_id='.$id;
        $this->model->update_values($table, $obj, $where);
        echo json_encode(array('success'=>0), JSON_NUMERIC_CHECK);
    }

    public function update_user($id){
        $data = json_decode(file_get_contents("php://input"));
        $table = 'users';
        $obj  = array(
            'name'=> strip_tags($data->name),
            'surname'=> strip_tags($data->surname),
            'address'=>strip_tags($data->address),
            'place_id'=>strip_tags($data->selectedPlaceId),
            'brlk'=>strip_tags($data->brlk),
            'sup'=>strip_tags($data->sup),
            'jmbg'=>strip_tags($data->jmbg),
            'email'=>strip_tags($data->email),
            'username'=>strip_tags($data->username),
            'password'=>strip_tags($data->password),
            'password_enc'=>Hash::create('sha1', strip_tags(trim($data->password)), HESH_SALT),
            'role'=>strip_tags($data->role),
            'set_update'=>date("Y-m-d H:i:s")
        );
        if(strip_tags($data->role)==='Magacioner'){
            $obj['wearehouse_id'] = strip_tags($data->selectedWarehouseId);
        }
        header('Content-Type: application/json');

        $where = 'user_id='.$id;
        $this->model->update_values($table, $obj, $where);
        echo json_encode(array('success'=>0), JSON_NUMERIC_CHECK);
    }


}
?>