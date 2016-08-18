<?php
class Pregled_Otpreme_Merkantile_Api extends Controller{
    public function __construct(){
        parent::__construct();
    }

    public function empty_load(){
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
            return array('login'=>0);
        } else {
            return array('login'=>1);
        }
    }

    public function get_search_otprema($ses){
        //type_of_goods_id=1&goods_id=94&client_id=1&datum_od=01.01.2016&datum_do=03.01.2016
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        $wearehouse = 'output_records.wearehouse_id= :wearehouse_id';
        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? ' AND output_records.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND output_records.goods_id= :goods_id' : '';
        $client_id = isset($_GET['client_id']) ? ' AND output_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) ? ' AND DATE(output_records.output_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) ? ' AND DATE(output_records.output_date)<= :datum_do' : '';
        $statement = $type_of_goods_id." ".$goods_id." ".$client_id." ".$datum_od." ".$datum_do;
       // print_r($statement);
        $params = array();
        $params[':wearehouse_id'] = Session::get('wearehouse_id');
        foreach($_GET as $key => $value){

            if($key!=='session_id' || $key!=='url' ){
                if ($key==="type_of_goods_id"){
                    $params[':type_of_goods_id'] = $value;
                } elseif ($key==="goods_id"){
                    $params[':goods_id'] = $value;
                }elseif ($key === "client_id"){
                    $params[':client_id'] = $value;
                } elseif($key==="datum_od"){
                    $params[':datum_od'] = date('Y-m-d', strtotime($value));
                } elseif ($key==="datum_do") {
                    $params[':datum_do'] = date('Y-m-d', strtotime($value));
                }
            }
        }
       /* print_r($params).'<br />';
        echo $statement;*/

        header('Content-Type: application/json');

        if( $check_session['login'] == 1){

            $sql = "SELECT
                goods.goods_cypher,
                goods.goods_name,
                output_records.output_id,
                output_records.driver_name,
                output_records.vehicle_registration,
                CONCAT(output_records.driver_name, ' / ', output_records.vehicle_registration) AS vozac,
                CONCAT(output_records.document_br, ' / ', YEAR(output_records.output_date)) As document_br,
                output_records.output_merkantila_id,
                YEAR (output_records.output_date) as year,
                /*DATE_FORMAT(DATE(output_records.output_date),'%d.%m.%Y') AS date,*/
                DATE(output_records.output_date) AS date,
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
                CONCAT(users.name, ' ', users.surname) AS storekeeper
                FROM output_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = output_records.user_id)
                INNER JOIN output_merkantila ON (output_merkantila.output_merkantila_id = output_records.output_merkantila_id)
                INNER JOIN clients ON (clients.client_id = output_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = output_records.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_records.type_of_goods_id)
                WHERE  ".$wearehouse." ".$type_of_goods_id." ".$goods_id." ".$client_id." ".$datum_od." ".$datum_do."  AND output_records.exit_date !='0000-00-00 00:00:00' AND output_records.sort_of_goods_id='1' AND output_records.stornirano='n' ORDER BY output_records.output_id";

            $result = $this->model->get_values($sql,$params);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }

    }

    public function get_search_good_type(){
        Ajax::ajaxCheck();
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        header('Content-Type: application/json');
        if( $check_session['login'] == 1){
            $sql = "SELECT output_records.type_of_goods_id, type_of_goods.goods_type FROM output_records
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_records.type_of_goods_id)
                    WHERE output_records.wearehouse_id= :wearehouse_id AND output_records.sort_of_goods_id= :sort_of_goods_id
                    AND output_records.exit_date !='0000-00-00 00:00:00' AND output_records.stornirano='n' GROUP BY output_records.type_of_goods_id";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1');
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }

    }


    public function get_search_good_name(){
        Ajax::ajaxCheck();
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        $type_of_goods_id = $_GET['type_of_goods_id']; //checking if session exists
        header('Content-Type: application/json');
        if( $check_session['login'] == 1){
            $sql = "SELECT goods.goods_id, goods.goods_name FROM output_records
                    INNER JOIN goods ON (goods.goods_id = output_records.goods_id)
                    WHERE output_records.wearehouse_id= :wearehouse_id AND output_records.sort_of_goods_id= :sort_of_goods_id AND output_records.exit_date !='0000-00-00 00:00:00'
                    AND output_records.type_of_goods_id= :type_of_goods_id
                    AND output_records.stornirano='n' GROUP BY goods.goods_id";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1', ':type_of_goods_id'=>$type_of_goods_id);
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }

    }

    public function get_search_good_client(){
        Ajax::ajaxCheck();
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        $type_of_goods_id = $_GET['type_of_goods_id']; //checking if session exists
        $goods_id = $_GET['goods_id'];
        //print_r($_GET);
        header('Content-Type: application/json');
        if( $check_session['login'] == 1){
            $sql = "SELECT clients.client_id, clients.firm_name FROM output_records
                    INNER JOIN clients ON (clients.client_id = output_records.client_id)
                    WHERE output_records.wearehouse_id= :wearehouse_id AND output_records.sort_of_goods_id= :sort_of_goods_id
                    AND output_records.type_of_goods_id= :type_of_goods_id  AND output_records.goods_id= :goods_id AND output_records.exit_date !='0000-00-00 00:00:00'
                    AND  output_records.stornirano='n'  GROUP BY clients.client_id";
            $obj = array(':wearehouse_id'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1', ':type_of_goods_id'=>$type_of_goods_id, ':goods_id'=>$goods_id);
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }

    }



    public function get_search_otprema_total(){
        //type_of_goods_id=1&goods_id=94&client_id=1&datum_od=01.01.2016&datum_do=03.01.2016
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        $wearehouse = 'output_records.wearehouse_id= :wearehouse_id';
        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? ' AND output_records.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND output_records.goods_id= :goods_id' : '';
        $client_id = isset($_GET['client_id']) ? ' AND output_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) ? ' AND DATE(output_records.output_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) ? ' AND DATE(output_records.output_date)<= :datum_do' : '';
        $statement = $type_of_goods_id." ".$goods_id." ".$client_id." ".$datum_od." ".$datum_do;
        $params = array();
        $params[':wearehouse_id'] = Session::get('wearehouse_id');
        foreach($_GET as $key => $value){

            if($key!=='session_id' || $key!=='url' ){
                if ($key==="type_of_goods_id"){
                    $params[':type_of_goods_id'] = $value;
                } elseif ($key==="goods_id"){
                    $params[':goods_id'] = $value;
                }elseif ($key === "client_id"){
                    $params[':client_id'] = $value;
                } elseif($key==="datum_od"){
                    $params[':datum_od'] = date('Y-m-d', strtotime($value));
                } elseif ($key==="datum_do") {
                    $params[':datum_do'] = date('Y-m-d', strtotime($value));
                }
            }
        }
        /* print_r($params).'<br />';
         echo $statement;*/

        header('Content-Type: application/json');

        if( $check_session['login'] == 1){

            $sql = "SELECT SUM(output_merkantila.neto) AS neto_total,
                SUM(output_merkantila.n_x_vlaga) AS x_vlaga,
                SUM(output_merkantila.n_x_primese) AS x_primese,
                SUM(output_merkantila.n_x_lom) AS x_lom,
                SUM(output_merkantila.n_x_defekt)AS x_defekt,
                SUM(output_merkantila.n_x_hektolitar) AS x_hektolitar,
                SUM(output_merkantila.srps) AS srps_total,
                SUM(output_merkantila.trosak_susenja) AS trosak_susenja_total,
                SUM(output_merkantila.suvo_zrno) AS suvo_zrno_total
                FROM output_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = output_records.user_id)
                INNER JOIN output_merkantila ON (output_merkantila.output_merkantila_id = output_records.output_merkantila_id)
                INNER JOIN clients ON (clients.client_id = output_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = output_records.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_records.type_of_goods_id)
                WHERE  ".$wearehouse." ".$type_of_goods_id." ".$goods_id." ".$client_id." ".$datum_od." ".$datum_do."  AND output_records.exit_date !='0000-00-00 00:00:00' AND output_records.sort_of_goods_id='1' AND output_records.stornirano='n' ORDER BY output_records.output_id";

            $result = $this->model->get_values($sql,$params);
            $result = $result[0];

            $suma = array();
            $suma['neto_total'] = $result['neto_total'] === NULL ? 0.00 : number_format($result['neto_total'], 2, '.', ',');
            $suma['ponder_vlage'] =  $result['x_vlaga'] === NULL ? 0.00 : number_format($result['x_vlaga'] / $result['neto_total'], 2);
            $suma['ponder_primesa'] = $result['x_primese'] === NULL ? 0.00 :  number_format($result['x_primese']  / $result['neto_total'], 2);
            $suma['ponder_loma'] = $result['x_lom'] === NULL ? 0.00 : number_format($result['x_lom'] / $result['neto_total'], 2);
            $suma['ponder_defekta'] =  $result['x_defekt'] === NULL ? 0.00 : number_format($result['x_defekt'] / $result['neto_total'], 2);
            $suma['ponder_hektolitra'] = $result['x_hektolitar'] === NULL ? 0.00 :  number_format($result['x_hektolitar'] / $result['neto_total'], 2);
            $suma['srps_total'] = $result['srps_total'] === NULL ? 0.00 : number_format($result['srps_total'], 2, '.', ',');
            $suma['trosak_susenja_total'] = $result['trosak_susenja_total'] === NULL ? 0.00 : number_format($result['trosak_susenja_total'], 2, '.', ',');
            $suma['suvo_zrno_total'] = $result['suvo_zrno_total'] === NULL ? 0.00 : number_format($result['suvo_zrno_total'], 2, '.', ',');
            echo json_encode($suma, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }

    }

    public function storniraj_dokument($input_id){
        Ajax::ajaxCheck();
        $table = 'input_records';
        $data = array(
            'stornirano' => 'y'
        );
        $where = 'input_id="'.$input_id.'"';
        $this->model->update_values($table, $data, $where);
        header('Content-Type: application/json');
        echo json_encode(array('success'=>1));
    }



    public function getExcell()
    {
        //var_dump($_GET);
        //type_of_goods_id=1&goods_id=94&client_id=1&datum_od=01.01.2016&datum_do=03.01.2016
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        $wearehouse = 'output_records.wearehouse_id= :wearehouse_id';
        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? ' AND output_records.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND output_records.goods_id= :goods_id' : '';
        $client_id = isset($_GET['client_id']) ? ' AND output_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) ? ' AND DATE(output_records.output_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) ? ' AND DATE(output_records.output_date)<= :datum_do' : '';
        $statement = $type_of_goods_id." ".$goods_id." ".$client_id." ".$datum_od." ".$datum_do;
        // print_r($statement);
        $params = array();
        $params[':wearehouse_id'] = Session::get('wearehouse_id');
        foreach($_GET as $key => $value){

            if($key!=='session_id' || $key!=='url' ){
                if ($key==="type_of_goods_id"){
                    $params[':type_of_goods_id'] = $value;
                } elseif ($key==="goods_id"){
                    $params[':goods_id'] = $value;
                }elseif ($key === "client_id"){
                    $params[':client_id'] = $value;
                } elseif($key==="datum_od"){
                    $params[':datum_od'] = date('Y-m-d', strtotime($value));
                } elseif ($key==="datum_do") {
                    $params[':datum_do'] = date('Y-m-d', strtotime($value));
                }
            }
        }
        /* print_r($params).'<br />';
         echo $statement;*/

        header('Content-Type: application/json');


        header('Content-Type: application/json');

        if ($check_session['login'] == 1) {

            $sql = "SELECT
                goods.goods_cypher,
                goods.goods_name,
                output_records.output_id,
                output_records.driver_name,
                output_records.vehicle_registration,
                CONCAT(output_records.driver_name, ' / ', output_records.vehicle_registration) AS vozac,
                CONCAT(output_records.document_br, ' / ', YEAR(output_records.output_date)) As document_br,
                output_records.output_merkantila_id,
                YEAR (output_records.output_date) as year,
                /*DATE_FORMAT(DATE(output_records.output_date),'%d.%m.%Y') AS date,*/
                DATE(output_records.output_date) AS date,
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
                CONCAT(users.name, ' ', users.surname) AS storekeeper
                FROM output_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = output_records.user_id)
                INNER JOIN output_merkantila ON (output_merkantila.output_merkantila_id = output_records.output_merkantila_id)
                INNER JOIN clients ON (clients.client_id = output_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = output_records.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_records.type_of_goods_id)
                WHERE  " . $wearehouse . " " . $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do . "  AND output_records.exit_date !='0000-00-00 00:00:00' AND output_records.sort_of_goods_id='1' AND output_records.stornirano='n' ORDER BY output_records.output_id";

            $result = $this->model->get_values($sql, $params);

            $sql = "SELECT SUM(output_merkantila.neto) AS neto_total,
                SUM(output_merkantila.n_x_vlaga) AS x_vlaga,
                SUM(output_merkantila.n_x_primese) AS x_primese,
                SUM(output_merkantila.n_x_lom) AS x_lom,
                SUM(output_merkantila.n_x_defekt)AS x_defekt,
                SUM(output_merkantila.n_x_hektolitar) AS x_hektolitar,
                SUM(output_merkantila.kalo_rastur) AS suma_rastur,
                SUM(output_merkantila.dnv) AS suma_dnv,
                SUM(output_merkantila.dnp) AS suma_dnp,
                SUM(output_merkantila.dnh) AS suma_dnh,
                SUM(output_merkantila.dnl) AS suma_dnl,
                SUM(output_merkantila.dnd) AS suma_dnd,
                SUM(output_merkantila.srps) AS srps_total,
                SUM(output_merkantila.srps) AS srps_total,
                SUM(output_merkantila.trosak_susenja) AS trosak_susenja_total,
                SUM(output_merkantila.suvo_zrno) AS suvo_zrno_total
                FROM output_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = output_records.user_id)
                INNER JOIN output_merkantila ON (output_merkantila.output_merkantila_id = output_records.output_merkantila_id)
                INNER JOIN clients ON (clients.client_id = output_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = output_records.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_records.type_of_goods_id)
                WHERE  " . $wearehouse . " " . $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do . "  AND output_records.exit_date !='0000-00-00 00:00:00' AND output_records.sort_of_goods_id='1' AND output_records.stornirano='n' ORDER BY output_records.output_id";

            $result_sum = $this->model->get_values($sql, $params);
            $result_sum = $result_sum[0];

            $suma = array();
            $suma['neto_total'] = $result_sum['neto_total'] === NULL ? 0.00 : $result_sum['neto_total'];
            $suma['ponder_vlage'] = $result_sum['x_vlaga'] === NULL ? 0.00 : $result_sum['x_vlaga'] / $result_sum['neto_total'];
            $suma['suma_rastur'] = $result_sum['suma_rastur'] === NULL ? 0.00 : $result_sum['suma_rastur'];
            $suma['suma_dnv'] = $result_sum['suma_dnv'] === NULL ? 0.00 : $result_sum['suma_dnv'];
            $suma['suma_dnp'] = $result_sum['suma_dnp'] === NULL ? 0.00 : $result_sum['suma_dnp'];
            $suma['suma_dnh'] = $result_sum['suma_dnh'] === NULL ? 0.00 : $result_sum['suma_dnh'];
            $suma['suma_dnl'] = $result_sum['suma_dnl'] === NULL ? 0.00 : $result_sum['suma_dnl'];
            $suma['suma_dnd'] = $result_sum['suma_dnd'] === NULL ? 0.00 : $result_sum['suma_dnd'];
            $suma['ponder_primesa'] = $result_sum['x_primese'] === NULL ? 0.00 : $result_sum['x_primese'] / $result_sum['neto_total'];
            $suma['ponder_loma'] = $result_sum['x_lom'] === NULL ? 0.00 : $result_sum['x_lom'] / $result_sum['neto_total'];
            $suma['ponder_defekta'] = $result_sum['x_defekt'] === NULL ? 0.00 : $result_sum['x_defekt'] / $result_sum['neto_total'];
            $suma['ponder_hektolitra'] = $result_sum['x_hektolitar'] === NULL ? 0.00 : $result_sum['x_hektolitar'] / $result_sum['neto_total'];
            $suma['srps_total'] = $result_sum['srps_total'] === NULL ? 0.00 : $result_sum['srps_total'];
            $suma['trosak_susenja_total'] = $result_sum['trosak_susenja_total'] === NULL ? 0.00 : $result_sum['trosak_susenja_total'];
            $suma['suvo_zrno_total'] = $result_sum['suvo_zrno_total'] === NULL ? 0.00 : $result_sum['suvo_zrno_total'];





            //echo json_encode($result, JSON_NUMERIC_CHECK);
            // echo Session::check();
            $this->printExcel($_GET, $result, $suma);
            //  print_r($result);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }


    }



    public function printExcel($get, $result, $suma){
        $kultura = (int) $get['type_of_goods_id'];

        if(isset($get['goods_id'])) {
            $sql = "SELECT goods_name FROM goods WHERE goods_id= :goods_id";
            $good_name = $this->model->get_values($sql, array(':goods_id' => $get['goods_id']));
            $good_name = ' ZA ROBU '.strtoupper ( $good_name[0]['goods_name'] );
        } else {
            $good_name = '';
        }

        if(isset($get['client_id'])) {
            $sql = "SELECT firm_name FROM clients WHERE client_id= :client_id";
            $client_name = $this->model->get_values($sql, array(':client_id' => $get['client_id']));
            $client_name = ' OD '.strtoupper ( $client_name[0]['firm_name'] );
        } else {
            $client_name = '';
        }
        if(isset($get['datum_od'])) {
            $datum_od = ' ZA DATUM '.$get['datum_od'];
        } else {
            $datum_od = '';
        }
        if(isset($get['datum_od']) && isset($get['datum_do'])) {
            $datum_od = ' ZA DATUM '.$get['datum_od'].' DO '.$get['datum_do'];
        } else {
            $datum_od = '';
        }


        $xml = new ExcelWriterXML('Pregled merkantile .xls');
        $xml->docAuthor('Raiffeisen Agro');

        $format = $xml->addStyle('StyleHeader');
        $format->fontBold();

        $format4 = $xml->addStyle('my style');
        $format4->alignHorizontal('Center');
        $format4->alignVertical('Center');
        $format4->fontSize('11');
        $format4->fontBold();
        $format4->border('All','1','#666666','Continuous');
        $format4->bgColor('#91B3DB');

        $format5 = $xml->addStyle('vraptext-top');
        $format5->alignWraptext();
        $format5->alignVertical('Center');
        $format5->alignHorizontal('Center');
        $format5->fontBold();
        $format5->bgColor('#D7E3F2');
        $format5->fontSize('8');
        $format5->border('All','1','#666666','Continuous');

        $format51 = $xml->addStyle('za_obracun');
        $format51->alignWraptext();
        $format51->alignVertical('Center');
        $format51->alignHorizontal('Center');
        $format51->fontBold();
        $format51->bgColor('#339900');
        $format51->fontSize('8');
        $format51->fontColor('White');
        $format51->border('All','1','#FFFFFF','Continuous');

        $formatp = $xml->addStyle('reza-top');
        $formatp->alignWraptext();
        $formatp->alignVertical('Center');
        $formatp->alignHorizontal('Right');
        $formatp->bgColor('#D7E3F2');
        $formatp->fontSize('8');
        $formatp->numberingFormat();
        $formatp->border('All','1','#666666','Continuous');

        $format6 = $xml->addStyle('za_ostalo');
        $format6->alignWraptext();
        $format6->alignHorizontal('Left');
        $format6->alignVertical('Center');
        $format6->redovanFormat();
        $format6->border('All','1','#666666','Continuous');
        $format6->fontSize('8');

        $format61 = $xml->addStyle('za_nesto');
        $format61->alignWraptext();
        $format61->alignHorizontal('Right');
        $format61->alignVertical('Center');
        $format61->numberingFormat();
        $format61->border('All','1','#666666','Continuous');
        $format61->fontSize('8');

        $format7 = $xml->addStyle('za_redovan');
        $format7->alignWraptext();
        $format7->alignHorizontal('Right');
        $format7->alignVertical('Center');
        $format7->redovanFormat();
        $format7->border('All','1','#666666','Continuous');
        $format7->fontSize('9');

        $format8 = $xml->addStyle('za_id');
        $format8->alignWraptext();
        $format8->alignHorizontal('Left');
        $format8->alignVertical('Center');
        $format8->redovanFormat();
        $format8->border('All','1','#666666','Continuous');
        $format8->fontSize('8');
        $format8->bgColor('#D7E3F2');

        $sheet = $xml->addSheet('Spisak robe');
        $red = 4;
        $rez = count($result);
        for($i=$red;$i<$rez;$i++){
            $sheet->rowHeight($i,'18');
        }
        $sheet->rowHeight($rez,'18');
        $sheet->rowHeight($rez+1,'18');
        $sheet->rowHeight($rez+2,'18');
        $sheet->rowHeight($rez+3,'18');
        $sheet->columnWidth(1,'60');
        $sheet->columnWidth(2,'60');
        $sheet->columnWidth(3,'30');
        $sheet->columnWidth(4,'100');
        $sheet->columnWidth(5,'130');
        $sheet->columnWidth(6,'130');
        $sheet->columnWidth(7,'120');
        $sheet->columnWidth(8,'130');
        $sheet->columnWidth(9,'100');
        $sheet->columnWidth(10,'60');
        $sheet->columnWidth(11,'60');
        $sheet->columnWidth(12,'60');



        $sheet->writeString(1,1,'IZVOD STANJA PRIJEMA '.$good_name.' U MAGACINU '.strtoupper (Session::get('wearehouse_name')).''.$client_name.''.$datum_od ,$format4);
        $sheet->cellMerge(1,1,10,1);
        $sheet->writeString(1,12,'Ukupno',$format5);
        $sheet->writeNumber(2,12,$suma['neto_total'],$formatp);
        if($kultura === 1 || $kultura === 2 || $kultura === 3 ||  $kultura === 4 || $kultura === 5 ||  $kultura === 12){
            $sheet->writeString(1,13,'Ponder %',$format5);
            $sheet->writeString(1,14,'Ukupno/kg',$format5);
            $sheet->writeString(1,15,'Ponder %',$format5);
            $sheet->writeString(1,16,'Ukupno/kg',$format5);
            $sheet->writeNumber(2,13,$suma['ponder_vlage'],$formatp);
            $sheet->writeNumber(2,14,$suma['suma_dnv'],$formatp);
            $sheet->writeNumber(2,15,$suma['ponder_primesa'],$formatp);
            $sheet->writeNumber(2,16,$suma['suma_dnp'],$formatp);

        }
        if($kultura === 3 ||  $kultura === 4 || $kultura === 12){
            $sheet->writeString(1,17,'Ukupno/kg',$format5);
            $sheet->writeNumber(2,17,$suma['srps_total'],$formatp);
        }
        if( $kultura === 2 || $kultura === 5){

            $sheet->writeString(1,17,'Ponder %',$format5);
            $sheet->writeString(1,18,'Ukupno/kg',$format5);
            $sheet->writeString(1,19,'Ukupno/kg',$format5);
            $sheet->writeNumber(2,17,$suma['ponder_hektolitra'],$formatp);
            $sheet->writeNumber(2,18,$suma['suma_dnh'],$formatp);
            $sheet->writeNumber(2,19,$suma['srps_total'],$formatp);
        }
        if( $kultura === 1 ){
            $sheet->writeString(1,17,'Ponder %',$format5);
            $sheet->writeString(1,18,'Ukupno/kg',$format5);
            $sheet->writeString(1,19,'Ponder %',$format5);
            $sheet->writeString(1,20,'Ukupno/kg',$format5);
            $sheet->writeString(1,21,'Ukupno/kg',$format5);
            $sheet->writeString(1,22,'Ukupno/kg',$format5);
            $sheet->writeString(1,23,'Ukupno/kg',$format5);
            $sheet->writeString(1,24,'Ukupno/kg',$format5);
            $sheet->writeNumber(2,17,$suma['ponder_loma'],$formatp);
            $sheet->writeNumber(2,18,$suma['suma_dnl'],$formatp);
            $sheet->writeNumber(2,19,$suma['ponder_defekta'],$formatp);
            $sheet->writeNumber(2,20,$suma['suma_dnd'],$formatp);
            $sheet->writeNumber(2,21,$suma['suma_rastur'],$formatp);
            $sheet->writeNumber(2,22,$suma['srps_total'],$formatp);
            $sheet->writeNumber(2,23,$suma['trosak_susenja_total'],$formatp);
            $sheet->writeNumber(2,24,$suma['suvo_zrno_total'],$formatp);
        }



        $sheet->writeString(3,1,'BR/PR',$format5);
        $sheet->writeString(3,2,'DATUM',$format5);
        $sheet->writeString(3,3,'SAT',$format5);
        $sheet->writeString(3,4,'KULTURA',$format5);
        $sheet->writeString(3,5,'IME KUPCA',$format5);
        $sheet->writeString(3,6,'ADRESA KUPCA',$format5);
        $sheet->writeString(3,7,'MESTO KUPCA',$format5);
        $sheet->writeString(3,8,'VOZAČ',$format5);
        $sheet->writeString(3,9,'REGISTRACIJA',$format5);
        $sheet->writeString(3,10,'BRUTO',$format5);
        $sheet->writeString(3,11,'TARA',$format5);
        $sheet->writeString(3,12,'NETO',$format51);
        if($kultura === 1 || $kultura === 2 || $kultura === 3 ||  $kultura === 4 || $kultura === 5 ||  $kultura === 12){
            $sheet->writeString(3,13,'VLAGA',$format51);
            $sheet->writeString(3,14,'ODNV',$format51);
            $sheet->writeString(3,15,'PRIMESE',$format51);
            $sheet->writeString(3,16,'ODNP',$format51);
        }
        if($kultura === 3 ||  $kultura === 4 || $kultura === 12){
            $sheet->writeString(3,17,'SRPS',$format51);
        }
        if( $kultura === 2 || $kultura === 5){

            $sheet->writeString(3,17,'HTL',$format51);
            $sheet->writeString(3,18,'ODNH',$format51);
            $sheet->writeString(3,19,'SRPS',$format51);
        }

        if( $kultura === 1 ){
            $sheet->writeString(3,17,'LOM',$format51);
            $sheet->writeString(3,18,'ODNL',$format51);
            $sheet->writeString(3,19,'DEFEKT',$format51);
            $sheet->writeString(3,20,'ODND',$format51);
            $sheet->writeString(3,21,'SIROV RASTUR',$format51);
            $sheet->writeString(3,22,'SRPS',$format51);
            $sheet->writeString(3,23,'TROSAK SUSENJA',$format51);
            $sheet->writeString(3,24,'SUVO ZRNO',$format51);
        }



        $row_br=3;
        foreach($result as $key=>$value){
            $row_br  = $row_br +1;
            $sheet->writeString($row_br,1,$value['document_br'],$format8);
            $sheet->writeString($row_br,2,date('d.m.Y', strtotime($value['date'])),$format6);
            $sheet->writeString($row_br,3,date('H:i', strtotime($value['time'])),$format6);
            $sheet->writeString($row_br,4,$value['goods_name'],$format6);
            $sheet->writeString($row_br,5,$value['firm_name'],$format6);
            $sheet->writeString($row_br,6,$value['client_address'],$format6);
            $sheet->writeString($row_br,7,$value['place_name'].' '.$value['post_number'],$format6);
            $sheet->writeString($row_br,8,$value['driver_name'],$format6);
            $sheet->writeString($row_br,9,$value['vehicle_registration'],$format6);
            $sheet->writeNumber($row_br,10,$value['bruto'],$format61);
            $sheet->writeNumber($row_br,11,$value['tara'],$format61);
            $sheet->writeNumber($row_br,12,$value['neto'],$format61);
            if($kultura === 1 || $kultura === 2 || $kultura === 3 ||  $kultura === 4 || $kultura === 5 ||  $kultura === 12){
                $sheet->writeNumber($row_br,13,$value['vlaga'],$format61);
                $sheet->writeNumber($row_br,14,$value['dnv'],$format61);
                $sheet->writeNumber($row_br,15,$value['primese'],$format61);
                $sheet->writeNumber($row_br,16,$value['dnp'],$format61);
            }
            if($kultura === 3 ||  $kultura === 4 || $kultura === 12){
                $sheet->writeNumber($row_br,17,$value['srps'],$format61);
            }
            if( $kultura === 2 || $kultura === 5){
                $sheet->writeNumber($row_br,17,$value['hektolitar'],$format61);
                $sheet->writeNumber($row_br,18,$value['dnh'],$format61);
                $sheet->writeNumber($row_br,19,$value['srps'],$format61);
            }
            if( $kultura === 1 ){
                $sheet->writeNumber($row_br,17,$value['lom'],$format61);
                $sheet->writeNumber($row_br,18,$value['dnl'],$format61);
                $sheet->writeNumber($row_br,19,$value['defekt'],$format61);
                $sheet->writeNumber($row_br,20,$value['dnd'],$format61);
                $sheet->writeNumber($row_br,21,$value['kalo_rastur'],$format61);
                $sheet->writeNumber($row_br,22,$value['srps'],$format61);
                $sheet->writeNumber($row_br,23,$value['trosak_susenja'],$format61);
                $sheet->writeNumber($row_br,24,$value['suvo_zrno'],$format61);
            }


        };
        $xml->sendHeaders();
        $xml->writeData();

    }


}
?>
