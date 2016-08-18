<?php
class Dashboard_Api extends Controller
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


    public function session_conditioner(){
        Session::init();
        $logged = Session::get('loggedIn');
        header('Content-Type: application/json');
        if($logged == '1'){
            echo json_encode(array('success'=>$logged));
        } else {
            echo json_encode(array('success'=>false));
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

    public function enableDays(){
        $wearehouse_id = isset($_GET['wearehouse_id']) ? strip_tags($_GET['wearehouse_id']) : null;
        $where_wearehouse_id = isset($_GET['wearehouse_id']) ? 'AND output_records.wearehouse_id= :wearehouse_id ' : '';
       // print_r($_GET); return false;
        $sql = 'SELECT DATE_FORMAT(DATE(output_records.output_date),"%e-%c-%Y") as datum FROM output_records
                INNER JOIN output_repromaterijal ON output_repromaterijal.output_id = output_records.output_id
                WHERE output_repromaterijal.sort_of_goods_id= :sort_of_goods_id '.$where_wearehouse_id.' GROUP BY output_records.document_br';
        $obj = array(':sort_of_goods_id'=>2);
        if(isset($_GET['wearehouse_id'])){
            $obj[':wearehouse_id'] = $wearehouse_id;
        }

        $sql_last = 'SELECT DATE_FORMAT(DATE(output_records.output_date),"%d.%m.%Y") AS last_day FROM output_records
                    INNER JOIN output_repromaterijal ON output_repromaterijal.output_id = output_records.output_id
                    WHERE output_repromaterijal.sort_of_goods_id= :sort_of_goods_id '.$where_wearehouse_id.'
                    GROUP BY DATE (output_records.output_date)
                    ORDER BY DATE (output_records.output_date)
                    DESC LIMIT 1';
        $obj_last = array(':sort_of_goods_id'=>2);
        if(isset($_GET['wearehouse_id'])){
            $obj_last[':wearehouse_id'] = $wearehouse_id;
        }

        $doc = $this->model->get_values($sql, $obj);
        $doc_last = $this->model->get_values($sql_last, $obj_last);
        echo json_encode(array('output_days'=>$doc, 'last_output_day'=>$doc_last));
    }

    public function getDayOutputs(){
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {
            $date = strip_tags($_GET['date']);
            $wearehouse_id = isset($_GET['wearehouse_id']) ? $_GET['wearehouse_id'] : null;
            $where_wearehouse_id = isset($_GET['wearehouse_id']) ? 'AND output_records.wearehouse_id= :wearehouse_id ' : '';
            $sql="SELECT
                    output_records.output_id,
                    CONCAT(output_records.document_br, ' / ', YEAR(output_records.output_date)) as doc_br,
                    DATE_FORMAT(DATE(output_records.output_date),'%d.%m.%Y') AS datum,
                    clients.firm_name,
                    output_records.driver_name,
                    output_records.vehicle_registration,
                    output_repromaterijal.kolicina,
                     sort_of_goods.goods_sort,
                     type_of_goods.goods_type,
                     goods.goods_name,
                     type_of_measurement_unit.measurement_unit
                    FROM output_records
                    INNER JOIN clients ON clients.client_id = output_records.client_id
                    INNER JOIN output_repromaterijal ON output_repromaterijal.output_id = output_records.output_id
                    INNER JOIN sort_of_goods ON sort_of_goods.sort_of_goods_id = output_repromaterijal.sort_of_goods_id
                    INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id
                    INNER JOIN goods ON goods.goods_id = output_repromaterijal.goods_id
                    INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                    WHERE DATE(output_records.output_date)= :output_date AND output_records.stornirano= :stornirano ".$where_wearehouse_id."
                ORDER BY output_records.document_br DESC";
            $obj = array(
                ':output_date'=>date('Y-m-d', strtotime($date)),
                ':stornirano'=>'n'
            );
            if(isset($_GET['wearehouse_id'])){
                $obj[':wearehouse_id'] = $wearehouse_id;
            }
            $doc = $this->model->get_values($sql, $obj);
           // print_r($doc);return false;
            /*foreach($doc as $key=>$value){
                $sql_r = "SELECT output_repromaterijal.kolicina,
                                 sort_of_goods.goods_sort,
                                 type_of_goods.goods_type,
                                 goods.goods_name,
                                 type_of_measurement_unit.measurement_unit
                          FROM output_repromaterijal
                          INNER JOIN sort_of_goods ON sort_of_goods.sort_of_goods_id = output_repromaterijal.sort_of_goods_id
                          INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = output_repromaterijal.type_of_goods_id
                          INNER JOIN goods ON goods.goods_id = output_repromaterijal.goods_id
                          INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                          WHERE output_repromaterijal.output_id = :output_id ";
                $doc[$key]['roba'] = $this->model->get_values($sql_r, array(':output_id'=>$value['output_id']));
            }*/
            header('Content-Type: application/json');
            echo json_encode($doc);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }
    }
}
?>