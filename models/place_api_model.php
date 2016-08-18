<?php
class Place_Api_Model extends Model{
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
    public function set_values($table, $obj){

        $this->db->insert($table, $obj);
        return $this->db->lastInsertId();
    }

    public function check_exists($sql, $array){
       return $this->db->countRow($sql, $array);
    }
}
?>