<?php
class Stanje_Repromaterijala_Api extends Controller
{
    public function __construct()
    {
        parent::__construct();
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
        if ($logged == false && $status != 'Administrator') {
            unset($logged);
            unset($status);
            Session::destroy();
            return array('login' => 0);
        } else {
            return array('login' => 1);
        }
    }

    public function get_wearehouses()
    {
        header('Content-Type: application/json');
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {

            $sql = "SELECT * FROM wearehouses ORDER BY wearehouse_id ASC";

            $result = $this->model->get_values($sql, $id=null);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }

    private function zamena($br){
        switch ($br) {
            case 6:
                return 'Hemija';
                break;
            case 7:
                return 'Seme';
                break;
            case 9:
                return 'Djubrivo';
                break;
            case 15:
                return 'Razna Roba';
                break;
        }
    }

    private function zamena2($br){
        switch ($br) {
            case 6:
                return 'Hemije';
                break;
            case 7:
                return 'Semena';
                break;
            case 9:
                return 'Djubriva';
                break;
            case 15:
                return 'Razne robe';
                break;
        }
    }

    public function get_results()
    {
        $napomena = "Rezultat pregleda ";
        header('Content-Type: application/json');

        if(isset($_GET['type_of_good'])){
            $all_type_of_goods = array($_GET['type_of_good']);
            $type_of_goods = array($this->zamena($_GET['type_of_good']));
            $napomena .= "robe tipa ".strtolower ($this->zamena($_GET['type_of_good']))." ";
        } else {
            $all_type_of_goods = array(6,7,9,15);
            $type_of_goods = array('Hemija','Seme','Djubrivo','Razna_Roba');
            $napomena .= "sve robe u ";
        }


        if(isset($_GET['wearehouse'])){
            $wearehouse_input = " AND input_records.wearehouse_id ='".$_GET['wearehouse']."'";
            $wearehouse_output = " AND output_records.wearehouse_id ='".$_GET['wearehouse']."'";
            $wearehouse_rezervacija = " AND reservation.wearehouse_id ='".$_GET['wearehouse']."'";
            $sql="SELECT wearehouses.wearehouse_name FROM wearehouses WHERE wearehouses.wearehouse_id = :wearehouse_id";
            $wearehouse = $this->model->get_values($sql, array(':wearehouse_id'=>$_GET['wearehouse']));
           // print_r($wearehouse[0]['wearehouse_name']);
            $napomena .= "u magacinu " . $wearehouse[0]['wearehouse_name']."";
            //return false;
        } else {
            $wearehouse= array(array("wearehouse_name"=>""));
            $wearehouse_input = "";
            $wearehouse_output = "";
            $wearehouse_rezervacija = "";
            $napomena .= ' svim magacinima.';
        }




        $input_result = array();
        $all_result = array();
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {
            foreach($all_type_of_goods AS $key=>$value)
            {

                $sql="SELECT goods.goods_id as goods_id, type_of_measurement_unit.measurement_unit, goods.goods_name FROM goods
                      INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                      WHERE goods.type_of_goods_id = :type_of_goods_id
                      ORDER BY goods.goods_name";
                $result = $this->model->get_values($sql, array(':type_of_goods_id'=>$value));

              foreach($result as $k=>$v)
                {

                    $sql_ulaz="SELECT
                            SUM(input_repromaterijal.kolicina) as input_sum
                          FROM
                            input_records
                          INNER JOIN
                            input_repromaterijal ON input_repromaterijal.input_id = input_records.input_id
                          INNER JOIN
                            goods ON goods.goods_id = input_repromaterijal.goods_id
                          WHERE
                            input_records.stornirano = :stornirano
                            ".$wearehouse_input."
                          AND
                            input_repromaterijal.goods_id = :goods_id";
                    $ulaz = $this->model->get_values($sql_ulaz, array(':stornirano'=>'n', ':goods_id'=>$v['goods_id']));

                    $sql_izlaz="SELECT SUM(output_repromaterijal.kolicina) as output_sum FROM output_records
                          INNER JOIN output_repromaterijal ON output_repromaterijal.output_id = output_records.output_id
                          INNER JOIN goods ON goods.goods_id = output_repromaterijal.goods_id
                          WHERE output_records.stornirano = :stornirano
                          ".$wearehouse_output."
                          AND output_repromaterijal.goods_id = :goods_id";
                    $izlaz = $this->model->get_values($sql_izlaz, array(':stornirano'=>'n', ':goods_id'=>$v['goods_id']));

                    $sql_rezervacija="SELECT SUM(reservation.kolicina) as rezervisano_sum FROM reservation
                          INNER JOIN goods ON goods.goods_id = reservation.goods_id
                          WHERE reservation.realizovana = :realizovana
                          AND reservation.stornirana = :stornirana
                          ".$wearehouse_rezervacija."
                          AND reservation.goods_id = :goods_id";
                    $rezervacija = $this->model->get_values($sql_rezervacija, array(':realizovana'=>'n', ':stornirana'=>'n', ':goods_id'=>$v['goods_id']));
                   // var_dump($izlaz);

                    $stanje = (float)$ulaz[0]['input_sum'] - (float)$izlaz[0]['output_sum'];
                    $stanje_rezervacija = $stanje - (float)$rezervacija[0]['rezervisano_sum'];
                    if($ulaz[0]['input_sum'] != 0.000 || $izlaz[0]['output_sum'] != 0.000 || $rezervacija[0]['rezervisano_sum']){
                        $input_result[$type_of_goods[$key]][] = array(
                            'goods_id' => $v['goods_id'],
                            'naziv_proizvoda'    => $v['goods_name'],
                            'merna_jedinica'     => $v['measurement_unit'],
                            'ulaz'               => number_format($ulaz[0]['input_sum'], 3, '.', ','),
                            'izlaz'              => number_format($izlaz[0]['output_sum'], 3, '.', ','),
                            'stanje_magacina'    => number_format($stanje, 3, '.', ','),
                            'rezervacija'        => number_format($rezervacija[0]['rezervisano_sum'], 3, '.', ','),
                            'stanje_rezervacija' => number_format($stanje_rezervacija, 3, '.', ',')
                        );
                    }
                }
            }
            array_push($all_result, $input_result);
            /* echo'<pre>';
            print_r($all_result[0]);*/
            echo json_encode(array('svi_rezultati'=>$all_result[0], 'napomena'=>$napomena, 'magacin'=>$wearehouse[0]['wearehouse_name']));
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    public function get_results_wearehouse()
    {
        $this->check_logedIn($_GET['session_id']);
        /*print_r(Session::get());  return false;
        $napomena = "Rezultat pregleda ";*/
        header('Content-Type: application/json');

        if(isset($_GET['type_of_good'])){
            $all_type_of_goods = array($_GET['type_of_good']);
            $type_of_goods = array($this->zamena($_GET['type_of_good']));
            $napomena .= "robe tipa ".strtolower ($this->zamena($_GET['type_of_good']))." ";
        } else {
            $all_type_of_goods = array(6,7,9,15);
            $type_of_goods = array('Hemija','Seme','Djubrivo','Razna_Roba');
            $napomena .= "sve robe u ";
        }


        if(isset($_GET['session_id'])){
            $wearehouse_input = " AND input_records.wearehouse_id ='".Session::get('wearehouse_id')."'";
            $wearehouse_output = " AND output_records.wearehouse_id ='".Session::get('wearehouse_id')."'";
            $wearehouse_rezervacija = " AND reservation.wearehouse_id ='".Session::get('wearehouse_id')."'";
            $sql="SELECT wearehouses.wearehouse_name FROM wearehouses WHERE wearehouses.wearehouse_id = :wearehouse_id";
            $wearehouse = $this->model->get_values($sql, array(':wearehouse_id'=>Session::get('wearehouse_id')));
            // print_r($wearehouse[0]['wearehouse_name']); return false;
            $napomena .= "u magacinu " . $wearehouse[0]['wearehouse_name']."";
            //return false;
        } else {
            $wearehouse_input = "";
            $wearehouse_output = "";
            $wearehouse_rezervacija = "";
            $napomena .= ' svim magacinima.';
        }




        $input_result = array();
        $all_result = array();
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if ($check_session['login'] == 1) {
            foreach($all_type_of_goods AS $key=>$value)
            {

                $sql="SELECT goods.goods_id as goods_id, type_of_measurement_unit.measurement_unit, goods.goods_name FROM goods
                      INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                      WHERE goods.type_of_goods_id = :type_of_goods_id
                      ORDER BY goods.goods_name";
                $result = $this->model->get_values($sql, array(':type_of_goods_id'=>$value));

                foreach($result as $k=>$v)
                {

                    $sql_ulaz="SELECT
                            SUM(input_repromaterijal.kolicina) as input_sum
                          FROM
                            input_records
                          INNER JOIN
                            input_repromaterijal ON input_repromaterijal.input_id = input_records.input_id
                          INNER JOIN
                            goods ON goods.goods_id = input_repromaterijal.goods_id
                          WHERE
                            input_records.stornirano = :stornirano
                            ".$wearehouse_input."
                          AND
                            input_repromaterijal.goods_id = :goods_id";
                    $ulaz = $this->model->get_values($sql_ulaz, array(':stornirano'=>'n', ':goods_id'=>$v['goods_id']));

                    $sql_izlaz="SELECT SUM(output_repromaterijal.kolicina) as output_sum FROM output_records
                          INNER JOIN output_repromaterijal ON output_repromaterijal.output_id = output_records.output_id
                          INNER JOIN goods ON goods.goods_id = output_repromaterijal.goods_id
                          WHERE output_records.stornirano = :stornirano
                          ".$wearehouse_output."
                          AND output_repromaterijal.goods_id = :goods_id";
                    $izlaz = $this->model->get_values($sql_izlaz, array(':stornirano'=>'n', ':goods_id'=>$v['goods_id']));

                    $sql_rezervacija="SELECT SUM(reservation.kolicina) as rezervisano_sum FROM reservation
                          INNER JOIN goods ON goods.goods_id = reservation.goods_id
                          WHERE reservation.realizovana = :realizovana
                          AND reservation.stornirana = :stornirana
                          ".$wearehouse_rezervacija."
                          AND reservation.goods_id = :goods_id";
                    $rezervacija = $this->model->get_values($sql_rezervacija, array(':realizovana'=>'n', ':stornirana'=>'n', ':goods_id'=>$v['goods_id']));
                    // var_dump($izlaz);

                    $stanje = (float)$ulaz[0]['input_sum'] - (float)$izlaz[0]['output_sum'];
                    $stanje_rezervacija = $stanje - (float)$rezervacija[0]['rezervisano_sum'];
                    if($ulaz[0]['input_sum'] != 0.000 || $izlaz[0]['output_sum'] != 0.000 || $rezervacija[0]['rezervisano_sum']){
                        $input_result[$type_of_goods[$key]][] = array(
                            'goods_id' => $v['goods_id'],
                            'naziv_proizvoda'    => $v['goods_name'],
                            'merna_jedinica'     => $v['measurement_unit'],
                            'ulaz'               => number_format($ulaz[0]['input_sum'], 3, '.', ','),
                            'izlaz'              => number_format($izlaz[0]['output_sum'], 3, '.', ','),
                            'stanje_magacina'    => number_format($stanje, 3, '.', ','),
                            'rezervacija'        => number_format($rezervacija[0]['rezervisano_sum'], 3, '.', ','),
                            'stanje_rezervacija' => number_format($stanje_rezervacija, 3, '.', ',')
                        );
                    }
                }
            }
            array_push($all_result, $input_result);
            /* echo'<pre>';
            print_r($all_result[0]);*/
            echo json_encode(array('svi_rezultati'=>$all_result[0], 'napomena'=>$napomena, 'magacin'=>$wearehouse[0]['wearehouse_name']));
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }






}


?>
