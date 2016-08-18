<?php
class Pregled_Prijema_Merkantile_Api extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_proracun = new Proracun();
       /* Session::init();
        print_r($_SESSION);
        die;*/
    }

    public function empty_load()
    {
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
            return array('login' => 0);
        } else {
            return array('login' => 1);
        }
    }

    private function check_logedIn_admin()
    {
        Session::init();
        $logged = Session::get('loggedIn');
        $status = Session::get('role');
        if ($logged == false || $status != 'Administrator' && $status != 'Redovan korisnik' && $status != 'Logistika') {
            unset($logged);
            unset($status);
            Session::destroy();
            return array('login' => 0);
        } else {
            return array('login' => 1);
        }
    }

    public function get_search_prijem($ses)
    {
        //type_of_goods_id=1&goods_id=94&client_id=1&datum_od=01.01.2016&datum_do=03.01.2016
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        $wearehouse = 'input_records.wearehouse_id= :wearehouse_id';
        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? ' AND input_merkantila.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND input_merkantila.goods_id= :goods_id' : '';
        $client_id = isset($_GET['client_id']) ? ' AND input_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) ? ' AND DATE(input_records.input_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) ? ' AND DATE(input_records.input_date)<= :datum_do' : '';
        $statement = $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do;
        $params = array();
        $params[':wearehouse_id'] = Session::get('wearehouse_id');
        foreach ($_GET as $key => $value) {

            if ($key !== 'session_id' || $key !== 'url') {
                if ($key === "type_of_goods_id") {
                    $params[':type_of_goods_id'] = $value;
                } elseif ($key === "goods_id") {
                    $params[':goods_id'] = $value;
                } elseif ($key === "client_id") {
                    $params[':client_id'] = $value;
                } elseif ($key === "datum_od") {
                    $params[':datum_od'] = date('Y-m-d', strtotime($value));
                } elseif ($key === "datum_do") {
                    $params[':datum_do'] = date('Y-m-d', strtotime($value));
                }
            }
        }
        /* print_r($params).'<br />';
         echo $statement;*/

        header('Content-Type: application/json');

        if ($check_session['login'] == 1) {

            $sql = "SELECT
                goods.goods_cypher,
                goods.goods_name,
                input_records.input_id,
                input_records.driver_name,
                input_records.vehicle_registration,
                CONCAT(input_records.driver_name, ' / ', input_records.vehicle_registration) AS vozac,
                CONCAT(input_records.document_br, ' / ', YEAR(input_records.input_date)) As document_br,
                YEAR (input_records.input_date) as year,
                /*DATE_FORMAT(DATE(input_records.input_date),'%d.%m.%Y') AS date,*/
                DATE(input_records.input_date) AS date,
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
                CONCAT(users.name, ' ', users.surname) AS storekeeper
                FROM input_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = input_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = input_records.user_id)
                INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                WHERE  " . $wearehouse . " " . $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND input_records.exit_date !='0000-00-00 00:00:00'
                AND input_merkantila.sort_of_goods_id='1' AND input_records.stornirano='n' ORDER BY input_records.input_id";

            $result = $this->model->get_values($sql, $params);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }

    public function get_search_good_type()
    {
        Ajax::ajaxCheck();
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        header('Content-Type: application/json');
        if ($check_session['login'] == 1) {
            $sql = "SELECT input_merkantila.type_of_goods_id, type_of_goods.goods_type FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                    WHERE input_records.wearehouse_id= :wearehouse_id AND input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND input_records.exit_date !='0000-00-00 00:00:00' AND input_records.stornirano='n' GROUP BY input_merkantila.type_of_goods_id";
            $obj = array(':wearehouse_id' => Session::get('wearehouse_id'), ':sort_of_goods_id' => '1');
            $result = $this->model->get_values($sql, $obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }


    //-------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_search_good_type_admin()
    {
       // Ajax::ajaxCheck();
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        header('Content-Type: application/json');
        if ($check_session['login'] == 1) {
            $sql = "SELECT input_merkantila.type_of_goods_id, type_of_goods.goods_type FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                    WHERE input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND input_records.exit_date !='0000-00-00 00:00:00' AND input_records.stornirano='n' GROUP BY input_merkantila.type_of_goods_id";
            $obj = array(':sort_of_goods_id' => '1');
            $result = $this->model->get_values($sql, $obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------


    public function get_search_good_name()
    {
        Ajax::ajaxCheck();
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        $type_of_goods_id = $_GET['type_of_goods_id']; //checking if session exists
        header('Content-Type: application/json');
        if ($check_session['login'] == 1) {
            $sql = "SELECT goods.goods_id, goods.goods_name FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                    WHERE input_records.wearehouse_id= :wearehouse_id
                    AND input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND input_records.exit_date !='0000-00-00 00:00:00'
                    AND input_merkantila.type_of_goods_id= :type_of_goods_id
                    AND input_records.stornirano='n' GROUP BY goods.goods_id";
            $obj = array(':wearehouse_id' => Session::get('wearehouse_id'), ':sort_of_goods_id' => '1', ':type_of_goods_id' => $type_of_goods_id);
            $result = $this->model->get_values($sql, $obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }


    //----------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_search_good_name_admin()
    {
        Ajax::ajaxCheck();
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        $type_of_goods_id = $_GET['type_of_goods_id']; //checking if session exists
        header('Content-Type: application/json');
        if ($check_session['login'] == 1) {
            $sql = "SELECT goods.goods_id, goods.goods_name FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                    WHERE input_merkantila.sort_of_goods_id= :sort_of_goods_id
                     AND input_records.exit_date !='0000-00-00 00:00:00'
                    AND input_merkantila.type_of_goods_id= :type_of_goods_id
                    AND input_records.stornirano='n' GROUP BY goods.goods_id";
            $obj = array(':sort_of_goods_id' => '1', ':type_of_goods_id' => $type_of_goods_id);
            $result = $this->model->get_values($sql, $obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }

    public function get_search_good_wearehouses_admin(){
        //var_dump($_GET);return false;
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        $type_of_goods_id = $_GET['type_of_goods_id']; //checking if session exists
        $goods_id = $_GET['goods_id'];
        //print_r($_GET);
        header('Content-Type: application/json');
        if ($check_session['login'] == 1) {

            $sqlc = "SELECT wearehouses.wearehouse_id, wearehouses.wearehouse_name FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN wearehouses ON (wearehouses.wearehouse_id = input_records.wearehouse_id)
                    WHERE input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND input_merkantila.type_of_goods_id= :type_of_goods_id
                    AND input_merkantila.goods_id= :goods_id
                    AND input_records.exit_date !='0000-00-00 00:00:00'
                    AND  input_records.stornirano='n'  GROUP BY wearehouses.wearehouse_id";
            $objc = array(':sort_of_goods_id' => '1', ':type_of_goods_id' => $type_of_goods_id, ':goods_id' => $goods_id);
            $wearehouses = $this->model->get_values($sqlc, $objc);

            $sqlw = "SELECT clients.client_id, clients.firm_name FROM input_records
                    INNER JOIN clients ON (clients.client_id = input_records.client_id)
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    WHERE input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND input_merkantila.type_of_goods_id= :type_of_goods_id
                    AND input_merkantila.goods_id= :goods_id
                    AND input_records.exit_date !='0000-00-00 00:00:00'
                    AND  input_records.stornirano='n'
                    GROUP BY clients.client_id";
            $objw = array(':sort_of_goods_id' => '1', ':type_of_goods_id' => $type_of_goods_id, ':goods_id' => $goods_id);
            $clients = $this->model->get_values($sqlw, $objw);

            echo json_encode(array('clients'=>$clients, 'wearehouses'=>$wearehouses), JSON_NUMERIC_CHECK);

        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }
    }

    //----------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_search_good_client_admin()
    {
        Ajax::ajaxCheck();
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        $type_of_goods_id = $_GET['type_of_goods_id']; //checking if session exists
        $goods_id = $_GET['goods_id'];

        if(isset($_GET['wearehouse_id'])){
            $wearehouse = 'input_records.wearehouse_id= :wearehouse_id AND ';
        }
        header('Content-Type: application/json');
        if ($check_session['login'] == 1) {
            $sql = "SELECT clients.client_id, clients.firm_name FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN clients ON (clients.client_id = input_records.client_id)
                    WHERE ". $wearehouse ."  input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND input_merkantila.type_of_goods_id= :type_of_goods_id
                    AND input_records.goods_id= :goods_id AND input_records.exit_date !='0000-00-00 00:00:00' AND  input_records.stornirano='n'  GROUP BY clients.client_id";
            $obj = array(':sort_of_goods_id' => '1', ':type_of_goods_id' => $type_of_goods_id, ':goods_id' => $goods_id);
            if(isset($_GET['wearehouse_id'])) {
                $obj[':wearehouse_id'] = $_GET['wearehouse_id'];
            }
            $result = $this->model->get_values($sql, $obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }


    public function get_search_good_client()
    {
        Ajax::ajaxCheck();
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        $type_of_goods_id = $_GET['type_of_goods_id']; //checking if session exists
        $goods_id = $_GET['goods_id'];
        header('Content-Type: application/json');
        if ($check_session['login'] == 1) {
            $sql = "SELECT clients.client_id, clients.firm_name FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN clients ON (clients.client_id = input_records.client_id)
                    WHERE input_records.wearehouse_id= :wearehouse_id
                    AND input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND input_merkantila.type_of_goods_id= :type_of_goods_id
                    AND input_merkantila.goods_id= :goods_id
                    AND input_records.exit_date !='0000-00-00 00:00:00' AND  input_records.stornirano='n'  GROUP BY clients.client_id";
            $obj = array(':wearehouse_id' => Session::get('wearehouse_id'),':sort_of_goods_id' => '1', ':type_of_goods_id' => $type_of_goods_id, ':goods_id' => $goods_id);
            $result = $this->model->get_values($sql, $obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }


    public function get_search_prijem_total()
    {
        //type_of_goods_id=1&goods_id=94&client_id=1&datum_od=01.01.2016&datum_do=03.01.2016
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        $wearehouse = 'input_records.wearehouse_id= :wearehouse_id';
        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? ' AND input_merkantila.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND input_merkantila.goods_id= :goods_id' : '';
        $client_id = isset($_GET['client_id']) ? ' AND input_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) ? ' AND DATE(input_records.input_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) ? ' AND DATE(input_records.input_date)<= :datum_do' : '';
        $statement = $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do;
        $params = array();
        $params[':wearehouse_id'] = Session::get('wearehouse_id');
        foreach ($_GET as $key => $value) {

            if ($key !== 'session_id' || $key !== 'url') {
                if ($key === "type_of_goods_id") {
                    $params[':type_of_goods_id'] = $value;
                } elseif ($key === "goods_id") {
                    $params[':goods_id'] = $value;
                } elseif ($key === "client_id") {
                    $params[':client_id'] = $value;
                } elseif ($key === "datum_od") {
                    $params[':datum_od'] = date('Y-m-d', strtotime($value));
                } elseif ($key === "datum_do") {
                    $params[':datum_do'] = date('Y-m-d', strtotime($value));
                }
            }
        }
        /* print_r($params).'<br />';
         echo $statement;*/

        header('Content-Type: application/json');

        if ($check_session['login'] == 1) {

            $sql = "SELECT SUM(input_merkantila.neto) AS neto_total,
                SUM(input_merkantila.n_x_vlaga) AS x_vlaga,
                SUM(input_merkantila.n_x_primese) AS x_primese,
                SUM(input_merkantila.n_x_lom) AS x_lom,
                SUM(input_merkantila.n_x_defekt)AS x_defekt,
                SUM(input_merkantila.n_x_hektolitar) AS x_hektolitar,
                SUM(input_merkantila.srps) AS srps_total,
                SUM(input_merkantila.trosak_susenja) AS trosak_susenja_total,
                SUM(input_merkantila.suvo_zrno) AS suvo_zrno_total
                FROM input_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = input_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = input_records.user_id)
                INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                WHERE  " . $wearehouse . " " . $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND input_records.exit_date !='0000-00-00 00:00:00' AND input_merkantila.sort_of_goods_id='1' AND input_records.stornirano='n' ORDER BY input_records.input_id";

            $result = $this->model->get_values($sql, $params);
            $result = $result[0];

            $suma = array();
            $suma['neto_total'] = $result['neto_total'] === NULL ? 0.00 : number_format($result['neto_total'], 2, '.', ',');
            $suma['ponder_vlage'] = $result['x_vlaga'] === NULL ? 0.00 : number_format($result['x_vlaga'] / $result['neto_total'], 2);
            $suma['ponder_primesa'] = $result['x_primese'] === NULL ? 0.00 : number_format($result['x_primese'] / $result['neto_total'], 2);
            $suma['ponder_loma'] = $result['x_lom'] === NULL ? 0.00 : number_format($result['x_lom'] / $result['neto_total'], 2);
            $suma['ponder_defekta'] = $result['x_defekt'] === NULL ? 0.00 : number_format($result['x_defekt'] / $result['neto_total'], 2);
            $suma['ponder_hektolitra'] = $result['x_hektolitar'] === NULL ? 0.00 : number_format($result['x_hektolitar'] / $result['neto_total'], 2);
            $suma['srps_total'] = $result['srps_total'] === NULL ? 0.00 : number_format($result['srps_total'], 2, '.', ',');
            $suma['trosak_susenja_total'] = $result['trosak_susenja_total'] === NULL ? 0.00 : number_format($result['trosak_susenja_total'], 2, '.', ',');
            $suma['suvo_zrno_total'] = $result['suvo_zrno_total'] === NULL ? 0.00 : number_format($result['suvo_zrno_total'], 2, '.', ',');
            echo json_encode($suma, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }

    /*public function storniraj_dokument($input_id)
    {
        Ajax::ajaxCheck();
        $table = 'input_records';
        $data = array(
            'stornirano' => 'y'
        );
        $where = 'input_id="' . $input_id . '"';
        $this->model->update_values($table, $data, $where);
        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }*/

    //----------------------------------------------------------------------------------------------------------------------------------------------------

    public function storniraj_dokument()
    {
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        // print_r($data);return false;
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $table = 'input_records';
            $date = new DateTime();
            $new_data = array(
                'stornirano'       => 'y',
                'stornirano_komentar'  => $data->napomena,
                'stornirano_datum' => $date = $date->format('Y-m-d H:i:s'),
                "stornirao_id"     => Session::get('user_id')
            );
            $where = 'input_id="' . $data->input_id . '"';
            $this->model->update_values($table, $new_data, $where);
            header('Content-Type: application/json');
            echo json_encode(array('success' => 1));
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }


    public function get_search_prijem_total_admin()
    {

        //type_of_goods_id=1&goods_id=94&client_id=1&datum_od=01.01.2016&datum_do=03.01.2016
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? 'input_merkantila.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND input_merkantila.goods_id= :goods_id' : '';
        $wearehouse = isset($_GET['wearehouse_id']) ? ' AND input_records.wearehouse_id= :wearehouse_id' : '';
        $client_id = isset($_GET['client_id']) ? ' AND input_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) ? ' AND DATE(input_records.input_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) ? ' AND DATE(input_records.input_date)<= :datum_do' : '';
        $statement = $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do;
        $params = array();
        // $params[':wearehouse_id'] = Session::get('wearehouse_id');
        foreach ($_GET as $key => $value) {

            if ($key !== 'session_id' || $key !== 'url') {
                if ($key === "type_of_goods_id") {
                    $params[':type_of_goods_id'] = $value;
                } elseif ($key === "goods_id") {
                    $params[':goods_id'] = $value;
                } elseif ($key === "wearehouse_id") {
                    $params[':wearehouse_id'] = $value;
                } elseif ($key === "client_id") {
                    $params[':client_id'] = $value;
                } elseif ($key === "datum_od") {
                    $params[':datum_od'] = date('Y-m-d', strtotime($value));
                } elseif ($key === "datum_do") {
                    $params[':datum_do'] = date('Y-m-d', strtotime($value));
                }
            }
        }

        header('Content-Type: application/json');

        if ($check_session['login'] === 1) {

            $sql = "SELECT SUM(input_merkantila.neto) AS neto_total,
                SUM(input_merkantila.n_x_vlaga) AS x_vlaga,
                SUM(input_merkantila.n_x_primese) AS x_primese,
                SUM(input_merkantila.n_x_lom) AS x_lom,
                SUM(input_merkantila.n_x_defekt)AS x_defekt,
                SUM(input_merkantila.n_x_hektolitar) AS x_hektolitar,
                SUM(input_merkantila.srps) AS srps_total,
                SUM(input_merkantila.trosak_susenja) AS trosak_susenja_total,
                SUM(input_merkantila.suvo_zrno) AS suvo_zrno_total
                FROM input_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = input_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = input_records.user_id)
                INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                WHERE  " . $type_of_goods_id . " " . $goods_id . " " . $wearehouse . " " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND input_records.exit_date !='0000-00-00 00:00:00'
                AND input_merkantila.sort_of_goods_id='1'
                AND input_records.stornirano='n'
                ORDER BY input_records.input_id";

            $result = $this->model->get_values($sql, $params);
            $result = $result[0];

            $suma = array();
            $suma['neto_total'] = $result['neto_total'] === NULL ? 0.00 : number_format($result['neto_total'], 2, '.', ',');
            $suma['ponder_vlage'] = $result['x_vlaga'] === NULL ? 0.00 : number_format($result['x_vlaga'] / $result['neto_total'], 2);
            $suma['ponder_primesa'] = $result['x_primese'] === NULL ? 0.00 : number_format($result['x_primese'] / $result['neto_total'], 2);
            $suma['ponder_loma'] = $result['x_lom'] === NULL ? 0.00 : number_format($result['x_lom'] / $result['neto_total'], 2);
            $suma['ponder_defekta'] = $result['x_defekt'] === NULL ? 0.00 : number_format($result['x_defekt'] / $result['neto_total'], 2);
            $suma['ponder_hektolitra'] = $result['x_hektolitar'] === NULL ? 0.00 : number_format($result['x_hektolitar'] / $result['neto_total'], 2);
            $suma['srps_total'] = $result['srps_total'] === NULL ? 0.00 : number_format($result['srps_total'], 2, '.', ',');
            $suma['trosak_susenja_total'] = $result['trosak_susenja_total'] === NULL ? 0.00 : number_format($result['trosak_susenja_total'], 2, '.', ',');
            $suma['suvo_zrno_total'] = $result['suvo_zrno_total'] === NULL ? 0.00 : number_format($result['suvo_zrno_total'], 2, '.', ',');
            echo json_encode($suma, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }


    public function get_search_prijem_admin()
    {
        //type_of_goods_id=1&goods_id=94&client_id=1&datum_od=01.01.2016&datum_do=03.01.2016
        $check_session = $this->check_logedIn_admin(); //checking if session exists

        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? 'input_merkantila.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND input_merkantila.goods_id= :goods_id' : '';
        $wearehouse = isset($_GET['wearehouse_id']) ? ' AND input_records.wearehouse_id= :wearehouse_id' : '';
        $client_id = isset($_GET['client_id']) ? ' AND input_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) ? ' AND DATE(input_records.input_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) ? ' AND DATE(input_records.input_date)<= :datum_do' : '';
        $statement = $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do;
        $params = array();
       // $params[':wearehouse_id'] = Session::get('wearehouse_id');
        foreach ($_GET as $key => $value) {

            if ($key !== 'session_id' || $key !== 'url') {
                if ($key === "type_of_goods_id") {
                    $params[':type_of_goods_id'] = $value;
                } elseif ($key === "goods_id") {
                    $params[':goods_id'] = $value;
                } elseif ($key === "wearehouse_id") {
                    $params[':wearehouse_id'] = $value;
                } elseif ($key === "client_id") {
                    $params[':client_id'] = $value;
                } elseif ($key === "datum_od") {
                    $params[':datum_od'] = date('Y-m-d', strtotime($value));
                } elseif ($key === "datum_do") {
                    $params[':datum_do'] = date('Y-m-d', strtotime($value));
                }
            }
        }
        /* print_r($params).'<br />';
         echo $statement;*/

        header('Content-Type: application/json');

        if ($check_session['login'] == 1) {

            $sql = "SELECT
                goods.goods_cypher,
                goods.goods_name,
                input_records.input_id,
                input_records.driver_name,
                input_records.vehicle_registration,
                CONCAT(input_records.driver_name, ' / ', input_records.vehicle_registration) AS vozac,
                CONCAT(input_records.document_br, ' / ', YEAR(input_records.input_date)) As document_br,
                YEAR (input_records.input_date) as year,
                /*DATE_FORMAT(DATE(input_records.input_date),'%d.%m.%Y') AS date,*/
                DATE(input_records.input_date) AS date,
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
                CONCAT(users.name, ' ', users.surname) AS storekeeper
                FROM input_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = input_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = input_records.user_id)
                INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                WHERE  " . $type_of_goods_id . " " . $goods_id . " " . $wearehouse . " " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND input_records.exit_date !='0000-00-00 00:00:00'
                AND input_merkantila.sort_of_goods_id='1'
                AND input_records.stornirano='n'
                ORDER BY input_records.input_id";

            $result = $this->model->get_values($sql, $params);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }






    public function getExcell()
    {
        //var_dump($_GET);
        $check_session = $this->check_logedIn($_GET['session_id']); //checking if session exists
        $wearehouse = 'input_records.wearehouse_id= :wearehouse_id';
        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? ' AND input_merkantila.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND input_merkantila.goods_id= :goods_id' : '';
        $client_id = isset($_GET['client_id']) ? ' AND input_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) ? ' AND DATE(input_records.input_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) ? ' AND DATE(input_records.input_date)<= :datum_do' : '';
        $statement = $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do;
        $params = array();
        $params[':wearehouse_id'] = Session::get('wearehouse_id');
        foreach ($_GET as $key => $value) {

            if ($key !== 'session_id' || $key !== 'url') {
                if ($key === "type_of_goods_id") {
                    $params[':type_of_goods_id'] = $value;
                } elseif ($key === "goods_id") {
                    $params[':goods_id'] = $value;
                } elseif ($key === "client_id") {
                    $params[':client_id'] = $value;
                } elseif ($key === "datum_od") {
                    $params[':datum_od'] = date('Y-m-d', strtotime($value));
                } elseif ($key === "datum_do") {
                    $params[':datum_do'] = date('Y-m-d', strtotime($value));
                }
            }
        }
        /* print_r($params).'<br />';
         echo $statement;*/

        header('Content-Type: application/json');

        if ($check_session['login'] == 1) {

            $sql = "SELECT
                goods.goods_cypher,
                goods.goods_name,
                input_records.input_id,
                input_records.driver_name,
                input_records.vehicle_registration,
                CONCAT(input_records.document_br, ', ', YEAR(input_records.input_date)) As document_br,
                YEAR (input_records.input_date) as year,
                /*DATE_FORMAT(DATE(input_records.input_date),'%d.%m.%Y') AS date,*/
                DATE(input_records.input_date) AS date,
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
                CONCAT(users.name, ' ', users.surname) AS storekeeper
                FROM input_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = input_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = input_records.user_id)
                INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                WHERE  " . $wearehouse . " " . $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND input_records.exit_date !='0000-00-00 00:00:00'
                AND input_merkantila.sort_of_goods_id='1'
                AND input_records.stornirano='n'
                ORDER BY input_records.input_id";

            $result = $this->model->get_values($sql, $params);

            $sql = "SELECT SUM(input_merkantila.neto) AS neto_total,
                SUM(input_merkantila.n_x_vlaga) AS x_vlaga,
                SUM(input_merkantila.n_x_primese) AS x_primese,
                SUM(input_merkantila.n_x_lom) AS x_lom,
                SUM(input_merkantila.n_x_defekt)AS x_defekt,
                SUM(input_merkantila.n_x_hektolitar) AS x_hektolitar,
                SUM(input_merkantila.kalo_rastur) AS suma_rastur,
                SUM(input_merkantila.dnv) AS suma_dnv,
                SUM(input_merkantila.dnp) AS suma_dnp,
                SUM(input_merkantila.dnh) AS suma_dnh,
                SUM(input_merkantila.dnl) AS suma_dnl,
                SUM(input_merkantila.dnd) AS suma_dnd,
                SUM(input_merkantila.srps) AS srps_total,
                SUM(input_merkantila.trosak_susenja) AS trosak_susenja_total,
                SUM(input_merkantila.suvo_zrno) AS suvo_zrno_total
                FROM input_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = input_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = input_records.user_id)
                INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                WHERE  " . $wearehouse . " " . $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND input_records.exit_date !='0000-00-00 00:00:00'
                AND input_merkantila.sort_of_goods_id='1'
                AND input_records.stornirano='n'
                ORDER BY input_records.input_id";

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




    public function getExcellAdmin()
    {
        //var_dump($_GET);
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? ' input_merkantila.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND input_merkantila.goods_id= :goods_id' : '';
        $wearehouse_id = isset($_GET['wearehouse_id']) ? ' AND input_records.wearehouse_id= :wearehouse_id' : '';
        $client_id = isset($_GET['client_id']) ? ' AND input_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) ? ' AND DATE(input_records.input_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) ? ' AND DATE(input_records.input_date)<= :datum_do' : '';
        $statement = $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do;
        $params = array();
       // $params[':wearehouse_id'] = Session::get('wearehouse_id');
        foreach ($_GET as $key => $value) {

            if ($key !== 'session_id' || $key !== 'url') {
                if ($key === "type_of_goods_id") {
                    $params[':type_of_goods_id'] = $value;
                } elseif ($key === "goods_id") {
                    $params[':goods_id'] = $value;
                } elseif ($key === "wearehouse_id") {
                    $params[':wearehouse_id'] = $value;
                } elseif ($key === "client_id") {
                    $params[':client_id'] = $value;
                } elseif ($key === "datum_od") {
                    $params[':datum_od'] = date('Y-m-d', strtotime($value));
                } elseif ($key === "datum_do") {
                    $params[':datum_do'] = date('Y-m-d', strtotime($value));
                }
            }
        }
        /* print_r($params).'<br />';
         echo $statement;*/

        header('Content-Type: application/json');

        if ($check_session['login'] == 1) {

            $sql = "SELECT
                goods.goods_cypher,
                goods.goods_name,
                input_records.input_id,
                input_records.driver_name,
                input_records.vehicle_registration,
                CONCAT(input_records.document_br, ', ', YEAR(input_records.input_date)) As document_br,
                YEAR (input_records.input_date) as year,
                /*DATE_FORMAT(DATE(input_records.input_date),'%d.%m.%Y') AS date,*/
                DATE(input_records.input_date) AS date,
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
                CONCAT(users.name, ' ', users.surname) AS storekeeper
                FROM input_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = input_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = input_records.user_id)
                INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                WHERE   " . $type_of_goods_id . " " . $goods_id . " " . $wearehouse_id . "  " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND input_records.exit_date !='0000-00-00 00:00:00'
                AND input_merkantila.sort_of_goods_id='1'
                AND input_records.stornirano='n'
                ORDER BY input_records.input_id";

            $result = $this->model->get_values($sql, $params);

            $sql = "SELECT SUM(input_merkantila.neto) AS neto_total,
                SUM(input_merkantila.n_x_vlaga) AS x_vlaga,
                SUM(input_merkantila.n_x_primese) AS x_primese,
                SUM(input_merkantila.n_x_lom) AS x_lom,
                SUM(input_merkantila.n_x_defekt)AS x_defekt,
                SUM(input_merkantila.n_x_hektolitar) AS x_hektolitar,
                SUM(input_merkantila.kalo_rastur) AS suma_rastur,
                SUM(input_merkantila.dnv) AS suma_dnv,
                SUM(input_merkantila.dnp) AS suma_dnp,
                SUM(input_merkantila.dnh) AS suma_dnh,
                SUM(input_merkantila.dnl) AS suma_dnl,
                SUM(input_merkantila.dnd) AS suma_dnd,
                SUM(input_merkantila.srps) AS srps_total,
                SUM(input_merkantila.trosak_susenja) AS trosak_susenja_total,
                SUM(input_merkantila.suvo_zrno) AS suvo_zrno_total,
                AVG(input_merkantila.protein) AS protein,
                AVG(input_merkantila.gluten) AS gluten,
                AVG(input_merkantila.energija) AS energija
                FROM input_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = input_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = input_records.user_id)
                INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                WHERE  " . $type_of_goods_id . "  " . $goods_id . " " . $wearehouse_id . "  " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND input_records.exit_date !='0000-00-00 00:00:00'
                AND input_merkantila.sort_of_goods_id='1'
                AND input_records.stornirano='n'
                ORDER BY input_records.input_id";

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
            $suma['ponder_protein'] = $result_sum['protein'] === NULL ? 0.00 : $result_sum['protein'];
            $suma['ponder_gluten'] = $result_sum['gluten'] === NULL ? 0.00 : $result_sum['gluten'];
            $suma['ponder_energija'] = $result_sum['energija'] === NULL ? 0.00 : $result_sum['energija'];




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

        if(isset($get['wearehouse_id'])) {
            $sql = "SELECT wearehouse_name FROM wearehouses WHERE wearehouse_id= :wearehouse_id";
            $wearehouse_name = $this->model->get_values($sql, array(':wearehouse_id' => $get['wearehouse_id']));
            $wearehouse_name = ' U MAGACINU '.strtoupper ( $wearehouse_name[0]['wearehouse_name'] );
        } else {
            $wearehouse_name = 'U SVIM MAGACINIMA';
        }

        if(Session::get('wearehouse_name') != '' || Session::get('wearehouse_name') != NULL){
            $wearehouse_name = 'U MAGACINU '.strtoupper (Session::get('wearehouse_name'));
        }

        if(isset($get['client_id'])) {
            $sql = "SELECT firm_name FROM clients WHERE client_id= :client_id";
            $client_name = $this->model->get_values($sql, array(':client_id' => $get['client_id']));
            $client_name = ' OD '.strtoupper ( $client_name[0]['firm_name'] );
        } else {
            $client_name = ' OD SVIH DOBAVLJACA';
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



        $sheet->writeString(1,1,'IZVOD STANJA PRIJEMA '.$good_name.' '.$wearehouse_name.''.$client_name.''.$datum_od ,$format4);
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
            $sheet->writeString(1,18,'Ponder %',$format5);
            $sheet->writeString(1,19,'Ponder %',$format5);
            $sheet->writeString(1,20,'Ponder %',$format5);
            $sheet->writeString(1,21,'Ukupno/kg',$format5);
            $sheet->writeString(1,22,'Ukupno/kg',$format5);

            $sheet->writeNumber(2,17,$suma['ponder_protein'],$formatp);
            $sheet->writeNumber(2,18,$suma['ponder_gluten'],$formatp);
            $sheet->writeNumber(2,19,$suma['ponder_energija'],$formatp);
            $sheet->writeNumber(2,20,$suma['ponder_hektolitra'],$formatp);
            $sheet->writeNumber(2,21,$suma['suma_dnh'],$formatp);
            $sheet->writeNumber(2,22,$suma['srps_total'],$formatp);
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
            $sheet->writeString(3,17,'PROTEIN',$format51);
            $sheet->writeString(3,18,'GLUTEN',$format51);
            $sheet->writeString(3,19,'ENERGIJA',$format51);
            $sheet->writeString(3,20,'HTL',$format51);
            $sheet->writeString(3,21,'ODNH',$format51);
            $sheet->writeString(3,22,'SRPS',$format51);
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
                     $sheet->writeNumber($row_br,17,$value['protein'],$format61);
                     $sheet->writeNumber($row_br,18,$value['gluten'],$format61);
                     $sheet->writeNumber($row_br,19,$value['energija'],$format61);
                     $sheet->writeNumber($row_br,20,$value['hektolitar'],$format61);
                     $sheet->writeNumber($row_br,21,$value['dnh'],$format61);
                     $sheet->writeNumber($row_br,22,$value['srps'],$format61);
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

    //************************************************************** UNOS MERKANTILE **********************************************************************************************
    public function get_wearehouses(){
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {
            header('Content-Type: application/json');
            $sql = 'SELECT * FROM wearehouses ORDER BY wearehouse_name ASC';
            $result = $this->model->get_values($sql, $id=null);
            echo json_encode($result);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }
    }

    public function get_goods_type(){
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {
            Ajax::ajaxCheck();
            header('Content-Type: application/json');
            $sql = "SELECT * FROM type_of_goods WHERE goods_type='kukuruz suseni' || goods_type='kukuruz tel-kel' || goods_type='psenica tel-kel' || goods_type='kukuruz' || goods_type='psenica' || goods_type='suncokret' || goods_type='soja' || goods_type='jecam' || goods_type='uljana repica' || goods_type='sacma'";
            $result = $this->model->get_values($sql,$id=null);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_goods_name($type_of_goods_id){
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {
            Ajax::ajaxCheck();
            $sql = "SELECT * FROM goods WHERE type_of_goods_id= :type_of_goods_id";
            $obj = array(':type_of_goods_id'=>$type_of_goods_id);
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }
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
    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function prijem_merkantila(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        header('Content-Type: application/json');
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){

            $document_br = $this->set_document_number(); //postavlja novi broj dokumenta
           // $date = new DateTime();                      //pravi datum unosa

            $input_info= array(
                "document_br"=>$document_br,
                "user_id"=>Session::get('user_id'),
                "wearehouse_id"=>$data->wearehouse_id,
                "client_id"=>$data->client_id,
                "driver_name"=>$data->driver_name,
                "vehicle_registration"=>$data->driver_reg,
                "input_date"=> date('Y-m-d', strtotime($data->datum)).' '.date('H:i:s'),
                "exit_date"=> date('Y-m-d', strtotime($data->datum)).' '.date('H:i:s'),
            );
            $input_id = $this->model->set_values('input_records',  $input_info); //vraca id prijemnice

            //empty array $goods_parametars
            $goods_parametars = array();
            $goods_parametars["input_id"] = $input_id;
            $goods_parametars["sort_of_goods_id"]= 1 ;
            $goods_parametars["type_of_goods_id"]= $data->type_of_goods_id;
            $goods_parametars["goods_id"]= $data->goods_id;
            //set values into array $goods_parametars
            isset($data->defekt) ? $goods_parametars["defekt"]=$data->defekt : null ;
            isset($data->lom) ? $goods_parametars["lom"]=$data->lom : null;
            isset($data->hektolitar) ? $goods_parametars["hektolitar"]=$data->hektolitar : null;
            isset($data->bruto) ? $goods_parametars["bruto"]=$data->bruto  : null;
            isset($data->tara) ? $goods_parametars["tara"]=$data->tara  : null;
            isset($data->primese) ? $goods_parametars["primese"]=$data->primese  : null;
            isset($data->vlaga) ? $goods_parametars["vlaga"]=$data->vlaga  : null;
            isset($data->protein) ? $goods_parametars["protein"]=$data->protein  : null;
            isset($data->gluten) ? $goods_parametars["gluten"]=$data->gluten  : null;
            isset($data->energija) ? $goods_parametars["energija"]=$data->energija  : null;
            isset($data->br_padanja) ? $goods_parametars["br_padanja"]=$data->br_padanja  : null;
            //set values from array $goods_parametars into mysql table input_merkantila input_merkantila and return merkantila_id
            //print_r($goods_parametars);
           $this->model->set_values('input_merkantila', $goods_parametars);
            $goods_type = strtolower($data->goods_type);
            isset($data->goods_type) ? $goods_parametars["goods_type"]=$data->goods_type : null ;
            if( $goods_type==='kukuruz'){
                $this->obracun_kukuruza($goods_parametars);
            } else if($goods_type==='suncokret'){
                $this->obracun_suncokreta($goods_parametars);
            }else if($goods_type==='soja'){
                $this->obracun_soje($goods_parametars);
            }else if($goods_type==='uljana repica'){
                $this->obracun_uljane_repice($goods_parametars);
            } else if($goods_type==='psenica'){
                $this->obracun_psenice($goods_parametars);
            } else if($goods_type==='sacma'){
                $this->bez_obracuna($goods_parametars);
            }else if($goods_type==='kukuruz tel-kel'){
                $this->bez_obracuna($goods_parametars);
            }else if($goods_type==='kukuruz suseni'){
                $this->bez_obracuna($goods_parametars);
            }else if($goods_type==='psenica tel-kel'){
                $this->bez_obracuna($goods_parametars);
            }

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

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

        $this->_update_results($obj, $result['input_id'], $result['input_id']);
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
        //print_r($data_set); die;
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
            'kalo_rastur'=>$this->_proracun->get_property('_kalo'),
            'tara'=>$this->_proracun->get_property('_tara'),
            'neto'=>$this->_proracun->get_property('_neto'),
            'dnv'=>$this->_proracun->get_property('_dnv'),
            'dnp' =>$this->_proracun->get_property('_dnp'),
            //'dnh' =>$this->_proracun->get_property('_dnh'),
            'n_x_vlaga'=>$this->_proracun->get_property('_neto_x_vlaga'),
            'n_x_primese'=>$this->_proracun->get_property('_neto_x_primese'),
            'n_x_hektolitar'=>$this->_proracun->get_property('_neto_x_hektolitar'),
            'srps' => $this->_proracun->get_property('_srps'),
            //'trosak_susenja'=> $this->_proracun->get_property('_trs'),
            //'suvo_zrno'=> $this->_proracun->get_property('_suvo_zrno'),
        );

        $this->_update_results($obj, $result['input_id'], $result['input_id']);
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

        $this->_update_results($obj, $result['input_id'], $result['input_id']);
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
        $this->_update_results($obj, $result['input_id'], $result['input_id']);
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
        $this->_update_results($obj, $result['input_id'], $result['input_id']);
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

        $this->_update_results($obj, $result['input_id'], $result['input_id']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    private function _update_results($obj, $input_id, $input_id){
        $table = 'input_merkantila';
        $where = 'input_id='.$input_id;
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

    public function select_last_input(){
        $data = json_decode(file_get_contents("php://input"));
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        header('Content-Type: application/json');

        if( $check_session['login'] == 1){
            $sql='SELECT
                goods.goods_cypher,
                goods.goods_name,
                input_records.input_id,
                input_records.driver_name,
                input_records.vehicle_registration,
                CONCAT(input_records.document_br, " / ", YEAR(input_records.input_date)) As document_br,
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
                INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                WHERE exit_date !="0000-00-00 00:00:00" AND input_merkantila.sort_of_goods_id= :sort_of_goods_id
                ORDER BY input_records.exit_date DESC LIMIT 1';
            $result = $this->model->get_values($sql, array(":sort_of_goods_id"=>"1"));
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function getEnabledDays(){
        Ajax::ajaxCheck();
        $sql = "SELECT DATE_FORMAT(DATE(input_records.input_date),'%e-%c-%Y') FROM input_records
                INNER JOIN input_merkantila ON( input_merkantila.input_id = input_records.input_id)
                WHERE input_merkantila.sort_of_goods_id= :sort_of_goods_id AND input_records.stornirano='n'   GROUP BY DATE(input_records.input_date)";
        $obj = array(':sort_of_goods_id'=>'1');
        $result = $this->model->get_values($sql,$obj);
        print_r($result);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function enable_days(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        header('Content-Type: application/json');
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $sql = "SELECT DATE_FORMAT(DATE(input_records.input_date),'%e-%c-%Y') as datum FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    WHERE input_merkantila.sort_of_goods_id= :sort_of_goods_id   GROUP BY DATE(input_records.input_date)";
            $obj = array(':sort_of_goods_id'=>'1');
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
            // echo json_encode(array('ulogovan'=>Session::get('wearehouse_id'), ':sort_of_goods_id'=>'1'), JSON_NUMERIC_CHECK);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //---------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_search_type(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        header('Content-Type: application/json');
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $input_date = date('Y-m-d', strtotime($data->datumPrijema));
            $sql = "SELECT type_of_goods.type_of_goods_id, type_of_goods.goods_type FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                    WHERE input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND DATE(input_records.input_date)= :input_date
                    AND input_records.stornirano='n'
                    GROUP BY input_merkantila.type_of_goods_id";
            $obj = array(':sort_of_goods_id'=>'1', ':input_date'=>$input_date);
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //--------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_search_good_name_prijem(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object

        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $input_date = date('Y-m-d', strtotime($data->datumPrijema));
            $type_of_goods_id = $data->input_type_id;
            $sql = "SELECT goods.goods_id, goods.goods_name  FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                    WHERE input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND DATE(input_records.input_date)= :input_date
                    AND input_merkantila.type_of_goods_id= :type_of_goods_id
                    AND input_records.stornirano='n'
                    GROUP BY goods_id";
            $obj = array(':sort_of_goods_id'=>'1', ':input_date'=>$input_date, ':type_of_goods_id'=>$type_of_goods_id);
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //----------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_search_prijemnica(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $input_date = date('Y-m-d', strtotime($data->datumPrijema));
            $type_of_goods_id = $data->input_type_id;
            $goods_id = $data->goods_id;
            $sql = "SELECT
                    input_records.input_id,
                    goods.goods_id,
                    CONCAT(input_records.document_br, ' / ', YEAR(input_records.input_date)) As document_br,
                    goods.goods_name,
                    clients.firm_name
                    FROM input_records
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                    INNER JOIN clients ON (clients.client_id = input_records.client_id)
                    WHERE input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND DATE(input_records.input_date)= :input_date
                    AND input_merkantila.type_of_goods_id= :type_of_goods_id
                    AND input_merkantila.goods_id= :goods_id
                    AND input_records.stornirano= :stornirano
                    AND input_records.exit_date!= :exit_date
                    ORDER BY input_records.input_id DESC";
            $obj = array(':sort_of_goods_id'=>'1', ':input_date'=>$input_date, ':type_of_goods_id'=>$type_of_goods_id, ':goods_id'=>$goods_id, ':stornirano'=>"n", ":exit_date"=>"0000-00-00 00:00:00" );
            $result = $this->model->get_values($sql,$obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);


        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function select_odabrani_prijem(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        header('Content-Type: application/json');
        if( $check_session['login'] == 1){
            $sql='SELECT
                    goods.goods_cypher,
                    goods.goods_name,
                    input_records.input_id,
                    input_records.driver_name,
                    input_records.vehicle_registration,
                    CONCAT(input_records.document_br, " / ", YEAR(input_records.input_date)) As document_br,
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
                    INNER JOIN input_merkantila ON (input_merkantila.input_id = input_records.input_id)
                    INNER JOIN clients ON (clients.client_id = input_records.client_id)
                    INNER JOIN places ON (places.place_id = clients.place_id)
                    INNER JOIN goods ON (goods.goods_id = input_merkantila.goods_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_merkantila.type_of_goods_id)
                    WHERE input_merkantila.sort_of_goods_id= :sort_of_goods_id
                    AND input_records.input_id= :input_id
                    AND input_records.stornirano= :stornirano
                    AND input_records.exit_date!= :exit_date';
            $result = $this->model->get_values($sql, array(":sort_of_goods_id"=>"1", ":input_id"=>$data->input_id, ":stornirano"=>"n", ":exit_date"=>"0000-00-00 00:00:00"));
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }

    }

}
?>
