<?php
class Client_Api extends Controller{

    public function __construct(){
        parent::__construct();
       // ajax::ajaxCheck();
        /*Session::init();
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

    public function get_clients($id=null){
        if($id==null){
            $sql = 'SELECT clients.client_id, clients.client_cypher, clients.firm_name,
                    CONCAT(clients.client_name, " ", clients.client_surname) AS client_name,
                    CONCAT(clients.client_brlk, " ", clients.client_sup) AS supbrlk,
                    CONCAT(places.place_name, " ", places.post_number) AS place,
                    clients.client_address, clients.client_jmbg, clients.client_email, clients.client_tel,
                    clients.client_fax, clients.client_mob, clients.br_agricultural, clients.pib,
                    clients.maticni_br, clients.bank_name, clients.bank_account, clients.client_type, @curRow := @curRow + 1 AS row_number
                    FROM clients
                    INNER JOIN (SELECT @curRow := 0) r
                    INNER JOIN places ON (places.place_id = clients.place_id)
                    ORDER BY clients.client_id';
        } else {
            $sql = 'SELECT *
                    FROM clients
                    INNER JOIN places ON (places.place_id = clients.place_id)
                    WHERE clients.client_id= :client_id';
            $id = array(':client_id'=>$id);
        }
        $result = $this->model->get_values($sql, $id);
        header('Content-Type: application/json');
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //------------------------------------------------------------------------------------------------------------------------------------------

    /* wearehouse */
    public function insert_client(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));
       /* echo '<pre>';
        print_r($data);*/
        $cypher = isset($data->cypher) ? $data->cypher : '';
        $table = 'clients';
        $obj = array(
            'client_type' => $data->client_type,
            'u_pdv' => $data->syspdv,
            'client_cypher' => $cypher,
            'firm_name' => $data->firmname ,
            'client_name' => isset($data->name) ? $data->name  : '',
            'client_surname' => isset($data->surname) ? $data->surname  : '',
            'client_brlk' => isset($data->brlk) ? $data->brlk  : 0,
            'client_sup' => isset($data->sup) ? $data->sup  : '',
            'client_jmbg' => isset($data->jmbg) ? $data->jmbg  : 0,
            'client_address' => $data->address ,
            'place_id' => $data->selectedPlaceId ,
            'client_fax' => isset($data->fax) ? $data->fax  : '',
            'client_tel' => isset($data->tel) ? $data->tel  : '',
            'client_mob' => isset($data->mob) ? $data->mob  : '',
            'client_email' => isset($data->email) ? $data->email : '',
            'br_agricultural' => isset($data->pib) ? $data->pib  : '',
            'pib' => isset($data->pib) ? $data->pib  : '',
            'maticni_br' => isset($data->maticni_broj) ? $data->maticni_broj  : '',
            'bank_name' => isset($data->bank_name) ? $data->bank_name  : '',
            'bank_account' => $data->bank_jib.'-'.$data->bank_account,

        );
        header('Content-Type: application/json');
        /*$check_jmbg = 'SELECT * FROM '.$table.' WHERE client_jmbg= :jmbg';
        $check_jmbgobj = array(':jmbg'=>strip_tags(trim($data->jmbg)));
        $exists = $this->model->check_exists($check_jmbg, $check_jmbgobj);

        if($exists){
            echo json_encode(array('success'=>$exists, 'error_msg'=>'Osoba sa unetim JMBG brojem već postoji u bazi podataka!', 'field'=>'jmbg'), JSON_NUMERIC_CHECK);
            return false;
        }*/
        $exists = 0;
        $client_id = $this->model->set_values($table, $obj);
        header('Content-Type: application/json');
        echo json_encode(array('success'=>$exists,'result'=>$client_id), JSON_NUMERIC_CHECK);
    }

    //------------------------------------------------------------------------------------------------------------------------------------------

    public function update_client($id){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));
        //update($table, $data, $where)$table = 'wearehouses';
        $table = 'clients';
        $data = array(
            'client_type' => isset($data->client_type) ? $data->client_type : '',
            'u_pdv' => isset($data->u_pdv) ? $data->u_pdv : '',
            'client_cypher' => isset($data->client_cypher) ? $data->client_cypher : '',
            'firm_name' => isset($data->firm_name) ? $data->firm_name : '',
            'client_name' => isset($data->client_name) ? $data->client_name : '',
            'client_surname' => isset($data->client_surname) ? $data->client_surname : '',
            'client_brlk' => isset($data->client_brlk) ? $data->client_brlk : '',
            'client_sup' => isset($data->client_sup) ? $data->client_sup : '',
            'client_jmbg' => isset($data->client_jmbg) ? $data->client_jmbg : '',
            'client_address' => isset($data->client_address) ? $data->client_address : '',
            'place_id' => isset($data->selectedPlaceId) ? $data->selectedPlaceId : 1,
            'client_fax' => isset($data->client_fax) ? $data->client_fax  : '',
            'client_tel' => isset($data->client_tel) ? $data->client_tel  : '',
            'client_mob' => isset($data->client_mob) ? $data->client_mob  : '',
            'client_email' => isset($data->client_email) ? $data->client_email : '',
            'br_agricultural' => isset($data->br_agricultural) ? $data->br_agricultural  : '',
            'pib' => isset($data->pib) ? $data->pib  : '',
            'maticni_br' => isset($data->maticni_broj) ? $data->maticni_broj  : '',
            'bank_name' => isset($data->bank_name) ? $data->bank_name  : '',
            'bank_account' => $data->bank_jib.'-'.$data->bank_account,

        );
        $where = 'client_id='.$id;
        $this->model->update_values($table, $data, $where);
    }

    //---------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_clients_excell($id=null){
        if($id==null){
            $sql = 'SELECT clients.*, places.place_name, places.post_number
                    FROM clients
                    INNER JOIN places ON (places.place_id = clients.place_id)
                    ORDER BY clients.client_id';
        } else {
            $sql = 'SELECT *
                    FROM clients
                    INNER JOIN places ON (places.place_id = clients.place_id)
                    WHERE clients.client_id= :client_id';
            $id = array(':client_id'=>$id);
        }
        $result = $this->model->get_values($sql, $id);
       // print_r($result);
        return $result;
    }


    public function printExcel(){

        $data = $this->model->get_clients();
       // echo '<pre>';
       //print_r($data); return false;
        $xml = new ExcelWriterXML('Lista dobavljaca-kupaca.xls');
        $xml->docAuthor('rtyrty');

        $format = $xml->addStyle('StyleHeader');
        $format->fontBold();

        $sheet = $xml->addSheet('Lista_dobavljaca_kupaca');
        $sheet->columnWidth(1,'70');
        $sheet->columnWidth(2,'150');
        $sheet->columnWidth(3,'150');
        $sheet->columnWidth(4,'50');
        $sheet->columnWidth(5,'70');
        $sheet->columnWidth(6,'100');
        $sheet->columnWidth(7,'100');
        $sheet->columnWidth(8,'100');
        $sheet->columnWidth(9,'100');
        $sheet->columnWidth(10,'70');
        $sheet->columnWidth(11,'70');
        $sheet->columnWidth(12,'70');
        $sheet->columnWidth(13,'70');
        $sheet->columnWidth(14,'100');
        $sheet->columnWidth(15,'50');
        $sheet->columnWidth(16,'100');
        $sheet->columnWidth(17,'100');
        $sheet->columnWidth(18,'100');
        $sheet->columnWidth(19,'125');
        $sheet->columnWidth(20,'100');




        $sheet->writeString(1, 1,'Sifra dobavljaca', 'StyleHeader');
        $sheet->writeString(1, 2, 'Naziv firme', 'StyleHeader');
        $sheet->writeString(1, 3, 'Adresa', 'StyleHeader');
        $sheet->writeString(1, 4, 'Postanski broj', 'StyleHeader');
        $sheet->writeString(1, 5, 'Naziv mesta', 'StyleHeader');
        $sheet->writeString(1, 6, 'Odgovorno lice', 'StyleHeader');
        $sheet->writeString(1, 7, 'Br licne karte', 'StyleHeader');
        $sheet->writeString(1, 8, 'Sup', 'StyleHeader');
        $sheet->writeString(1, 9, 'JMBG dgovornog lica', 'StyleHeader');
        $sheet->writeString(1, 10, 'Tel', 'StyleHeader');
        $sheet->writeString(1, 11, 'Mob', 'StyleHeader');
        $sheet->writeString(1, 12, 'Fax', 'StyleHeader');
        $sheet->writeString(1, 13, 'email','StyleHeader');
        $sheet->writeString(1, 14, 'Br poljgazdinstva','StyleHeader');
        $sheet->writeString(1, 15, 'U PDV', 'StyleHeader');
        $sheet->writeString(1, 16, 'Pib', 'StyleHeader');
        $sheet->writeString(1, 17, 'Maticni broj', 'StyleHeader');
        $sheet->writeString(1, 18, 'Naziv banke', 'StyleHeader');
        $sheet->writeString(1, 19, 'Br racuna', 'StyleHeader');
        $sheet->writeString(1, 20, 'Tip dobavljacakupca', 'StyleHeader');





       $row_br=1;
        foreach ($data as $value){
            $row_br  = $row_br +1;
            $sheet->writeNumber($row_br,1,$value['client_cypher']);
            $sheet->writeString($row_br,2,$value['firm_name']);
            $sheet->writeString($row_br,3,$value['client_address']);
            $sheet->writeNumber($row_br,4,$value['post_number']);
            $sheet->writeString($row_br,5,$value['place_name']);
            $sheet->writeString($row_br,6,$value['client_name'].' '.$value['client_surname']);
            $sheet->writeNumber($row_br,7,$value['client_brlk']);
            $sheet->writeString($row_br,8,$value['client_sup']);
            $sheet->writeNumber($row_br,9,$value['client_jmbg']);
            $sheet->writeString($row_br,10,$value['client_tel']);
            $sheet->writeString($row_br,11,$value['client_mob']);
            $sheet->writeString($row_br,12,$value['client_fax']);
            $sheet->writeString($row_br,13,$value['client_email']);
            $sheet->writeString($row_br,14,$value['br_agricultural']);
            $sheet->writeString($row_br,15,$value['u_pdv']);
            $sheet->writeNumber($row_br,16,$value['pib']);
            $sheet->writeNumber($row_br,17,$value['maticni_br']);
            $sheet->writeString($row_br,18,$value['bank_name']);
            $sheet->writeString($row_br,19,$value['bank_account']);
            $sheet->writeString($row_br,20,$value['client_type']);
        };










        $xml->sendHeaders();
        $xml->writeData();
    }




}
?>