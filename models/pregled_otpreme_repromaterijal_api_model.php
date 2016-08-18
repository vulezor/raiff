<?php
class Pregled_Otpreme_Repromaterijal_Api_Model extends Model{
    public function __construct(){
        parent::__construct();
    }

    //get values
    public function get_values($sql, $id){

        if($id===null){
            $obj = array();
        } else if (is_array($id)) {
            $obj = $id;
        }
        return $this->db->select($sql, $obj);
    }

    //set values
    public function set_values($table, $obj){
        $this->db->insert($table, $obj);
        return $this->db->lastInsertId();
    }

    public function update_values($table, $data, $where){
         $this->db->update($table, $data, $where);
    }

    public function check_exists($sql, $array){
        return $this->db->countRow($sql, $array);
    }

    public function get_clients(){
        $sql = 'SELECT clients.*, places.place_name, places.post_number FROM clients
                    INNER JOIN places ON (places.place_id = clients.place_id)
                    ORDER BY clients.client_id';
        $result = $this->db->select($sql);
        return $result;
    }
}
?>