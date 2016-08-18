<?php
class Login_Model extends Model{
    public function __construct(){
        parent::__construct();
    }

    public function login($data){

        $sql="SELECT users.*, wearehouses.* FROM users
        LEFT JOIN wearehouses ON (wearehouses.wearehouse_id = users.wearehouse_id)
        WHERE username= :username
        AND password_enc= :password_enc
        AND active='Y'";
        $result = $this->db->select($sql, array(
            ':username'=>$data['username'],
            ':password_enc'=>$data['password_enc']
        ));
        return $result;

    }
}
?>