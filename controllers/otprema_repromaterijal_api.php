<?php
class Otprema_Repromaterijal_Api extends Controller
{
    private $_proracun;
    public function __construct()
    {
        parent::__construct();
        //error_reporting(0);
        $this->_proracun = new Proracun();
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

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
            return array('login'=>0);
        } else {
            return array('login'=>1);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_goods_type(){
        Ajax::ajaxCheck();
        header('Content-Type: application/json');
        $sql = "SELECT * FROM type_of_goods WHERE goods_type='hemija' || goods_type='djubrivo'  || goods_type='seme'";
        $result = $this->model->get_values($sql,$id=null);
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_goods_name($type_of_goods_id){
        Ajax::ajaxCheck();
        $sql = "SELECT goods.*, type_of_measurement_unit.measurement_unit, type_of_measurement_unit.measurement_name FROM goods
                INNER JOIN  type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id )
                WHERE goods.type_of_goods_id= :type_of_goods_id";
        $obj = array(':type_of_goods_id'=>$type_of_goods_id);
        $result = $this->model->get_values($sql,$obj);
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function otprema_repromaterijal(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object


        header('Content-Type: application/json');
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){
            $document_br = $this->set_document_number();
            $date = new DateTime();
           /* var_dump($data->datum);
            print_r(is_null($data->datum)).'\n';*/
            $datum =   $data->datum === (string)"" || !isset($data->datum)  ? $date->format('Y-m-d H:i:s') : date('Y-m-d ', strtotime($data->datum)).' '.date('H:i:s');
            $exit_date = $data->vaga === true ? "0000-00-00 00:00:00" : $date->format('Y-m-d H:i:s');
            //print_r($datum); return false;
            $output_info= array(
                "document_br"=>$document_br,
                "user_id"=>Session::get('user_id'),
                "wearehouse_id"=>Session::get('wearehouse_id'),
                "client_id"=>$data->client_id,
                "output_date"=> $datum,
                "exit_date"=> $exit_date,
                "driver_name"=>$data->driver_name,
                "vehicle_registration"=>$data->driver_reg
            );
            $output_id = $this->model->set_values('output_records',  $output_info);

            if($data->vaga){
                $goods_parametars['tara'] = $data->tara;
                $goods_parametars['output_id'] = $output_id;
                $goods_parametars['sort_of_goods_id'] = 2;
                $goods_parametars['type_of_goods_id'] = $data->type_of_goods_id;
                $goods_parametars['goods_id'] = $data->goods_id;
                $input_repromaterijal_id = $this->model->set_values('output_repromaterijal', $goods_parametars);

            } else {
                foreach($data->orders as $obj){
                    $input_repromaterijal= array(
                        "output_id"=>$output_id,
                        "sort_of_goods_id"=>$obj->sort_of_goods_id,
                        "type_of_goods_id"=>$obj->type_of_goods_id,
                        "goods_id"=>$obj->goods_id,
                        "kolicina"=>$obj->quantity,
                        "lot"=>$obj->lot
                    );
                    $this->model->set_values('output_repromaterijal',  $input_repromaterijal);
                }
            }

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function enable_days(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        header('Content-Type: application/json');
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){
            $sql = "SELECT DATE_FORMAT(DATE(input_date),'%e-%c-%Y') as datum FROM input_records WHERE wearehouse_id= :wearehouse_id AND sort_of_goods_id= :sort_of_goods_id   GROUP BY DATE(input_date)";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1');
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
           // echo json_encode(array('ulogovan'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1'), JSON_NUMERIC_CHECK);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    public function get_search_type(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object

        header('Content-Type: application/json');
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){
            $input_date = date('Y-m-d', strtotime($data->datumPrijema));
            $sql = "SELECT output_records.type_of_goods_id, type_of_goods.goods_type FROM output_records
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_records.type_of_goods_id)
                    WHERE wearehouse_id= :wearehouse_id AND sort_of_goods_id= :sort_of_goods_id AND DATE(output_date)= :input_date AND output_records.stornirano='n' GROUP BY type_of_goods_id";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1', ':input_date'=>$input_date);
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }

    }

    public function get_search_good_name(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object

        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){
            $input_date = date('Y-m-d', strtotime($data->datumPrijema));
            $type_of_goods_id = $data->input_type_id;
            $sql = "SELECT goods.goods_id, goods.goods_name  FROM output_records
                    INNER JOIN goods ON (goods.goods_id = output_records.goods_id)
                    WHERE output_records.wearehouse_id=:wearehouse_id AND output_records.sort_of_goods_id= :sort_of_goods_id
                     AND  DATE(output_records.input_date)= :output_date AND input_records.type_of_goods_id= :type_of_goods_id AND output_records.stornirano='n' GROUP BY goods_id";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1', ':output_date'=>$input_date, ':type_of_goods_id'=>$type_of_goods_id);
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }


    public function get_search_prijemnica(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){
            $input_date = date('Y-m-d', strtotime($data->datumPrijema));
            $type_of_goods_id = $data->input_type_id;
            $goods_id = $data->goods_id;
            $sql = "SELECT input_records.input_id, goods.goods_id, CONCAT(input_records.document_br, ' / ', YEAR(input_records.input_date)) As document_br ,goods.goods_name, clients.firm_name FROM input_records
                    INNER JOIN goods ON (goods.goods_id = input_records.goods_id)
                    INNER JOIN clients ON (clients.client_id = input_records.client_id)
                    WHERE input_records.wearehouse_id= :wearehouse_id AND input_records.sort_of_goods_id= :sort_of_goods_id AND DATE(input_records.input_date)= :input_date AND input_records.type_of_goods_id= :type_of_goods_id AND input_records.goods_id= :goods_id AND input_records.stornirano='n' ORDER BY input_records.input_id DESC";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1', ':input_date'=>$input_date, ':type_of_goods_id'=>$type_of_goods_id, ':goods_id'=>$goods_id );
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);


        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function getEnabledDays(){
        Ajax::ajaxCheck();
        $sql = "SELECT DATE_FORMAT(DATE(input_date),'%e-%c-%Y') FROM input_records WHERE wearehouse_id= :wearehouse_id AND sort_of_goods_id= :sort_of_goods_id AND input_records.stornirano='n'   GROUP BY DATE(input_date)";
        $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1');
        $result = $this->model->get_values($sql,$obj);
        print_r($result);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    private function set_document_number()
    {
        Ajax::ajaxCheck();
        //get last inserted document numer and year from
        $sql = "SELECT document_br, output_date, YEAR ( output_date ) as record_year FROM output_records ORDER BY output_id DESC LIMIT 1";
        $result = $this->model->get_values($sql, null);
        //if not empty result
        if (!empty($result)){
            if($result[0]['record_year'] === date('Y')){ //check are we in same year
                return $result[0]['document_br'] + 1;      //last doc number increment for 1
            } else {
                return 1; //if year is new start from one
            }
        } else {
            return 1; // if empty result we must start from somethink
        }
    }

    public function prvo_merenje_repromaterijal($session_id=null){
        $data = json_decode(file_get_contents("php://input"));
        $session = $session_id==null ? (string) $data->session_id : (string) $session_id;
        $check_session = $this->check_logedIn($session);

        header('Content-Type: application/json');

        if( $check_session['login'] == 1){
            $user_id = Session::get('user_id');
            $sql='SELECT
                    goods.goods_name,
                    output_records.output_id,
                    output_records.driver_name,
                    output_records.vehicle_registration,
                    CONCAT(document_br, " / ", YEAR(output_date)) As document_br,
                    DATE_FORMAT(DATE(output_records.output_date),"%d.%m.%Y") AS date,
                    TIME(output_records.output_date) AS time,
                    output_repromaterijal.tara,
                    clients.firm_name
                    FROM output_records
                    INNER JOIN output_repromaterijal ON ( output_repromaterijal.output_id = output_records.output_id )
                    INNER JOIN clients ON ( clients.client_id = output_records.client_id )
                    INNER JOIN goods ON ( goods.goods_id = output_repromaterijal.goods_id )
                    WHERE output_records.wearehouse_id= :wearehouse_id
                    AND output_records.exit_date = :exit_date
                    AND output_repromaterijal.sort_of_goods_id= :sort_of_goods_id
                    AND output_records.stornirano="n"
                    ORDER BY output_records.output_id DESC';
            $result = $this->model->get_values($sql, array(":wearehouse_id"=>Session::get('wearehouse_id'), ":exit_date"=>"0000-00-00 00:00:00", ":sort_of_goods_id"=>"2"));
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    public function drugo_merenje_repromaterijal($session_id=null){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));

        $session = $session_id==null ? (string) $data->session_id : (string) $session_id;
        $check_session = $this->check_logedIn($session);

        header('Content-Type: application/json');
        $bruto = $data->bruto;// === "0" ? (int) 200 : (int) $data->tara;
        if( $check_session['login'] == 1){
            $sql='SELECT
            output_records.output_id,
            type_of_goods.goods_type,
            output_repromaterijal.bruto,
            output_repromaterijal.output_repromaterijal_id
            FROM output_records
            INNER JOIN output_repromaterijal ON ( output_repromaterijal.output_id = output_records.output_id )
            INNER JOIN type_of_goods ON ( type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id )
            WHERE output_records.output_id= :output_id
            AND output_records.stornirano="n"';
            $result = $this->model->get_values($sql,array(":output_id"=>$data->output_id));
            $neto = $bruto - $result[0]['tara'];
            $obj = array(
                'neto'=>$neto,
                'bruto'=>$bruto
            );
            $this->_update_results($obj, $result[0]['output_repromaterijal_id'], $result[0]['output_id']);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------




    private function _update_results($obj, $output_repromaterijal_id, $output_id){
        $table = 'output_repromaterijal';
        $where = 'output_repromaterijal_id='.$output_repromaterijal_id;
        $this->model->update_values($table, $obj, $where);

        $date = new DateTime();

        $table = 'output_records';
        $obj = array('exit_date'=>$date->format('Y-m-d H:i:s'),
        );
        $where = 'output_id='.$output_id;
        $result = $this->model->update_values($table, $obj, $where);
        echo json_encode(array('output_id'=>$result));
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function select_last_input($session_id=null){
        $data = json_decode(file_get_contents("php://input"));
        $session = $session_id==null ? (string) $data->session_id : (string) $session_id;
        $check_session = $this->check_logedIn($session);
      //  print_r(Session::get('wearehouse_id'))l
        header('Content-Type: application/json');

        if( $check_session['login'] == 1){
        $sql='SELECT
                output_records.output_id,
                output_records.driver_name,
                output_records.vehicle_registration,
                output_records.dispozicija_id,
                output_records.end_point,
                CONCAT(output_records.document_br, " / ", YEAR(output_records.output_date)) As document_br,
                YEAR (output_records.output_date) as year,
                DATE_FORMAT(DATE(output_records.output_date),"%d.%m.%Y") AS date,
                TIME(output_records.output_date) AS time,
                clients.client_cypher,
                clients.firm_name,
                clients.client_address,
                places.place_name,
                places.post_number,
                clients.client_brlk,
                clients.client_sup,
                clients.client_jmbg,
                clients.client_jmbg,
                clients.br_agricultural,
                clients.pib,
                wearehouses.wearehouse_name,
                CONCAT(users.name, " ", users.surname) AS storekeeper
                FROM output_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = output_records.user_id)
                INNER JOIN output_repromaterijal ON (output_repromaterijal.output_id = output_records.output_id)
                INNER JOIN clients ON (clients.client_id = output_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                WHERE output_records.wearehouse_id= :wearehouse_id
                AND exit_date !="0000-00-00 00:00:00"
                AND output_repromaterijal.sort_of_goods_id= :sort_of_goods_id
                ORDER BY output_records.output_id DESC LIMIT 1';
            $result = $this->model->get_values($sql, array(":wearehouse_id"=>Session::get('wearehouse_id'), ":sort_of_goods_id"=>"2"));
            $result = $result[0];


            $sql = 'SELECT
                    output_repromaterijal.bruto,
                    output_repromaterijal.tara,
                    output_repromaterijal.neto,
                    output_repromaterijal.lot,
                    output_repromaterijal.kolicina,
                    sort_of_goods.goods_sort,
                    type_of_goods.goods_type,
                    goods.goods_name,
                    goods.goods_cypher,
                    type_of_measurement_unit.measurement_unit,
                    type_of_measurement_unit.measurement_name
                    FROM output_repromaterijal
                    INNER JOIN sort_of_goods ON ( sort_of_goods.sort_of_goods_id = output_repromaterijal.sort_of_goods_id )
                    INNER JOIN type_of_goods ON ( type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id )
                    INNER JOIN goods ON ( goods.goods_id = output_repromaterijal.goods_id )
                    INNER JOIN type_of_measurement_unit ON ( type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id )
                    WHERE output_repromaterijal.output_id= :output_id';
            $inputs = $this->model->get_values($sql, array(":output_id"=>$result['output_id']));
            $result['inputs'] = $inputs;
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function select_odabrani_prijem(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        header('Content-Type: application/json');
        if( $check_session['login'] == 1){
            $sql='SELECT
                goods.goods_cypher,
                goods.goods_name,
                input_records.input_id,
                input_records.driver_name,
                input_records.vehicle_registration,
                CONCAT(input_records.document_br, " / ", YEAR(input_records.input_date)) As document_br,
                input_records.input_merkantila_id,
                YEAR (input_records.input_date) as year,
                DATE_FORMAT(DATE(input_records.input_date),"%d.%m.%Y") AS date,
                TIME(input_records.input_date) AS time,
                input_merkantila.bruto,
                input_merkantila.vlaga,
                input_merkantila.primese,
                input_merkantila.hektolitar,
                input_merkantila.lom,
                input_merkantila.defekt,
                input_merkantila.protein,
                input_merkantila.energija,
                input_merkantila.gluten,
                input_merkantila.br_padanja,
                input_merkantila.kalo_rastur,
                input_merkantila.tara,
                input_merkantila.neto,
                input_merkantila.dnv,
                input_merkantila.dnp,
                input_merkantila.dnd,
                input_merkantila.dnh,
                input_merkantila.dnl,
                input_merkantila.srps,
                input_merkantila.trosak_susenja,
                input_merkantila.suvo_zrno,
                clients.client_cypher,
                clients.firm_name,
                clients.client_address,
                places.place_name,
                places.post_number,
                clients.client_brlk,
                clients.client_sup,
                clients.client_jmbg,
                clients.client_jmbg,
                clients.br_agricultural,
                clients.pib,
                type_of_goods.goods_type, wearehouses.wearehouse_name,
                CONCAT(users.name, " ", users.surname) AS storekeeper
                FROM input_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = input_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = input_records.user_id)
                INNER JOIN input_merkantila ON (input_merkantila.input_merkantila_id = input_records.input_merkantila_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_records.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_records.type_of_goods_id)
                WHERE input_records.wearehouse_id= :wearehouse_id  AND input_records.sort_of_goods_id= :sort_of_goods_id AND input_records.input_id= :input_id';
            $result = $this->model->get_values($sql, array(":wearehouse_id"=>Session::get('wearehouse_id'), ":sort_of_goods_id"=>"2", ":input_id"=>$data->input_id));
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }


    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_all_goods(){
        $sql = 'SELECT goods.*,
                type_of_measurement_unit.measurement_unit,
                type_of_measurement_unit.measurement_name
                FROM goods
                INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                WHERE goods.sort_of_goods_id="2"
                AND goods.type_of_goods_id="6"
                OR goods.type_of_goods_id="7"
                OR goods.type_of_goods_id="9"
                ORDER BY goods_id';
        $result = $this->model->get_values($sql, $id=null);
        header('Content-Type: application/json');
        echo json_encode($result);

    }


































}
?>