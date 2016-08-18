<?php
class Pregled_Otpreme_Repromaterijal_Admin_Api extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Session::init();
        $logged = Session::get('loggedIn');
        $status = Session::get('role');
        if($logged == false || $status != 'Administrator' && $status != 'Redovan korisnik' && $status != 'Logistika'){
            unset($logged);
            unset($status);
            Session::destroy();
            return array('login' => 0, 'msg' => 'Vreme sesije je isteklo ulogujte se ponovo');
        }
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



    public function get_search_good_type_admin()
    {
        Ajax::ajaxCheck();
        header('Content-Type: application/json');
            $sql = "SELECT
                    output_repromaterijal.type_of_goods_id,
                    type_of_goods.goods_type
                    FROM output_records
                    INNER JOIN output_repromaterijal ON (output_repromaterijal.output_id = output_records.output_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id)
                    WHERE output_repromaterijal.sort_of_goods_id= :sort_of_goods_id
                    AND output_records.exit_date !='0000-00-00 00:00:00'
                    AND output_records.stornirano='n'
                    GROUP BY output_repromaterijal.type_of_goods_id";
            $obj = array(':sort_of_goods_id' => '2');
            $result = $this->model->get_values($sql, $obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------


    public function get_search_good_name_admin()
    {
        Ajax::ajaxCheck();
        $type_of_goods_id = $_GET['type_of_goods_id']; //checking if session exists
        header('Content-Type: application/json');

            $sql = "SELECT
                    goods.goods_id,
                    goods.goods_name
                    FROM output_records
                    INNER JOIN output_repromaterijal ON (output_repromaterijal.output_id = output_records.output_id)
                    INNER JOIN goods ON (goods.goods_id = output_repromaterijal.goods_id)
                    WHERE output_repromaterijal.sort_of_goods_id= :sort_of_goods_id
                    AND output_records.exit_date !='0000-00-00 00:00:00'
                    AND output_repromaterijal.type_of_goods_id= :type_of_goods_id
                    AND output_records.stornirano='n'
                    GROUP BY goods.goods_id";
            $obj = array(':sort_of_goods_id' => '2', ':type_of_goods_id' => $type_of_goods_id);
            $result = $this->model->get_values($sql, $obj);
            echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //--------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_search_good_wearehouses_admin(){
        $type_of_goods_id = $_GET['type_of_goods_id']; //checking if session exists
        $goods_id = $_GET['goods_id'];
        header('Content-Type: application/json');


            $sqlc = "SELECT wearehouses.wearehouse_id, wearehouses.wearehouse_name FROM output_records
                    INNER JOIN output_repromaterijal ON ( output_repromaterijal.output_id = output_records.output_id)
                    INNER JOIN wearehouses ON ( wearehouses.wearehouse_id = output_records.wearehouse_id)
                    WHERE output_repromaterijal.sort_of_goods_id= :sort_of_goods_id
                    AND output_repromaterijal.type_of_goods_id= :type_of_goods_id
                    AND output_repromaterijal.goods_id= :goods_id
                    AND output_records.exit_date !='0000-00-00 00:00:00'
                    AND output_records.stornirano='n'  GROUP BY wearehouses.wearehouse_id";
            $objc = array(':sort_of_goods_id' => '2', ':type_of_goods_id' => $type_of_goods_id, ':goods_id' => $goods_id);
            $wearehouses = $this->model->get_values($sqlc, $objc);

            $sqlw = "SELECT clients.client_id, clients.firm_name FROM output_records
                    INNER JOIN clients ON (clients.client_id = output_records.client_id)
                    INNER JOIN output_repromaterijal ON (output_repromaterijal.output_id = output_records.output_id)
                    WHERE output_repromaterijal.sort_of_goods_id= :sort_of_goods_id
                    AND output_repromaterijal.type_of_goods_id= :type_of_goods_id
                    AND output_repromaterijal.goods_id= :goods_id
                    AND output_records.exit_date !='0000-00-00 00:00:00'
                    AND output_records.stornirano='n'
                    GROUP BY clients.client_id";
            $objw = array(':sort_of_goods_id' => '2', ':type_of_goods_id' => $type_of_goods_id, ':goods_id' => $goods_id);
            $clients = $this->model->get_values($sqlw, $objw);

            echo json_encode(array('clients'=>$clients, 'wearehouses'=>$wearehouses), JSON_NUMERIC_CHECK);
    }

    //----------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_search_good_client_admin()
    {
        Ajax::ajaxCheck();
        $type_of_goods_id = $_GET['type_of_goods_id']; //checking if session exists
        $goods_id = $_GET['goods_id'];
        $wearehouse_id = $_GET['wearehouse_id'];

        $wearehouse_upit = isset($_GET['wearehouse_id']) || !($_GET['wearehouse_id'] == NULL) ? 'output_records.wearehouse_id= :wearehouse_id AND ' : '';

        header('Content-Type: application/json');
        $sql = "SELECT
                clients.client_id,
                clients.firm_name
                FROM output_records
                INNER JOIN output_repromaterijal ON ( output_repromaterijal.output_id = output_records.output_id )
                INNER JOIN clients ON ( clients.client_id = output_records.client_id )
                WHERE  ".$wearehouse_upit." output_repromaterijal.sort_of_goods_id= :sort_of_goods_id
                AND output_repromaterijal.type_of_goods_id= :type_of_goods_id
                AND output_repromaterijal.goods_id= :goods_id
                AND output_records.exit_date !='0000-00-00 00:00:00'
                AND output_records.stornirano='n'
                GROUP BY clients.client_id";
        $obj = array();
        if(isset($_GET['wearehouse_id']) || !($_GET['wearehouse_id'] == NULL)){
            $obj[':wearehouse_id'] = $wearehouse_id;
        }
        $obj[':sort_of_goods_id'] = '2';
        $obj[':type_of_goods_id'] = $type_of_goods_id;
        $obj[':goods_id'] = $goods_id;
        //$obj = array(':sort_of_goods_id' => '2', ':type_of_goods_id' => $type_of_goods_id, ':goods_id' => $goods_id);

        $result = $this->model->get_values($sql, $obj);
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }


    public function get_search_prijem()
    {
        //type_of_goods_id=1&goods_id=94&client_id=1&datum_od=01.01.2016&datum_do=03.01.2016


        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? 'output_repromaterijal.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id'])  || !($_GET['goods_id'] == NULL)  ? ' AND output_repromaterijal.goods_id= :goods_id' : '';
        $wearehouse = isset($_GET['wearehouse_id']) || !($_GET['wearehouse_id'] == NULL) ? 'AND output_records.wearehouse_id= :wearehouse_id' : '';
        $client_id = isset($_GET['client_id']) || !($_GET['client_id'] == NULL)  ? ' AND output_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) || !($_GET['datum_od'] == NULL) ? ' AND DATE(output_records.output_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) || !($_GET['datum_do'] == NULL) ? ' AND DATE(output_records.output_date)<= :datum_do' : '';
        $statement = $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do;
        $params = array();
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
        /*print_r($params).'<br />';
        echo $statement;*/

        header('Content-Type: application/json');



        $sql = "SELECT
                goods.goods_cypher,
                goods.goods_name,
                output_records.output_id,
                output_records.driver_name,
                output_records.vehicle_registration,
                CONCAT(output_records.driver_name, ' / ', output_records.vehicle_registration) AS vozac,
                CONCAT(output_records.document_br, ' / ', YEAR(output_records.output_date)) As document_br,
                YEAR (output_records.output_date) as year,
                DATE(output_records.output_date) AS date,
                TIME(output_records.output_date) AS time,
                output_repromaterijal.bruto,
                output_repromaterijal.tara,
                output_repromaterijal.neto,
                output_repromaterijal.kolicina,
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
                type_of_measurement_unit.measurement_unit,
                type_of_measurement_unit.measurement_name,
                CONCAT(users.name, ' ', users.surname) AS storekeeper,
                CONCAT(type_of_measurement_unit.measurement_name, ' (', type_of_measurement_unit.measurement_unit, ')') As merna_jedinica
                FROM output_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = output_records.user_id)
                INNER JOIN output_repromaterijal ON (output_repromaterijal.output_id = output_records.output_id)
                INNER JOIN clients ON (clients.client_id = output_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = output_repromaterijal.goods_id)
                INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id)
                WHERE  " . $type_of_goods_id . " " . $goods_id . " " . $wearehouse . " " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND output_records.exit_date !='0000-00-00 00:00:00'
                AND output_repromaterijal.sort_of_goods_id='2'
                AND output_records.stornirano='n' ORDER BY output_records.output_id";

        $result = $this->model->get_values($sql, $params);
        echo json_encode($result, JSON_NUMERIC_CHECK);


    }


    public function get_search_prijem_total()
    {
        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? 'output_repromaterijal.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND output_repromaterijal.goods_id= :goods_id' : '';
        $wearehouse = isset($_GET['wearehouse_id']) || !($_GET['wearehouse_id'] == NULL) ? 'AND output_records.wearehouse_id= :wearehouse_id' : '';
        $client_id = isset($_GET['client_id']) || !($_GET['client_id'] == NULL)  ? ' AND output_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) || !($_GET['datum_od'] == NULL) ? ' AND DATE(output_records.output_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) || !($_GET['datum_do'] == NULL) ? ' AND DATE(output_records.output_date)<= :datum_do' : '';
        $statement = $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do;
        $params = array();
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
            $sql = "SELECT
                    SUM(output_repromaterijal.neto) AS neto_total,
                    SUM(output_repromaterijal.kolicina) AS kolicina_total,
                    type_of_measurement_unit.measurement_name,
                    type_of_measurement_unit.measurement_unit
                FROM output_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = output_records.user_id)
                INNER JOIN output_repromaterijal ON ( output_repromaterijal.output_id = output_records.output_id )
                INNER JOIN clients ON (clients.client_id = output_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = output_repromaterijal.goods_id)
                INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id)
                WHERE   " . $type_of_goods_id . " " . $goods_id . " " . $wearehouse . " " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND output_records.exit_date !='0000-00-00 00:00:00' AND output_repromaterijal.sort_of_goods_id='2' AND output_records.stornirano='n' ORDER BY output_records.output_id";

            $result = $this->model->get_values($sql, $params);
            $result = $result[0];



            echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //----------------------------------------------------------------------------------------------------------------------------------------------------

  /*  public function storniraj_dokument($output_id)
    {
        Ajax::ajaxCheck();
        $table = 'output_records';
        $data = array(
            'stornirano' => 'y'
        );
        $where = 'output_id="' . $output_id . '"';
        $this->model->update_values($table, $data, $where);
        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }*/


    public function storniraj_dokument()
    {
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        //print_r($data);return false;
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $table = 'output_records';
            $date = new DateTime();
            $new_data = array(
                'stornirano'       => 'y',
                'stornirano_komentar'  => $data->napomena,
                'stornirano_datum' => $date = $date->format('Y-m-d H:i:s'),
                "stornirao_id"     => Session::get('user_id')
            );
            $where = 'output_id="' . $data->output_id . '"';
            $this->model->update_values($table, $new_data, $where);
            header('Content-Type: application/json');
            echo json_encode(array('success' => 1));
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //----------------------------------------------------------------------------------------------------------------------------------------------------

    public function getExcell()
    {

        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? 'output_repromaterijal.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND output_repromaterijal.goods_id= :goods_id' : '';
        $wearehouse = isset($_GET['wearehouse_id']) || !($_GET['wearehouse_id'] == NULL) ? 'AND output_records.wearehouse_id= :wearehouse_id' : '';
        $client_id = isset($_GET['client_id']) || !($_GET['client_id'] == NULL)  ? ' AND output_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) || !($_GET['datum_od'] == NULL) ? ' AND DATE(output_records.output_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) || !($_GET['datum_do'] == NULL) ? ' AND DATE(output_records.output_date)<= :datum_do' : '';
        $statement = $type_of_goods_id . " " . $goods_id . " " . $client_id . " " . $datum_od . " " . $datum_do;
        $params = array();
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



            $sql = "SELECT
                goods.goods_cypher,
                goods.goods_name,
                output_records.output_id,
                output_records.driver_name,
                output_records.vehicle_registration,
                CONCAT(output_records.document_br, ', ', YEAR(output_records.output_date)) As document_br,
                YEAR (output_records.output_date) as year,
                /*DATE_FORMAT(DATE(output_records.output_date),'%d.%m.%Y') AS date,*/
                DATE(output_records.output_date) AS date,
                TIME(output_records.output_date) AS time,
                output_repromaterijal.bruto,
                output_repromaterijal.tara,
                output_repromaterijal.neto,
                output_repromaterijal.kolicina,
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
                CONCAT(users.name, ' ', users.surname) AS storekeeper,
                CONCAT(type_of_measurement_unit.measurement_name, ' (', type_of_measurement_unit.measurement_unit, ')') As merna_jedinica
                FROM output_records
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                INNER JOIN users ON (users.user_id = output_records.user_id)
                INNER JOIN output_repromaterijal ON (output_repromaterijal.output_id = output_records.output_id)
                INNER JOIN clients ON (clients.client_id = output_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = output_repromaterijal.goods_id)
                INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id)
                WHERE  " . $type_of_goods_id . " " . $goods_id . "  " . $wearehouse . " " . $client_id . " " . $datum_od . " " . $datum_do . "
                AND output_records.exit_date !='0000-00-00 00:00:00'
                AND output_repromaterijal.sort_of_goods_id='2'
                AND output_records.stornirano='n'
                ORDER BY output_records.output_id";

            $result = $this->model->get_values($sql, $params);

              $sql = "SELECT
                    SUM(output_repromaterijal.neto) AS neto_total,
                    SUM(output_repromaterijal.kolicina) AS kolicina_total,
                    type_of_measurement_unit.measurement_name,
                    type_of_measurement_unit.measurement_unit
                    FROM output_records
                    INNER JOIN wearehouses ON (wearehouses.wearehouse_id = output_records.wearehouse_id)
                    INNER JOIN users ON (users.user_id = output_records.user_id)
                    INNER JOIN output_repromaterijal ON (output_repromaterijal.output_id = output_records.output_id)
                    INNER JOIN clients ON (clients.client_id = output_records.client_id)
                    INNER JOIN places ON (places.place_id = clients.place_id)
                    INNER JOIN goods ON (goods.goods_id = output_repromaterijal.goods_id)
                    INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id)
                    WHERE   " . $type_of_goods_id . " " . $goods_id . " " . $wearehouse . " " . $client_id . " " . $datum_od . " " . $datum_do . "
                    AND output_records.exit_date !='0000-00-00 00:00:00'
                    AND output_repromaterijal.sort_of_goods_id='2'
                    AND output_records.stornirano='n'
                    ORDER BY output_records.output_id";

            $result_sum = $this->model->get_values($sql, $params);
            $result_sum = $result_sum[0];




            $this->printExcel($_GET, $result, $result_sum);


    }




    public function getExcellAdmin()
    {
        //var_dump($_GET);

        $type_of_goods_id = isset($_GET['type_of_goods_id']) ? ' output_records.type_of_goods_id= :type_of_goods_id' : '';
        $goods_id = isset($_GET['goods_id']) ? ' AND output_records.goods_id= :goods_id' : '';
        $wearehouse_id = isset($_GET['wearehouse_id']) ? ' AND output_records.wearehouse_id= :wearehouse_id' : '';
        $client_id = isset($_GET['client_id']) ? ' AND output_records.client_id= :client_id' : '';
        $datum_od = isset($_GET['datum_od']) ? ' AND DATE(output_records.output_date)>= :datum_od' : '';
        $datum_do = isset($_GET['datum_do']) ? ' AND DATE(output_records.output_date)<= :datum_do' : '';
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



            $sql = "SELECT
                goods.goods_cypher,
                goods.goods_name,
                input_records.input_id,
                input_records.driver_name,
                input_records.vehicle_registration,
                CONCAT(input_records.document_br, ', ', YEAR(input_records.input_date)) As document_br,
                input_records.input_merkantila_id,
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
                INNER JOIN input_merkantila ON (input_merkantila.input_merkantila_id = input_records.input_merkantila_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_records.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_records.type_of_goods_id)
                WHERE   " . $type_of_goods_id . " " . $goods_id . " " . $wearehouse_id . "  " . $client_id . " " . $datum_od . " " . $datum_do . "  AND input_records.exit_date !='0000-00-00 00:00:00' AND input_records.sort_of_goods_id='1' AND input_records.stornirano='n' ORDER BY input_records.input_id";

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
                INNER JOIN input_merkantila ON (input_merkantila.input_merkantila_id = input_records.input_merkantila_id)
                INNER JOIN clients ON (clients.client_id = input_records.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                INNER JOIN goods ON (goods.goods_id = input_records.goods_id)
                INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = input_records.type_of_goods_id)
                WHERE  " . $type_of_goods_id . "  " . $goods_id . " " . $wearehouse_id . "  " . $client_id . " " . $datum_od . " " . $datum_do . "  AND input_records.exit_date !='0000-00-00 00:00:00' AND input_records.sort_of_goods_id='1' AND input_records.stornirano='n' ORDER BY input_records.output_id";

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
            $client_name = ' OD SVIH KUPACA';
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


        $xml = new ExcelWriterXML('Pregled_otpreme _epromaterijala.xls');
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
        $sheet->columnWidth(13,'60');
        $sheet->columnWidth(14,'90');



        $sheet->writeString(1,1,'IZVOD STANJA OTPREME '.$good_name.' '.$wearehouse_name.''.$client_name.''.$datum_od ,$format4);
        $sheet->cellMerge(1,1,10,1);
        $sheet->writeString(1,12,'Ukupno',$format5);
        $sheet->writeNumber(2,12,$suma['neto_total'],$formatp);
        $sheet->writeString(1,13,'Ukupno',$format5);
        $sheet->writeNumber(2,13,$suma['kolicina_total'],$formatp);
        $sheet->writeString(1,14,'Merna jedinica',$formatp);
        $sheet->writeString(2,14,$suma['measurement_name'].' ('.$suma['measurement_unit'].')',$formatp);



        $sheet->writeString(3,1,'BR/PR',$format5);
        $sheet->writeString(3,2,'DATUM',$format5);
        $sheet->writeString(3,3,'SAT',$format5);
        $sheet->writeString(3,4,'PROIZVOD',$format5);
        $sheet->writeString(3,5,'NAZIV KUPCA',$format5);
        $sheet->writeString(3,6,'ADRESA KUPCA',$format5);
        $sheet->writeString(3,7,'MESTO KUPCA',$format5);
        $sheet->writeString(3,8,'VOZAČ',$format5);
        $sheet->writeString(3,9,'REGISTRACIJA',$format5);
        $sheet->writeString(3,10,'BRUTO',$format5);
        $sheet->writeString(3,11,'TARA',$format5);
        $sheet->writeString(3,12,'NETO',$format51);
        $sheet->writeString(3,13,'KOLICINA',$format51);
        $sheet->writeString(3,14,'MERNA JEDINICA',$format51);




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
                 $sheet->writeNumber($row_br,13,$value['kolicina'],$format61);
                 $sheet->writeString($row_br,14,$value['merna_jedinica'],$format6);


             };
        $xml->sendHeaders();
        $xml->writeData();

    }


    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function getOtprema($session_id=null){
        $output_id = $_GET['output_id'];
        header('Content-Type: application/json');
            $sql='SELECT
                output_records.output_id,
                output_records.driver_name,
                output_records.vehicle_registration,
                output_records.vezani_dokument,
                output_records.dispozicija_id,
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
                WHERE exit_date !="0000-00-00 00:00:00"
                AND output_repromaterijal.sort_of_goods_id = :sort_of_goods_id
                AND output_records.output_id = :output_id
                ORDER BY output_records.exit_date DESC LIMIT 1';
            $result = $this->model->get_values($sql, array(":sort_of_goods_id"=>"2", ":output_id"=>$output_id));
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
                    INNER JOIN sort_of_goods ON (sort_of_goods.sort_of_goods_id = output_repromaterijal.sort_of_goods_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id)
                    INNER JOIN goods ON (goods.goods_id = output_repromaterijal.goods_id)
                    INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                    WHERE output_repromaterijal.output_id= :output_id';
            $outputs = $this->model->get_values($sql, array(":output_id"=>$result['output_id']));
            $result['outputs'] = $outputs;
            echo json_encode($result);
    }

    //************************************************************** IZLAZ REPROMATERIJAL  **********************************************************************************************
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

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_all_goods(){
        $sql = 'SELECT goods.*, type_of_measurement_unit.measurement_unit, type_of_measurement_unit.measurement_name FROM goods
                INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                WHERE goods.sort_of_goods_id="2"
                AND goods.type_of_goods_id="6"
                OR goods.type_of_goods_id="7"
                OR goods.type_of_goods_id="9"
                OR goods.type_of_goods_id="15"
                ORDER BY goods_id';
        $result = $this->model->get_values($sql, $id=null);
        header('Content-Type: application/json');
        echo json_encode($result);

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

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function otprema_repromaterijal(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
       /* echo '<pre>';
        print_r($data);return false;*/
        header('Content-Type: application/json');
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $document_br = $this->set_document_number();


            $output_info= array(
                "document_br"=>$document_br,
                "user_id"=>Session::get('user_id'),
                "wearehouse_id"=>$data->wearehouse_id,
                "client_id"=>$data->client_id,
                "vezani_dokument"=>$data->vezani_dokument,
                "output_date"=> date('Y-m-d', strtotime($data->datum)).' '.date('H:i:s'),
                "exit_date"=> date('Y-m-d', strtotime($data->datum)).' '.date('H:i:s'),
                "driver_name"=>$data->driver_name,
                "vehicle_registration"=>$data->driver_reg
            );
            $output_id = $this->model->set_values('output_records',  $output_info);

            foreach($data->orders as $obj){
                $date = new DateTime();
                $output_repromaterijal= array(
                    "output_id"=>$output_id,
                    "sort_of_goods_id"=>$obj->sort_of_goods_id,
                    "type_of_goods_id"=>$obj->type_of_goods_id,
                    "goods_id"=>$obj->goods_id,
                    "kolicina"=>$obj->quantity
                );
                $this->model->set_values('output_repromaterijal',  $output_repromaterijal);
            }

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function select_last_output(){
        //$data = json_decode(file_get_contents("php://input"));
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        //  print_r(Session::get('wearehouse_id'))l
        header('Content-Type: application/json');

        if( $check_session['login'] == 1){
            $sql='SELECT
                output_records.output_id,
                output_records.driver_name,
                output_records.vehicle_registration,
                output_records.vezani_dokument,
                output_records.dispozicija_id,
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
                WHERE exit_date!= :exit_date
                AND output_records.stornirano= :stornirano
                AND output_repromaterijal.sort_of_goods_id != :sort_of_goods_id
                ORDER BY output_records.output_id DESC LIMIT 1';
            $result = $this->model->get_values($sql, array(":exit_date"=>"0000-00-00 00:00:00", ":stornirano"=>"n", ":sort_of_goods_id"=>"1"));
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
                    INNER JOIN sort_of_goods ON (sort_of_goods.sort_of_goods_id = output_repromaterijal.sort_of_goods_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id)
                    INNER JOIN goods ON (goods.goods_id = output_repromaterijal.goods_id)
                    INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                    WHERE output_repromaterijal.output_id= :output_id';
            $outputs = $this->model->get_values($sql, array(":output_id"=>$result['output_id']));
            $result['outputs'] = $outputs;
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }


}


?>
