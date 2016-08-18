<?php
class Otprema_Merkantila_Api extends Controller
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

    public function empty_result(){
        header('Content-Type: application/json');
        echo json_encode(array());
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_goods_type(){
        Ajax::ajaxCheck();
        header('Content-Type: application/json');
        $sql = "SELECT * FROM type_of_goods WHERE goods_type='kukuruz tel-kel' || goods_type='psenica tel-kel' || goods_type='kukuruz' || goods_type='psenica' || goods_type='suncokret' || goods_type='soja' || goods_type='jecam' || goods_type='uljana repica' || goods_type='sacma'";
        $result = $this->model->get_values($sql,$id=null);
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_goods_name($type_of_goods_id){
        Ajax::ajaxCheck();
        $sql = "SELECT * FROM goods WHERE type_of_goods_id= :type_of_goods_id";
        $obj = array(':type_of_goods_id'=>$type_of_goods_id);
        $result = $this->model->get_values($sql,$obj);
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function otprema_merkantila(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        header('Content-Type: application/json');
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){

            $document_br = $this->set_document_number();
            $date = new DateTime();
            $output_info= array(
                "document_br"          => $document_br,
                "user_id"              => Session::get('user_id'),
                "wearehouse_id"        => Session::get('wearehouse_id'),
                "client_id"            => $data->client_id,
                "driver_name"          => $data->driver_name,
                "vehicle_registration" => $data->driver_reg,
                "output_date"=> $date->format('Y-m-d H:i:s')
            );
           $output_id =  $this->model->set_values('output_records',  $output_info);

            //empty array $goods_parametars
            $goods_parametars = array();
            $goods_parametars["output_id"] = $output_id;
            $goods_parametars["sort_of_goods_id"] = 1;
            $goods_parametars["type_of_goods_id"] = $data->type_of_goods_id;
            $goods_parametars["goods_id"] = $data->goods_id;
            isset($data->tara) ? $goods_parametars["tara"]=$data->tara  : null;
            //set values from array $goods_parametars into mysql table input_merkantila input_merkantila and return merkantila_id
           $this->model->set_values('output_merkantila', $goods_parametars);

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
            $sql = "SELECT DATE_FORMAT(DATE(output_records.output_date),'%e-%c-%Y') as datum FROM output_records
                    INNER JOIN output_merkantila ON (output_merkantila.output_id = output_records.output_id)
                    WHERE output_records.wearehouse_id= :wearehouse_id AND output_merkantila.sort_of_goods_id= :sort_of_goods_id   GROUP BY DATE( output_records.output_date )";
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
            $output_date = date('Y-m-d', strtotime($data->datumOtpreme));
            $sql = "SELECT output_merkantila.type_of_goods_id, type_of_goods.goods_type FROM output_records
                    INNER JOIN output_merkantila ON (output_merkantila.output_id = output_records.output_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_merkantila.type_of_goods_id)
                    WHERE output_records.wearehouse_id= :wearehouse_id AND output_merkantila.sort_of_goods_id= :sort_of_goods_id AND output_merkantila.sort_of_goods_id= :sort_of_goods_id AND DATE(output_records.output_date)= :output_date AND output_records.stornirano='n' AND DATE(output_records.exit_date) != '0000-00-00'  GROUP BY type_of_goods.type_of_goods_id";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1', ':sort_of_goods_id'=>'1', ':output_date'=>$output_date);
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }

    }

    public function get_search_good_name(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
       // print_r($data);return false;
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){
            $output_date = date('Y-m-d', strtotime($data->datumOtpreme));
            $type_of_goods_id = $data->output_type_id;
            $sql = "SELECT goods.goods_id, goods.goods_name  FROM output_records
                    INNER JOIN output_merkantila ON (output_merkantila.output_id = output_records.output_id)
                    INNER JOIN goods ON (goods.goods_id = output_merkantila.goods_id)
                    WHERE output_records.wearehouse_id=:wearehouse_id
                    AND output_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND  DATE(output_records.output_date)= :output_date
                    AND output_merkantila.type_of_goods_id= :type_of_goods_id
                    AND DATE(output_records.exit_date) != '0000-00-00'
                    AND output_records.stornirano='n' GROUP BY goods_id";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1', ':output_date'=>$output_date, ':type_of_goods_id'=>$type_of_goods_id);
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }


    public function get_search_prijemnica(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        //print_r($data);
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){
            $output_date = date('Y-m-d', strtotime($data->datumOtpreme));
            $type_of_goods_id = $data->output_type_id;
            $goods_id = $data->goods_id;
            $sql = "SELECT
                    output_records.output_id,
                    goods.goods_id,
                    CONCAT(output_records.document_br, ' / ', YEAR(output_records.output_date)) As document_br ,
                    goods.goods_name,
                    clients.firm_name
                    FROM output_records
                    INNER JOIN output_merkantila ON (output_merkantila.output_id = output_records.output_id)
                    INNER JOIN goods ON (goods.goods_id = output_merkantila.goods_id)
                    INNER JOIN clients ON (clients.client_id = output_records.client_id)
                    WHERE output_records.wearehouse_id= :wearehouse_id
                    AND output_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND DATE(output_records.output_date)= :output_date
                    AND output_merkantila.type_of_goods_id= :type_of_goods_id
                    AND output_merkantila.goods_id= :goods_id
                    AND output_records.stornirano= :stornirano AND output_records.exit_date!= :exit_date
                    ORDER BY output_records.output_id DESC";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1', ':output_date'=>$output_date, ':type_of_goods_id'=>$type_of_goods_id, ':goods_id'=>$goods_id, ':stornirano'=>'n', ':exit_date'=>"0000-00-00 00:00:00" );
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);


        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function getEnabledDays(){
        Ajax::ajaxCheck();
        $sql = "SELECT DATE_FORMAT(DATE(output_date),'%e-%c-%Y') FROM output_records WHERE wearehouse_id= :wearehouse_id AND sort_of_goods_id= :sort_of_goods_id AND output_records.stornirano='n'   GROUP BY DATE(output_date)";
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

    public function prvo_merenje_merkantila($session_id=null){
        $data = json_decode(file_get_contents("php://input"));
        $session = $session_id==null ? (string) $data->session_id : (string) $session_id;
        $check_session = $this->check_logedIn($session);

        header('Content-Type: application/json');
        if( $check_session['login'] == 1){
            $user_id = Session::get('user_id');
            $sql='SELECT goods.goods_name,
                    output_records.output_id,
                    output_merkantila.type_of_goods_id,
                    output_records.driver_name,
                    output_records.vehicle_registration,
                    CONCAT(document_br, " / ", YEAR(output_date)) As document_br,
                    DATE_FORMAT(DATE(output_records.output_date),"%d.%m.%Y") AS date,
                    TIME(output_records.output_date) AS time,
                    output_merkantila.tara,
                    clients.firm_name
                    FROM output_records
                    INNER JOIN output_merkantila ON (output_merkantila.output_id = output_records.output_id)
                    INNER JOIN clients ON (clients.client_id = output_records.client_id)
                    INNER JOIN goods ON (goods.goods_id = output_merkantila.goods_id)
                    WHERE output_records.wearehouse_id= :wearehouse_id AND output_records.exit_date = :exit_date
                  AND output_merkantila.sort_of_goods_id= :sort_of_goods_id AND output_records.stornirano="n" ORDER BY output_records.output_id DESC';
            $result = $this->model->get_values($sql, array(":wearehouse_id"=>Session::get('wearehouse_id'), ":exit_date"=>"0000-00-00 00:00:00", ":sort_of_goods_id"=>"1"));
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    public function drugo_merenje_merkantila($session_id=null){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));

        $session_id = $session_id==null ? (string) $data->session_id : (string) $session_id;
        $check_session = $this->check_logedIn($session_id);
        $goods_parametars = array();
        header('Content-Type: application/json');
        //set values into array $goods_parametars
        isset($data->bruto) ? $goods_parametars["bruto"]=$data->bruto : 0.00 ;
        isset($data->defekt) ? $goods_parametars["defekt"]=$data->defekt : 0.00 ;
        isset($data->lom) ? $goods_parametars["lom"]=$data->lom : 0.00;
        isset($data->hektolitar) ? $goods_parametars["hektolitar"]=$data->hektolitar : 0.00;
        isset($data->primese) ? $goods_parametars["primese"]=$data->primese  : 0.00;
        isset($data->vlaga) ? $goods_parametars["vlaga"]=$data->vlaga  : 0.00;
        isset($data->protein) ? $goods_parametars["protein"]=$data->protein  : 0.0;
        isset($data->gluten) ? $goods_parametars["gluten"]=$data->gluten  : 0.0;
        isset($data->energija) ? $goods_parametars["energija"]=$data->energija  : 0.0;
        isset($data->br_padanja) ? $goods_parametars["br_padanja"]=$data->br_padanja  : 0;
        if( $check_session['login'] == 1){
            $sql='SELECT
                    output_records.output_id,
                    output_merkantila.tara,
                    type_of_goods.goods_type
                    FROM output_records
                    INNER JOIN output_merkantila ON(output_merkantila.output_id = output_records.output_id)
                    INNER JOIN type_of_goods ON(type_of_goods.type_of_goods_id = output_merkantila.type_of_goods_id)
                    WHERE output_records.wearehouse_id= :wearehouse_id AND output_records.output_id= :output_id AND output_records.stornirano="n"';
            $result = $this->model->get_values($sql,array(":wearehouse_id"=>Session::get('wearehouse_id'),":output_id"=>$data->output_id));
            $result = array_merge($result[0], $goods_parametars);
           // print_r($result); return false;
            if( $result['goods_type']==='kukuruz'){
                $this->obracun_kukuruza($result);
            } else if($result['goods_type']==='suncokret'){
                $this->obracun_suncokreta($result);
            }else if($result['goods_type']==='soja'){
                $this->obracun_soje($result);
            }else if($result['goods_type']==='uljana repica'){
                $this->obracun_uljane_repice($result);
            } else if($result['goods_type']==='psenica'){
                $this->obracun_psenice($result);
            }else if($result['goods_type']==='sacma'){
                $this->bez_obracuna($result);
            }else if($result['goods_type']==='kukuruz tel-kel'){
                $this->bez_obracuna($result);
            }else if($result['goods_type']==='psenica tel-kel'){
                $this->bez_obracuna($result);
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

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function get_srps(){
        $sql='SELECT * FROM srps WHERE id="1"';
        $srps = $this->model->get_values($sql, $id=null);
        return $srps[0];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function get_bonifikacija(){
        $sql='SELECT * FROM bonifikacija WHERE id="1"';
        $bonifikacija = $this->model->get_values($sql, $id=null);
        return $bonifikacija[0];
    }

    private function get_nacin_obracuna_vlage(){//_nacin_obracuna_vlage
        $sql='SELECT * FROM obracun_vlage WHERE id="1"';
        $bonifikacija = $this->model->get_values($sql, $id=null);
        return $bonifikacija[0];
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function bez_obracuna($result){
        $neto = $result['bruto'] - $result['tara'];
        $obj = array(
            'bruto'=>$result['bruto'],
            'neto'=>$neto,
            'vlaga'=>$result['vlaga'],
            'primese'=>$result['primese'],
            'hektolitar'=>$result['hektolitar'],
            'protein'=>$result['protein'],
            'gluten'=>$result['gluten'],
            'energija'=>$result['energija'],
            'br_padanja'=>$result['br_padanja']
        );

        $this->_update_results($obj, $result['output_id'], $result['output_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------


   private function obracun_psenice($result){
        $data_set = array(
            '_kultura'    => $result['goods_type'],
            '_bruto'      =>$result['bruto'],
            '_tara'       => $result['tara'],
            '_vlaga'      => $result['vlaga'],
            '_primese'    => $result['primese'],
            '_hektolitar' => $result['hektolitar'],
            '_kalo_koeficient' => 0.000
        );
        $this->_proracun->set_property($data_set);

        //nacin obracunavanja vlage
        $nacin_vlage = $this->get_nacin_obracuna_vlage();
        $this->_proracun->set_property('_nacin_obracuna_vlage', $nacin_vlage['psenica_obracun']);

        $tablica = $this->get_tablica_psenica();
        $set_tablica = array(
            '_a14'   => $tablica['ps14'],
            '_a14_5' => $tablica['ps14_50'],
            '_a15'   => $tablica['ps15'],
            '_a15_5' => $tablica['ps15_50'],
            '_a16'   => $tablica['ps16'],
            '_a16_5' => $tablica['ps16_50'],
            '_a17'   => $tablica['ps17'],
            '_a17_5' => $tablica['ps17_50'],
            '_a18'   => $tablica['ps18'],
            '_a18_5' => $tablica['ps18_50'],
            '_a19'   => $tablica['ps19'],
            '_a19_5' => $tablica['ps19_50']
        );
        $this->_proracun->set_property($set_tablica);

        //set srps params
        $srps = $this->get_srps();
        $set_srps = array(
            '_srps_vlaga'      => $srps['psenica_vlaga'],
            '_srps_primese'    => $srps['psenica_primese'],
            '_srps_hektolitar' => $srps['psenica_hektolitar']
        );
        $this->_proracun->set_property($set_srps);

        //set bonifikacija params
        $bonifikacija = $this->get_bonifikacija();
        $set_bonifikacija = array(
            '_vlaga_donja'       => $bonifikacija['donja_vlps'],
            '_vlaga_gornja'      => $bonifikacija['gornja_vlps'],
            '_primesa_donja'     => $bonifikacija['donja_prps'],
            '_primesa_gornja'    => $bonifikacija['gornja_prps'],
            '_hektolitar_donja'  => $bonifikacija['donja_pshl_bo'],
            '_hektolitar_gornja' => $bonifikacija['gornja_pshl_bo']
        );
        $this->_proracun->set_property($set_bonifikacija);

        $this->_proracun->proracun_psenice();


        $table = 'output_merkantila';
        $obj = array(
            'kalo_rastur'     =>$this->_proracun->get_property('_kalo'),
            'bruto'           =>$this->_proracun->get_property('_bruto'),
            'neto'            =>$this->_proracun->get_property('_neto'),
            'vlaga'           =>$this->_proracun->get_property('_vlaga'),
            'primese'         =>$this->_proracun->get_property('_primese'),
            'hektolitar'      =>$this->_proracun->get_property('_hektolitar'),
            'protein'         =>$result['protein'],
            'gluten'          =>$result['gluten'],
            'energija'        =>$result['energija'],
            'br_padanja'      =>$result['br_padanja'],
            'dnv'             =>$this->_proracun->get_property('_dnv'),
            'dnp'             =>$this->_proracun->get_property('_dnp'),
            //'dnh'             =>$this->_proracun->get_property('_dnh'),
            'n_x_vlaga'       =>$this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese'     =>$this->_proracun->get_property('_neto_x_primese'),
            'n_x_hektolitar'  =>$this->_proracun->get_property('_neto_x_hektolitar'),
            'srps'            => $this->_proracun->get_property('_srps'),
            //'trosak_susenja'  => $this->_proracun->get_property('_trs'),
            //'suvo_zrno'       => $this->_proracun->get_property('_suvo_zrno'),
        );

        $this->_update_results($obj, $result['output_id'], $result['output_id']);
    }


    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function obracun_uljane_repice($result){
        $data_set = array(
            '_kultura' => $result['goods_type'],
            '_bruto'   =>$result['bruto'],
            '_tara'    => $result['tara'],
            '_vlaga'   => $result['vlaga'],
            '_primese' => $result['primese']
        );
        $this->_proracun->set_property($data_set);

        //nacin obracunavanja vlage
        $nacin_vlage = $this->get_nacin_obracuna_vlage();
        $this->_proracun->set_property('_nacin_obracuna_vlage', $nacin_vlage['uljana_obracun']);

        $srps = $this->get_srps();
        $set_srps = array(
            '_srps_vlaga'   => $srps['uljana_vlaga'],
            '_srps_primese' => $srps['uljana_primese']
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


        $obj = array(
            'bruto'       => $this->_proracun->get_property('_bruto'),
            'neto'        => $this->_proracun->get_property('_neto'),
            'vlaga'       => $this->_proracun->get_property('_vlaga'),
            'primese'     => $this->_proracun->get_property('_primese'),
            'dnv'         => $this->_proracun->get_property('_dnv'),
            'dnp'         => $this->_proracun->get_property('_dnp'),
            'n_x_vlaga'   => $this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese' => $this->_proracun->get_property('_neto_x_primese'),
            'srps'        => $this->_proracun->get_property('_srps'),
        );

        $this->_update_results($obj, $result['output_id'], $result['output_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function obracun_soje($result){

        $data_set = array(
            '_kultura' => $result['goods_type'],
            '_bruto'   => $result['bruto'],
            '_tara'    => $result['tara'],
            '_vlaga'   => $result['vlaga'],
            '_primese' => $result['primese']
        );
        $this->_proracun->set_property($data_set);

        //nacin obracunavanja vlage
        $nacin_vlage = $this->get_nacin_obracuna_vlage();
        $this->_proracun->set_property('_nacin_obracuna_vlage', $nacin_vlage['soja_obracun']);

        $srps = $this->get_srps();
        $set_srps = array(
            '_srps_vlaga'   => $srps['soja_vlaga'],
            '_srps_primese' => $srps['soja_primese']
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


        $obj = array(
            'tara'        => $this->_proracun->get_property('_tara'),
            'neto'        => $this->_proracun->get_property('_neto'),
            'vlaga'       => $this->_proracun->get_property('_vlaga'),
            'primese'     => $this->_proracun->get_property('_primese'),
            'dnv'         => $this->_proracun->get_property('_dnv'),
            'dnp'         => $this->_proracun->get_property('_dnp'),
            'n_x_vlaga'   => $this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese' => $this->_proracun->get_property('_neto_x_primese'),
            'srps'        => $this->_proracun->get_property('_srps'),
        );
        $this->_update_results($obj, $result['output_id'], $result['output_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function obracun_suncokreta($result){

        $data_set = array(
            '_kultura' => $result['goods_type'],
            '_bruto'   => $result['bruto'],
            '_tara'    => $result['tara'],
            '_vlaga'   => $result['vlaga'],
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


        $obj = array(
            'bruto'       =>$this->_proracun->get_property('_bruto'),
            'neto'        =>$this->_proracun->get_property('_neto'),
            'vlaga'       =>$this->_proracun->get_property('_vlaga'),
            'primese'     =>$this->_proracun->get_property('_primese'),
            'vlaga'       =>$this->_proracun->get_property('_vlaga'),
            'primese'     =>$this->_proracun->get_property('_primese'),
            'dnv'         =>$this->_proracun->get_property('_dnv'),
            'dnp'         =>$this->_proracun->get_property('_dnp'),
            'n_x_vlaga'   =>$this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese' =>$this->_proracun->get_property('_neto_x_primese'),
            'srps'        => $this->_proracun->get_property('_srps'),
        );
        $this->_update_results($obj, $result['output_id'], $result['output_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function obracun_kukuruza($result){
        $data_set = array(
            '_kultura' => $result['goods_type'],
            '_bruto'   =>$result['bruto'],
            '_tara'    => $result['tara'],
            '_vlaga'   => $result['vlaga'],
            '_primese' => $result['primese'],
            '_lom'     => $result['lom'],
            '_defekt'  => $result['defekt']
        );
        $this->_proracun->set_property($data_set);

        //nacin obracunavanja vlage
        $nacin_vlage = $this->get_nacin_obracuna_vlage();
        $this->_proracun->set_property('_nacin_obracuna_vlage', $nacin_vlage['kukuruz_obracun']);

        $tablica = $this->get_tablica();
        $set_tablica = array(
            '_a14'   => $tablica['ku14'],
            '_a14_5' => $tablica['ku14_5'],
            '_a15'   => $tablica['ku15'],
            '_a15_5' => $tablica['ku15_5'],
            '_a16'   => $tablica['ku16'],
            '_a16_5' => $tablica['ku16_5'],
            '_a17'   => $tablica['ku17'],
            '_a17_5' => $tablica['ku17_5'],
            '_a18'   => $tablica['ku18'],
            '_a18_5' => $tablica['ku18_5'],
            '_a19'   => $tablica['ku19'],
            '_a19_5' => $tablica['ku19_5'],
            '_a20'   => $tablica['ku20'],
            '_a20_5' => $tablica['ku20_5'],
            '_a21'   => $tablica['ku21'],
            '_a21_5' => $tablica['ku21_5'],
            '_a22'   => $tablica['ku22'],
            '_a22_5' => $tablica['ku22_5'],
            '_a23'   => $tablica['ku23'],
            '_a23_5' => $tablica['ku23_5'],
            '_a24'   => $tablica['ku24'],
            '_a24_5' => $tablica['ku24_5'],
            '_a25'   => $tablica['ku25'],
            '_a25_5' => $tablica['ku25_5'],
            '_a26'   => $tablica['ku26'],
            '_a26_5' => $tablica['ku26_5'],
            '_a27'   => $tablica['ku27'],
            '_a27_5' => $tablica['ku27_5'],
            '_a28'   => $tablica['ku28'],
            '_a28_5' => $tablica['ku28_5'],
            '_a29'   => $tablica['ku29'],
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



        $obj = array(
            'kalo_rastur'    => $this->_proracun->get_property('_kalo'),
            'bruto'          => $this->_proracun->get_property('_bruto'),
            'neto'           => $this->_proracun->get_property('_neto'),
            'vlaga'          => $this->_proracun->get_property('_vlaga'),
            'primese'        => $this->_proracun->get_property('_primese'),
            'lom'            => $this->_proracun->get_property('_lom'),
            'defekt'         => $this->_proracun->get_property('_defekt'),
            'dnv'            => $this->_proracun->get_property('_dnv'),
            'dnp'            => $this->_proracun->get_property('_dnp'),
            'dnl'            => $this->_proracun->get_property('_dnl'),
            'dnd'            => $this->_proracun->get_property('_dnd'),
            'n_x_vlaga'      => $this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese'    => $this->_proracun->get_property('_neto_x_primese'),
            'n_x_lom'        => $this->_proracun->get_property('_neto_x_lom'),
            'n_x_defekt'     => $this->_proracun->get_property('_neto_x_defekt'),
            'srps'           => $this->_proracun->get_property('_srps'),
            'trosak_susenja' => $this->_proracun->get_property('_trs'),
            'suvo_zrno'      => $this->_proracun->get_property('_suvo_zrno'),
        );

        $this->_update_results($obj, $result['output_id'], $result['output_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function _update_results($obj, $output_id, $output_id){

        $table = 'output_merkantila';
        $where = 'output_id='.$output_id;
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
                goods.goods_cypher,
                goods.goods_name,
                output_records.output_id,
                output_records.driver_name,
                output_records.vehicle_registration,
                CONCAT(output_records.document_br, " / ", YEAR(output_records.output_date)) As document_br,
                YEAR (output_records.output_date) as year,
                DATE_FORMAT(DATE(output_records.output_date),"%d.%m.%Y") AS date,
                TIME(output_records.output_date) AS time,
                output_merkantila.bruto,
                output_merkantila.vlaga,
                output_merkantila.primese,
                output_merkantila.hektolitar,
                output_merkantila.lom,
                output_merkantila.defekt,
                output_merkantila.protein,
                output_merkantila.energija,
                output_merkantila.gluten,
                output_merkantila.br_padanja,
                output_merkantila.kalo_rastur,
                output_merkantila.tara,
                output_merkantila.neto,
                output_merkantila.dnv,
                output_merkantila.dnp,
                output_merkantila.dnd,
                output_merkantila.dnh,
                output_merkantila.dnl,
                output_merkantila.srps,
                output_merkantila.trosak_susenja,
                output_merkantila.suvo_zrno,
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
                FROM output_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = output_records.user_id)
                INNER JOIN output_merkantila ON (output_merkantila.output_id = output_records.output_id)
                INNER JOIN clients ON (clients.client_id = output_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = output_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_merkantila.type_of_goods_id)
                WHERE output_records.wearehouse_id= :wearehouse_id AND output_records.exit_date !="0000-00-00 00:00:00" AND output_merkantila.sort_of_goods_id= :sort_of_goods_id  AND output_records.stornirano= :stornirano
                ORDER BY output_records.exit_date DESC LIMIT 1';
            $result = $this->model->get_values($sql, array(":wearehouse_id"=>Session::get('wearehouse_id'), ":sort_of_goods_id"=>"1",":stornirano"=>"n"));
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
                output_records.output_id,
                output_records.driver_name,
                output_records.vehicle_registration,
                CONCAT(output_records.document_br, " / ", YEAR(output_records.output_date)) As document_br,
                YEAR (output_records.output_date) as year,
                DATE_FORMAT(DATE(output_records.output_date),"%d.%m.%Y") AS date,
                TIME(output_records.output_date) AS time,
                output_merkantila.bruto,
                output_merkantila.vlaga,
                output_merkantila.primese,
                output_merkantila.hektolitar,
                output_merkantila.lom,
                output_merkantila.defekt,
                output_merkantila.protein,
                output_merkantila.energija,
                output_merkantila.gluten,
                output_merkantila.br_padanja,
                output_merkantila.kalo_rastur,
                output_merkantila.tara,
                output_merkantila.neto,
                output_merkantila.dnv,
                output_merkantila.dnp,
                output_merkantila.dnd,
                output_merkantila.dnh,
                output_merkantila.dnl,
                output_merkantila.srps,
                output_merkantila.trosak_susenja,
                output_merkantila.suvo_zrno,
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
                FROM output_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = output_records.user_id)
                INNER JOIN output_merkantila ON (output_merkantila.output_id = output_records.output_id)
                INNER JOIN clients ON (clients.client_id = output_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = output_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_merkantila.type_of_goods_id)
                WHERE output_records.wearehouse_id= :wearehouse_id
                AND output_merkantila.sort_of_goods_id= :sort_of_goods_id
                AND output_records.output_id= :output_id AND output_records.stornirano= :stornirano AND output_records.exit_date!= :exit_date';
            $result = $this->model->get_values($sql, array(":wearehouse_id"=>Session::get('wearehouse_id'), ":sort_of_goods_id"=>"1", ":output_id"=>$data->output_id, ":stornirano"=>"n", "exit_date"=>"0000-00-00 00:00:00"));
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
       /* $where = 'output_merkantila_id='.$result['input_merkantila_id'];
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