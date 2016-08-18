<?php
class Kupac_Repromaterijal_Api extends Controller
{
    public function __construct()
    {
        parent::__construct();
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
    //------------------------------------------------------------------------------------------------------------------

    public function get_kupce(){

        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {
            header('Content-Type: application/json');
            $sql = 'SELECT
                        output_records.client_id,
                        clients.firm_name
                    FROM
                        output_records
                    INNER JOIN clients ON clients.client_id = output_records.client_id
                    WHERE output_records.stornirano = :stornirano
                    GROUP BY output_records.client_id
                    ORDER BY clients.firm_name ASC';
            $result = $this->model->get_values($sql, array(':stornirano'=>'n'));
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }
    }

    //------------------------------------------------------------------------------------------------------------------

    public function get_category_items(){
        $client_id = strip_tags($_GET['client_id']);
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {
            header('Content-Type: application/json');
            $sql = 'SELECT
                        type_of_goods.type_of_goods_id,
                        type_of_goods.goods_type
                    FROM
                        output_records
                    INNER JOIN output_repromaterijal ON output_repromaterijal.output_id = output_records.output_id
                    INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id
                    WHERE output_records.stornirano = :stornirano AND output_records.client_id = :client_id
                    GROUP BY type_of_goods.type_of_goods_id
                    ORDER BY type_of_goods.goods_type ASC';
            $result = $this->model->get_values($sql, array(':stornirano'=>'n', ':client_id'=>$client_id));

            foreach($result as $key=>$value){
               // print_r($value);
                $sqli = 'SELECT
                        goods.goods_id,
                        goods.goods_name,
                        output_repromaterijal.*,
                        output_records.*
                    FROM
                        output_records
                    INNER JOIN output_repromaterijal ON output_repromaterijal.output_id = output_records.output_id
                    INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id
                    INNER JOIN goods ON goods.goods_id = output_repromaterijal.goods_id
                    WHERE output_records.stornirano= :stornirano AND output_records.client_id = :client_id AND output_repromaterijal.type_of_goods_id = :type_of_goods_id
                    GROUP BY output_repromaterijal.goods_id
                    ORDER BY goods.goods_name ASC';
                $resulti = $this->model->get_values($sqli, array(':stornirano'=>'n', ':client_id'=>$client_id, ':type_of_goods_id'=>$value['type_of_goods_id']));
               // print_r($resulti);
                $result[$key]['good_names'] = $resulti;
            }
           /// print_r($result);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }
    }

    public function get_goods(){
        $client_id = strip_tags($_GET['client_id']);
        $type_of_goods_id = isset($_GET['type_of_goods_id']) ?  strip_tags($_GET['type_of_goods_id']) : null;
        $goods_id = isset($_GET['goods_id']) ?  strip_tags($_GET['goods_id']) : null;

        $search_type_of_goods_id = isset($_GET['type_of_goods_id']) ? ' AND output_repromaterijal.type_of_goods_id = :type_of_goods_id' : '';
        $search_goods_id = isset($_GET['goods_id']) ? ' AND output_repromaterijal.goods_id = :goods_id' : '';
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {
            header('Content-Type: application/json');
            $sql = 'SELECT
                        type_of_goods.type_of_goods_id,
                        type_of_goods.goods_type
                    FROM
                        output_records
                    INNER JOIN output_repromaterijal ON output_repromaterijal.output_id = output_records.output_id
                    INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id
                    WHERE output_records.client_id = :client_id AND output_records.stornirano="n" '.$search_type_of_goods_id.'
                    GROUP BY type_of_goods.type_of_goods_id
                    ORDER BY type_of_goods.goods_type ASC';
            $arr1 = array(':client_id'=>$client_id);
            if(isset($_GET['type_of_goods_id'])){
                $arr1[':type_of_goods_id'] = $type_of_goods_id;
            }
            $result = $this->model->get_values($sql, $arr1);

            foreach($result as $key=>$value) {
                $sqli = 'SELECT
                        goods.goods_id,
                        goods.goods_name,
                        output_repromaterijal.*,
                        DATE_FORMAT(DATE(output_records.output_date),"%d.%m.%Y") AS datum_otpreme,
                        CONCAT(output_records.document_br, "/", YEAR(output_records.output_date)) AS document_number,
                        wearehouses.wearehouse_name AS magacin,
                        type_of_measurement_unit.measurement_unit AS merna_jedinica,
                        output_records.*
                    FROM
                        output_records
                    INNER JOIN output_repromaterijal ON output_repromaterijal.output_id = output_records.output_id
                    INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id
                    INNER JOIN goods ON goods.goods_id = output_repromaterijal.goods_id
                    INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                    INNER JOIN wearehouses ON wearehouses.wearehouse_id = output_records.wearehouse_id
                    WHERE output_records.client_id = :client_id AND output_repromaterijal.type_of_goods_id= :type_of_goods_id AND output_records.stornirano="n" ' . $search_goods_id . '
                    ORDER BY output_records.output_id ASC';
                $arr = array(':client_id' => $client_id,':type_of_goods_id'=>$value['type_of_goods_id']);
                if(isset($_GET['goods_id'])){
                    $arr[':goods_id'] = $goods_id;
                }
                // ':type_of_goods_id'=>$value['type_of_goods_id']
                $resulti = $this->model->get_values($sqli, $arr);
                // print_r($resulti);
                $result[$key]['good_names'] = $resulti;
            }
            echo json_encode($result);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }
    }

    //------------------------------------------------------------------------------------------------------------------

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


}
?>