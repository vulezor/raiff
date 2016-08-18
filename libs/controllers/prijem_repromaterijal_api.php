<?php
class Prijem_Repromaterijal_Api extends Controller
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

    public function prijem_repromaterijal(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        header('Content-Type: application/json');
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){
            //empty array $goods_parametars
            $goods_parametars = array();


          if($data->vaga==='NO'){
              $goods_parametars['neto'] = $data->bruto;
          } else {
              $goods_parametars['bruto'] = $data->bruto;
          }

            //set values from array $goods_parametars into mysql table input_merkantila input_merkantila and return merkantila_id
            $input_merkantila_id = $this->model->set_values('input_merkantila', $goods_parametars);

            $document_br = $this->set_document_number();
            // echo $input_merkantila_id.', '. $document_br;
            $date = new DateTime();

            $input_info= array(
                "document_br"=>$document_br,
                "input_merkantila_id"=>$input_merkantila_id,
                "sort_of_goods_id"=>2,
                "type_of_goods_id"=>$data->type_of_goods_id,
                "goods_id"=>$data->goods_id,
                "user_id"=>Session::get('user_id'),
                "wearehouse_id"=>Session::get('wearehouse_id'),
                "client_id"=>$data->client_id,
                "driver_name"=>$data->driver_name,
                "vehicle_registration"=>$data->driver_reg,
                "input_date"=> $date->format('Y-m-d H:i:s')
            );
            if($data->vaga==='NO'){
                $input_info['exit_date'] = $date->format('Y-m-d H:i:s');
            }
            $this->model->set_values('input_records',  $input_info);
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
            $sql = "SELECT input_records.type_of_goods_id, type_of_goods.goods_type FROM input_records
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_records.type_of_goods_id)
                    WHERE wearehouse_id= :wearehouse_id AND sort_of_goods_id= :sort_of_goods_id AND DATE(input_date)= :input_date AND input_records.stornirano='n' GROUP BY type_of_goods_id";
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
            $sql = "SELECT goods.goods_id, goods.goods_name  FROM input_records
                    INNER JOIN goods ON (goods.goods_id = input_records.goods_id)
                    WHERE input_records.wearehouse_id=:wearehouse_id AND input_records.sort_of_goods_id= :sort_of_goods_id AND  DATE(input_records.input_date)= :input_date AND input_records.type_of_goods_id= :type_of_goods_id AND input_records.stornirano='n' GROUP BY goods_id";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1', ':input_date'=>$input_date, ':type_of_goods_id'=>$type_of_goods_id);
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
        $sql = "SELECT document_br, input_date, YEAR ( input_date ) as record_year FROM input_records ORDER BY input_id DESC LIMIT 1";
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
                    input_records.input_id,
                    input_records.driver_name,
                    input_records.vehicle_registration,
                    CONCAT(document_br, " / ", YEAR(input_date)) As document_br,
                    input_records.input_merkantila_id,
                    DATE_FORMAT(DATE(input_records.input_date),"%d.%m.%Y") AS date,
                    TIME(input_records.input_date) AS time,
                    input_merkantila.bruto,
                    input_merkantila.vlaga,
                    input_merkantila.primese,
                    input_merkantila.hektolitar,
                    input_merkantila.lom,
                    input_merkantila.defekt,
                    clients.firm_name
                    FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_merkantila_id = input_records.input_merkantila_id)
                    INNER JOIN clients ON (clients.client_id = input_records.client_id)
                    INNER JOIN goods ON (goods.goods_id = input_records.goods_id)
                        WHERE input_records.wearehouse_id= :wearehouse_id AND input_records.exit_date = :exit_date AND input_records.sort_of_goods_id= :sort_of_goods_id AND input_records.stornirano="n" ORDER BY input_records.input_id DESC';
            $result = $this->model->get_values($sql, array(":wearehouse_id"=>Session::get('wearehouse_id'), ":exit_date"=>"0000-00-00 00:00:00", ":sort_of_goods_id"=>"2"));
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    public function drugo_merenje_merkantila($session_id=null){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));

        $session = $session_id==null ? (string) $data->session_id : (string) $session_id;
        $check_session = $this->check_logedIn($session);

        header('Content-Type: application/json');
         $tara = $data->tara === "0" ? (int) 200 : (int) $data->tara;
        if( $check_session['login'] == 1){
            $sql='SELECT input_records.input_id, type_of_goods.goods_type,
            input_merkantila.bruto,
            input_merkantila.vlaga,
            input_merkantila.primese,
            input_merkantila.hektolitar,
            input_merkantila.lom,
            input_merkantila.defekt,
            input_merkantila.input_merkantila_id
            FROM input_records
            INNER JOIN input_merkantila ON(input_merkantila.input_merkantila_id = input_records.input_merkantila_id)
            INNER JOIN type_of_goods ON(type_of_goods.type_of_goods_id = input_records.type_of_goods_id)
            WHERE input_records.input_id= :input_id AND input_records.stornirano="n"';
            $result = $this->model->get_values($sql,array(":input_id"=>$data->input_id));
            $result[0]['tara'] = $tara;
            if( $result[0]['goods_type']==='kukuruz'){
                $this->obracun_kukuruza($result[0]);
            } else if($result[0]['goods_type']==='suncokret'){
                $this->obracun_suncokreta($result[0]);
            }else if($result[0]['goods_type']==='soja'){
                $this->obracun_soje($result[0]);
            }else if($result[0]['goods_type']==='uljana repica'){
                $this->obracun_uljane_repice($result[0]);
            } else if($result[0]['goods_type']==='psenica'){
                $this->obracun_psenice($result[0]);
            } else if($result[0]['goods_type']==='sacma'){
                $this->bez_obracuna($result[0]);
            }else if($result[0]['goods_type']==='kukuruz tel-kel'){
                $this->bez_obracuna($result[0]);
            }
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function get_tablica(){
        $sql='SELECT * FROM vlaga_kukuruz WHERE id="1"';
        $tablica = $this->model->get_values($sql, $id=null);
        return $tablica[0];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function get_tablica_psenica(){
        $sql='SELECT * FROM vlaga_psenica WHERE id="1"';
        $tablica = $this->model->get_values($sql, $id=null);
        return $tablica[0];
    }

      private function bez_obracuna($result){
        $data_set = array(
            '_kultura' => $result['goods_type'],
            '_bruto' =>$result['bruto'],
            '_tara' => $result['tara'],
            '_vlaga' => $result['vlaga'],
            '_primese' => $result['primese'],
            '_hektolitar' => $result['hektolitar']
        );
        $neto = $result['bruto'] - $result['tara'];
        $obj = array(
            'tara'=>$result['tara'],
            'neto'=>$neto
        );

        $this->_update_results($obj, $result['input_merkantila_id'], $result['input_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function obracun_psenice($result){
        $data_set = array(
            '_kultura' => $result['goods_type'],
            '_bruto' =>$result['bruto'],
            '_tara' => $result['tara'],
            '_vlaga' => $result['vlaga'],
            '_primese' => $result['primese'],
            '_hektolitar' => $result['hektolitar']
        );
        $this->_proracun->set_property($data_set);

        //nacin obracunavanja vlage
        $nacin_vlage = $this->get_nacin_obracuna_vlage();
        $this->_proracun->set_property('_nacin_obracuna_vlage', $nacin_vlage['psenica_obracun']);

        $tablica = $this->get_tablica_psenica();
        $set_tablica = array(
            '_a14' => $tablica['ps14'],
            '_a14_5' => $tablica['ps14_50'],
            '_a15' => $tablica['ps15'],
            '_a15_5' => $tablica['ps15_50'],
            '_a16' => $tablica['ps16'],
            '_a16_5' => $tablica['ps16_50'],
            '_a17' => $tablica['ps17'],
            '_a17_5' => $tablica['ps17_50'],
            '_a18' => $tablica['ps18'],
            '_a18_5' => $tablica['ps18_50'],
            '_a19' => $tablica['ps19'],
            '_a19_5' => $tablica['ps19_50']
        );
        $this->_proracun->set_property($set_tablica);

        //set srps params
        $srps = $this->get_srps();
        $set_srps = array(
            '_srps_vlaga'  => $srps['psenica_vlaga'],
            '_srps_primese'  => $srps['psenica_primese'],
            '_srps_hektolitar'  => $srps['psenica_hektolitar']
        );
        $this->_proracun->set_property($set_srps);

        //set bonifikacija params
        $bonifikacija = $this->get_bonifikacija();
        $set_bonifikacija = array(
            '_vlaga_donja'    => $bonifikacija['donja_vlps'],
            '_vlaga_gornja'   => $bonifikacija['gornja_vlps'],
            '_primesa_donja'  => $bonifikacija['donja_prps'],
            '_primesa_gornja' => $bonifikacija['gornja_prps'],
            '_hektolitar_donja'      => $bonifikacija['donja_pshl_bo'],
            '_hektolitar_gornja'     => $bonifikacija['gornja_pshl_bo']
        );
        $this->_proracun->set_property($set_bonifikacija);

        $this->_proracun->proracun_psenice();


        $table = 'input_merkantila';
        $obj = array(
            'tara'=>$this->_proracun->get_property('_tara'),
            'neto'=>$this->_proracun->get_property('_neto'),
            'dnv'=>$this->_proracun->get_property('_dnv'),
            'dnp' =>$this->_proracun->get_property('_dnp'),
            'dnh' =>$this->_proracun->get_property('_dnh'),
            'n_x_vlaga'=>$this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese'=>$this->_proracun->get_property('_neto_x_primese'),
            'n_x_hektolitar'=>$this->_proracun->get_property('_neto_x_hektolitar'),
            'srps' => $this->_proracun->get_property('_srps'),
            'trosak_susenja'=> $this->_proracun->get_property('_trs'),
            'suvo_zrno'=> $this->_proracun->get_property('_suvo_zrno'),
        );

        $this->_update_results($obj, $result['input_merkantila_id'], $result['input_id']);
    }



    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function obracun_uljane_repice($result){
        $data_set = array(
            '_kultura' => $result['goods_type'],
            '_bruto' =>$result['bruto'],
            '_tara' => $result['tara'],
            '_vlaga' => $result['vlaga'],
            '_primese' => $result['primese']
        );
        $this->_proracun->set_property($data_set);

        //nacin obracunavanja vlage
        $nacin_vlage = $this->get_nacin_obracuna_vlage();
        $this->_proracun->set_property('_nacin_obracuna_vlage', $nacin_vlage['uljana_obracun']);

        $srps = $this->get_srps();
        $set_srps = array(
            '_srps_vlaga'  => $srps['uljana_vlaga'],
            '_srps_primese'  => $srps['uljana_primese']
        );
        $this->_proracun->set_property($set_srps);

        $bonifikacija = $this->get_bonifikacija();
        $set_bonifikacija = array(
            '_vlaga_donja'    => $bonifikacija['donja_uljvl'],
            '_vlaga_gornja'   => $bonifikacija['gornja_uljvl'],
            '_primesa_donja'  => $bonifikacija['donja_uljpr'],
            '_primesa_gornja' => $bonifikacija['gornja_uljpr']
        );
        $this->_proracun->set_property($set_bonifikacija);
        $this->_proracun->proracun_uljarica();

        $table = 'input_merkantila';
        $obj = array(
            'tara'=>$this->_proracun->get_property('_tara'),
            'neto'=>$this->_proracun->get_property('_neto'),
            'dnv'=>$this->_proracun->get_property('_dnv'),
            'dnp'=>$this->_proracun->get_property('_dnp'),
            'n_x_vlaga'=>$this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese'=>$this->_proracun->get_property('_neto_x_primese'),
            'srps' => $this->_proracun->get_property('_srps'),
        );

         $this->_update_results($obj, $result['input_merkantila_id'], $result['input_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function obracun_soje($result){

        $data_set = array(
            '_kultura' => $result['goods_type'],
            '_bruto' =>$result['bruto'],
            '_tara' => $result['tara'],
            '_vlaga' => $result['vlaga'],
            '_primese' => $result['primese']
        );
        $this->_proracun->set_property($data_set);

        //nacin obracunavanja vlage
        $nacin_vlage = $this->get_nacin_obracuna_vlage();
        $this->_proracun->set_property('_nacin_obracuna_vlage', $nacin_vlage['soja_obracun']);

        $srps = $this->get_srps();
        $set_srps = array(
            '_srps_vlaga'  => $srps['soja_vlaga'],
            '_srps_primese'  => $srps['soja_primese']
        );
        $this->_proracun->set_property($set_srps);

        $bonifikacija = $this->get_bonifikacija();
        $set_bonifikacija = array(
            '_vlaga_donja'    => $bonifikacija['donja_sovl'],
            '_vlaga_gornja'   => $bonifikacija['gornja_sovl'],
            '_primesa_donja'  => $bonifikacija['donja_sopr'],
            '_primesa_gornja' => $bonifikacija['gornja_sopr']
        );
        $this->_proracun->set_property($set_bonifikacija);
        $this->_proracun->proracun_uljarica();

        $table = 'input_merkantila';
        $obj = array(
            'tara'=>$this->_proracun->get_property('_tara'),
            'neto'=>$this->_proracun->get_property('_neto'),
            'dnv'=>$this->_proracun->get_property('_dnv'),
            'dnp'=>$this->_proracun->get_property('_dnp'),
            'n_x_vlaga'=>$this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese'=>$this->_proracun->get_property('_neto_x_primese'),
            'srps' => $this->_proracun->get_property('_srps'),
        );
        $this->_update_results($obj, $result['input_merkantila_id'], $result['input_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function obracun_suncokreta($result){

        $data_set = array(
            '_kultura' => $result['goods_type'],
            '_bruto' =>$result['bruto'],
            '_tara' => $result['tara'],
            '_vlaga' => $result['vlaga'],
            '_primese' => $result['primese']
        );
        $this->_proracun->set_property($data_set);

        //nacin obracunavanja vlage
        $nacin_vlage = $this->get_nacin_obracuna_vlage();
        $this->_proracun->set_property('_nacin_obracuna_vlage', $nacin_vlage['suncokret_obracun']);

        $srps = $this->get_srps();
        $set_srps = array(
            '_srps_vlaga'  => $srps['suncokret_vlaga'],
            '_srps_primese'  => $srps['suncokret_primese']
        );
        $this->_proracun->set_property($set_srps);

        $bonifikacija = $this->get_bonifikacija();
        $set_bonifikacija = array(
            '_vlaga_donja'    => $bonifikacija['donja_sunvl'],
            '_vlaga_gornja'   => $bonifikacija['gornja_sunvl'],
            '_primesa_donja'  => $bonifikacija['donja_sunpr'],
            '_primesa_gornja' => $bonifikacija['gornja_sunpr']
        );
        $this->_proracun->set_property($set_bonifikacija);
        $this->_proracun->proracun_uljarica();

        $table = 'input_merkantila';
        $obj = array(
            'tara'=>$this->_proracun->get_property('_tara'),
            'neto'=>$this->_proracun->get_property('_neto'),
            'dnv'=>$this->_proracun->get_property('_dnv'),
            'dnp'=>$this->_proracun->get_property('_dnp'),
            'n_x_vlaga'=>$this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese'=>$this->_proracun->get_property('_neto_x_primese'),
            'srps' => $this->_proracun->get_property('_srps'),
        );
        $this->_update_results($obj, $result['input_merkantila_id'], $result['input_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function obracun_kukuruza($result){
        $data_set = array(
            '_kultura' => $result['goods_type'],
            '_bruto' =>$result['bruto'],
            '_tara' => $result['tara'],
            '_vlaga' => $result['vlaga'],
            '_primese' => $result['primese'],
            '_lom' => $result['lom'],
            '_defekt' => $result['defekt']
        );
        $this->_proracun->set_property($data_set);

        //nacin obracunavanja vlage
        $nacin_vlage = $this->get_nacin_obracuna_vlage();
        $this->_proracun->set_property('_nacin_obracuna_vlage', $nacin_vlage['kukuruz_obracun']);

        $tablica = $this->get_tablica();
        $set_tablica = array(
            '_a14' => $tablica['ku14'],
            '_a14_5' => $tablica['ku14_5'],
            '_a15' => $tablica['ku15'],
            '_a15_5' => $tablica['ku15_5'],
            '_a16' => $tablica['ku16'],
            '_a16_5' => $tablica['ku16_5'],
            '_a17' => $tablica['ku17'],
            '_a17_5' => $tablica['ku17_5'],
            '_a18' => $tablica['ku18'],
            '_a18_5' => $tablica['ku18_5'],
            '_a19' => $tablica['ku19'],
            '_a19_5' => $tablica['ku19_5'],
            '_a20' => $tablica['ku20'],
            '_a20_5' => $tablica['ku20_5'],
            '_a21' => $tablica['ku21'],
            '_a21_5' => $tablica['ku21_5'],
            '_a22' => $tablica['ku22'],
            '_a22_5' => $tablica['ku22_5'],
            '_a23' => $tablica['ku23'],
            '_a23_5' => $tablica['ku23_5'],
            '_a24' => $tablica['ku24'],
            '_a24_5' => $tablica['ku24_5'],
            '_a25' => $tablica['ku25'],
            '_a25_5' => $tablica['ku25_5'],
            '_a26' => $tablica['ku26'],
            '_a26_5' => $tablica['ku26_5'],
            '_a27' => $tablica['ku27'],
            '_a27_5' => $tablica['ku27_5'],
            '_a28' => $tablica['ku28'],
            '_a28_5' => $tablica['ku28_5'],
            '_a29' => $tablica['ku29'],
            '_a29_5' => $tablica['ku29_5'],
        );
        $this->_proracun->set_property($set_tablica);

        //set srps params
        $srps = $this->get_srps();
        $set_srps = array(
            '_srps_vlaga'  => $srps['kukuruz_vlaga'],
            '_srps_primese'  => $srps['kukuruz_primese'],
            '_srps_lom'  => $srps['kukuruz_lom'],
            '_srps_defekt' => $srps['kukuruz_defekt'],
        );
        $this->_proracun->set_property($set_srps);

        //set bonifikacija params
        $bonifikacija = $this->get_bonifikacija();
        $set_bonifikacija = array(
            '_vlaga_donja'    => $bonifikacija['donja_kuvl'],
            '_vlaga_gornja'   => $bonifikacija['gornja_kuvl'],
            '_primesa_donja'  => $bonifikacija['donja_kupr'],
            '_primesa_gornja' => $bonifikacija['gornja_kupr'],
            '_lom_donja'      => $bonifikacija['donja_kulo'],
            '_lom_gornja'     => $bonifikacija['gornja_kulo'],
            '_defekt_donja'   => $bonifikacija['donja_kude'],
            '_defekt_gornja'  => $bonifikacija['gornja_kude']
        );
        $this->_proracun->set_property($set_bonifikacija);

        $this->_proracun->proracun_kukuruza();


        $table = 'input_merkantila';
        $obj = array(
            'kalo_rastur'=>$this->_proracun->get_property('_kalo'),
            'tara'=>$this->_proracun->get_property('_tara'),
            'neto'=>$this->_proracun->get_property('_neto'),
            'dnv'=>$this->_proracun->get_property('_dnv'),
            'dnp' =>$this->_proracun->get_property('_dnp'),
            'dnl' =>$this->_proracun->get_property('_dnl'),
            'dnd' =>$this->_proracun->get_property('_dnd'),
            'n_x_vlaga'=>$this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese'=>$this->_proracun->get_property('_neto_x_primese'),
            'n_x_lom'=>$this->_proracun->get_property('_neto_x_lom'),
            'n_x_defekt'=>$this->_proracun->get_property('_neto_x_defekt'),
            'srps' => $this->_proracun->get_property('_srps'),
            'trosak_susenja'=> $this->_proracun->get_property('_trs'),
            'suvo_zrno'=> $this->_proracun->get_property('_suvo_zrno'),
        );

        $this->_update_results($obj, $result['input_merkantila_id'], $result['input_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function _update_results($obj, $input_merkantila_id, $input_id){
        $table = 'input_merkantila';
        $where = 'input_merkantila_id='.$input_merkantila_id;
        $this->model->update_values($table, $obj, $where);

        $date = new DateTime();

        $table = 'input_records';
        $obj = array('exit_date'=>$date->format('Y-m-d H:i:s'),
        );
        $where = 'input_id='.$input_id;
        $result = $this->model->update_values($table, $obj, $where);
        echo json_encode(array('input_id'=>$result));
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
                WHERE input_records.wearehouse_id= :wearehouse_id AND exit_date !="0000-00-00 00:00:00" AND input_records.sort_of_goods_id= :sort_of_goods_id
                ORDER BY input_records.exit_date DESC LIMIT 1';
            $result = $this->model->get_values($sql, array(":wearehouse_id"=>Session::get('wearehouse_id'), ":sort_of_goods_id"=>"1"));
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
            $result = $this->model->get_values($sql, array(":wearehouse_id"=>Session::get('wearehouse_id'), ":sort_of_goods_id"=>"1", ":input_id"=>$data->input_id));
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------











































    ///----------------------------------------TEST PURPOSE------------------------------------------
    public function testp(){

        $data_set = array(
            '_kultura' => 'kukuruz',
            '_bruto' => 19490,//$result['bruto'],
            '_tara' => 8880,//$result['tara'],
            '_vlaga' => 19.80,//$result['vlaga'],
            '_primese' => 2,//$result['primese'],
            '_lom' => 5,//$result['lom'],
            '_defekt' => 3,//$result['defekt']
        );
        $this->_proracun->set_property($data_set);

        $tablica = $this->get_tablica();
        $set_tablica = array(
            '_a14' => $tablica['ku14'],
            '_a14_5' => $tablica['ku14_5'],
            '_a15' => $tablica['ku15'],
            '_a15_5' => $tablica['ku15_5'],
            '_a16' => $tablica['ku16'],
            '_a16_5' => $tablica['ku16_5'],
            '_a17' => $tablica['ku17'],
            '_a17_5' => $tablica['ku17_5'],
            '_a18' => $tablica['ku18'],
            '_a18_5' => $tablica['ku18_5'],
            '_a19' => $tablica['ku19'],
            '_a19_5' => $tablica['ku19_5'],
            '_a20' => $tablica['ku20'],
            '_a20_5' => $tablica['ku20_5'],
            '_a21' => $tablica['ku21'],
            '_a21_5' => $tablica['ku21_5'],
            '_a22' => $tablica['ku22'],
            '_a22_5' => $tablica['ku22_5'],
            '_a23' => $tablica['ku23'],
            '_a23_5' => $tablica['ku23_5'],
            '_a24' => $tablica['ku24'],
            '_a24_5' => $tablica['ku24_5'],
            '_a25' => $tablica['ku25'],
            '_a25_5' => $tablica['ku25_5'],
            '_a26' => $tablica['ku26'],
            '_a26_5' => $tablica['ku26_5'],
            '_a27' => $tablica['ku27'],
            '_a27_5' => $tablica['ku27_5'],
            '_a28' => $tablica['ku28'],
            '_a28_5' => $tablica['ku28_5'],
            '_a29' => $tablica['ku29'],
            '_a29_5' => $tablica['ku29_5'],
        );
        $this->_proracun->set_property($set_tablica);

        //set srps params
        $srps = $this->get_srps();
        $set_srps = array(
            '_srps_vlaga'  => $srps['kukuruz_vlaga'],
            '_srps_primese'  => $srps['kukuruz_primese'],
            '_srps_lom'  => $srps['kukuruz_lom'],
            '_srps_defekt' => $srps['kukuruz_defekt'],
        );
        $this->_proracun->set_property($set_srps);

        //set bonifikacija params
        $bonifikacija = $this->get_bonifikacija();
        $set_bonifikacija = array(
            '_vlaga_donja'    => $bonifikacija['donja_kuvl'],
            '_vlaga_gornja'   => $bonifikacija['gornja_kuvl'],
            '_primesa_donja'  => $bonifikacija['donja_kupr'],
            '_primesa_gornja' => $bonifikacija['gornja_kupr'],
            '_lom_donja'      => $bonifikacija['donja_kulo'],
            '_lom_gornja'     => $bonifikacija['gornja_kulo'],
            '_defekt_donja'   => $bonifikacija['donja_kude'],
            '_defekt_gornja'  => $bonifikacija['gornja_kude']
        );
        $this->_proracun->set_property($set_bonifikacija);

        $this->_proracun->proracun_kukuruza();

        //
        $table = 'input_merkantila';
        $obj = array(
            'kalo_rastur'=>$this->_proracun->get_property('_kalo'),
            'tara'=>$this->_proracun->get_property('_tara'),
            'neto'=>$this->_proracun->get_property('_neto'),
            'dnv'=>$this->_proracun->get_property('_dnv'),
            'dnp' =>$this->_proracun->get_property('_dnp'),
            'dnl' =>$this->_proracun->get_property('_dnl'),
            'dnd' =>$this->_proracun->get_property('_dnd'),
            'n_x_vlaga'=>$this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese'=>$this->_proracun->get_property('_neto_x_primese'),
            'n_x_lom'=>$this->_proracun->get_property('_neto_x_lom'),
            'n_x_defekt'=>$this->_proracun->get_property('_neto_x_defekt'),
            'srps' => $this->_proracun->get_property('_srps'),
            'trosak_susenja'=> $this->_proracun->get_property('_trs'),
            'suvo_zrno'=> $this->_proracun->get_property('_suvo_zrno'),

        );

    echo'<pre>';
       print_r($obj);
       /* $where = 'input_merkantila_id='.$result['input_merkantila_id'];
        $this->model->update_values($table, $obj, $where);

        $date = new DateTime();

        $table = 'input_records';
            $obj = array('exit_date'=>$date->format('Y-m-d H:i:s'),
            );
        $where = 'input_id='.$result['input_id'];
        $result = $this->model->update_values($table, $obj, $where);
        echo json_encode(array('input_id'=>$result));*/
    }



    public function test_get(){
        $sql = 'SELECT input_records.input_id, input_records.document_br,input_records.input_merkantila_id, input_records.input_date,
                        input_merkantila.bruto, input_merkantila.vlaga, input_merkantila.primese, input_merkantila.hektolitar, input_merkantila.lom, input_merkantila.defekt,
                        clients.firm_name
                        FROM input_records
                        INNER JOIN input_merkantila ON (input_merkantila.input_merkantila_id = input_records.input_merkantila_id)
                        INNER JOIN clients ON (clients.client_id = input_records.client_id)
                        WHERE input_records.wearehouse_id= :wearehouse_id AND input_records.exit_date = :exit_date AND input_records.sort_of_goods_id= :sort_of_goods_id';
        $result = $this->model->get_values($sql,array(":wearehouse_id"=>"19", ":exit_date"=>"0000-00-00 00:00:00", ":sort_of_goods_id"=>"1"));
        print_r($result);
    }



    /*TESTING*/
    public function test_insert_merkantila(){
        $data = new stdClass();
        $data->defekt = 2;
        $data->lom = 4;
        //$data->hektolitar = 74;
        $data->bruto = 200;
        $data->primese = 1;
        $data->vlaga = 14;
        $goods_parametars = array();

        isset($data->defekt) ? $goods_parametars["defekt"]=$data->defekt : null ;
        isset($data->lom) ? $goods_parametars["lom"]=$data->lom : null;
        isset($data->hektolitar) ? $goods_parametars["hektolitar"]=$data->hektolitar : null;
        isset($data->bruto) ? $goods_parametars["bruto"]=$data->bruto  : null;
        isset($data->primese) ? $goods_parametars["primese"]=$data->primese  : null;
        isset($data->vlaga) ? $goods_parametars["vlaga"]=$data->vlaga  : null;

        $input_merkantila_id = $this->model->set_values('input_merkantila', $goods_parametars);
        $document_br = $this->set_document_number();
        echo $input_merkantila_id.', '. $document_br;
        $date = new DateTime();

        $input_info= array(
            "document_br"=>$document_br,
            "input_merkantila_id"=>$input_merkantila_id,
            "sort_of_goods_id"=>1,//
            "type_of_goods_id"=>1,//$data->type_of_goods_id,
            "goods_id"=>94,//$data->goods_id,
            "user_id"=>4,//Session::get('user_id');
            "wearehouse_id"=>19,//Session::get('wearehouse_id')
            "client_id"=> 1,//$data->client_id,
            "driver_name"=>"Marko Nikolic",//$data->
            "vehicle_registration"=> "Ns 345 Tt",//$data->
            "input_date"=> $date->format('Y-m-d H:i:s'),
        );

        $this->model->set_values('input_records',  $input_info);
    }


}
?>