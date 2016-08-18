<?php
class Api_Model extends Model{
    public function __construct(){
        parent::__construct();
    }

    //get values
    public function get_values($sql, $id){
        if($id===null){
            $obj = array();
        } else {
            $obj = array(':place_id'=>$id);
        }
        return $this->db->select($sql, $obj);
    }

    //set values
    public function set_values($sql, $obj){

        return $this->db->insert($sql, $obj);
    }
}
?>