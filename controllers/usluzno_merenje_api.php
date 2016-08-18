<?php
class Usluzno_Merenje_Api extends Controller
{
    public function __construct()
    {
        parent::__construct();
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

    public function prvo_merenje($session_id){
        header('Content-Type: application/json');
        $check_session = $this->check_logedIn($session_id); //checking if session exists
        if( $check_session['login'] == 1){
           /* $sql = "SELECT *, clients.firm_name AS firm_name, clients.client_address AS adresa,
                    CONCAT(places.post_number, ' ', places.place_name),
                    usluzno_merenje.*, DATE ( datum_ulaza ) as datum,
                    TIME( usluzno_merenje.datum_ulaza ) AS time
                    FROM usluzno_merenje
                    INNER JOIN users ON ( users.user_id = usluzno_merenje.user_id )
                    INNER JOIN clients ON ( clients.client_id = usluzno_merenje.client_id )
                    INNER JOIN places ON ( places.place_id = clients.place_id )
                    WHERE usluzno_merenje.datum_izlaza = :datum_izlaza AND usluzno_merenje.wearehouse_id= :wearehouse_id ORDER BY usluzno_id";*/
            $sql = 'SELECT wearehouses.wearehouse_name, CONCAT(users.name, " ", users.surname) as storekeeper, clients.firm_name AS firm_name, clients.client_address AS adresa,
                CONCAT(places.post_number, " ", places.place_name),
                usluzno_merenje.*, DATE ( datum_ulaza ) as datum,
                TIME( usluzno_merenje.datum_ulaza ) AS time
                FROM usluzno_merenje
                INNER JOIN wearehouses ON ( wearehouses.wearehouse_id = usluzno_merenje.wearehouse_id )
                INNER JOIN users ON ( users.user_id = usluzno_merenje.user_id )
                INNER JOIN clients ON ( clients.client_id = usluzno_merenje.client_id )
                INNER JOIN places ON ( places.place_id = clients.place_id )
                WHERE usluzno_merenje.datum_izlaza = :datum_izlaza AND usluzno_merenje.wearehouse_id= :wearehouse_id ORDER BY usluzno_id';
            $result = $this->model->get_values($sql, array(':datum_izlaza'=> '0000-00-00 00:00:00', ':wearehouse_id'=>Session::get('wearehouse_id')));
            echo json_encode($result);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }



    public function first_measurement()
    {
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){
            $date = new DateTime();
            $obj = array(
                    'user_id'=>Session::get('user_id'),
                    'wearehouse_id'=>Session::get('wearehouse_id'),
                    'datum_ulaza'=>$date->format('Y-m-d H:i:s'),
                    'client_id' => $data->client_id,
                    'good_name' => $data->good_name,
                    'vozac' => $data->vozac,
                    'reg_vozila' => $data->registracija,
                    'tara'=> $data->tara,
                    'bruto'=> $data->bruto
                );
            header('Content-Type: application/json');
            $result = $this->model->set_values('usluzno_merenje', $obj);
           // echo $result;
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    public function second_measurement()
    {
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));
        $check_session = $this->check_logedIn($data->session_id); //checking if session exists
        if( $check_session['login'] == 1){
            $sql = "SELECT *, DATE(datum_ulaza) as datum FROM usluzno_merenje WHERE datum_izlaza = '0000-00-00 00:00:00' AND usluzno_id= :usluzno_id AND wearehouse_id= :wearehouse_id";
            $obj  = array(
                ':usluzno_id'=>$data->usluzno_id,
                ':wearehouse_id'=>Session::get('wearehouse_id')
            );
            $result = $this->model->get_values($sql, $obj);
            $date = new DateTime();
            if($result[0]['bruto'] > 0){
                $neto = $result[0]['bruto'] - $data->vaga;
            } else {
                $neto =  $data->vaga - $result[0]['tara'];
            }
            $obj = array(
                'datum_izlaza'=>$date->format('Y-m-d H:i:s'),
                'neto' => $neto,

            );
            if($result[0]['bruto'] > 0){
                $obj['tara'] =  $data->vaga;
            } else {
                $obj['bruto'] =  $data->vaga;
            }

            header('Content-Type: application/json');
            $where = 'usluzno_id='.$data->usluzno_id;
            $this->model->update_values('usluzno_merenje', $obj, $where);
            // echo $result;
            echo json_encode($result);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }


    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function select_last_input($session_id=null){
        $data = json_decode(file_get_contents("php://input"));
        $session = $session_id==null ? (string) $data->session_id : (string) $session_id;
        $check_session = $this->check_logedIn($session);
        //print_r(Session::get('wearehouse_id'));return false;
        header('Content-Type: application/json');

        if( $check_session['login'] == 1){
            $sql='SELECT wearehouses.wearehouse_name, CONCAT(users.name, " ", users.surname) as storekeeper, clients.firm_name AS firm_name, clients.client_address AS adresa,
                CONCAT(places.post_number, " ", places.place_name) AS mesto,
                usluzno_merenje.*, DATE_FORMAT(DATE(usluzno_merenje.datum_ulaza),"%d.%m.%Y") AS date,
                TIME(usluzno_merenje.datum_ulaza) AS time
                FROM usluzno_merenje
                INNER JOIN wearehouses ON (wearehouses.wearehouse_id = usluzno_merenje.wearehouse_id)
                INNER JOIN users ON (users.user_id = usluzno_merenje.user_id)
                INNER JOIN clients ON (clients.client_id = usluzno_merenje.client_id)
                INNER JOIN places ON (places.place_id = clients.place_id)
                WHERE usluzno_merenje.wearehouse_id= :wearehouse_id AND datum_izlaza !="0000-00-00 00:00:00"
                ORDER BY usluzno_merenje.datum_izlaza DESC LIMIT 1';
            $result = $this->model->get_values($sql, array(":wearehouse_id"=>Session::get('wearehouse_id')));
            echo json_encode($result);

        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }

    public function get_prijemnice($session_id=null){
        $data = json_decode(file_get_contents("php://input"));
        $session = $session_id==null ? (string) $data->session_id : (string) $session_id;
        $check_session = $this->check_logedIn($session);
        /*print_r($session);*/
        $sql = 'SELECT wearehouses.wearehouse_name,
                CONCAT(users.name, " ", users.surname) AS storekeeper,
                clients.firm_name AS firm_name,
                clients.client_address AS adresa,
                CONCAT(places.post_number, " ", places.place_name) AS place_name,
                usluzno_merenje.*, DATE ( datum_ulaza ) as datum,
                TIME( usluzno_merenje.datum_ulaza ) AS time
                FROM usluzno_merenje
                INNER JOIN wearehouses ON ( wearehouses.wearehouse_id = usluzno_merenje.wearehouse_id )
                INNER JOIN users ON ( users.user_id = usluzno_merenje.user_id )
                INNER JOIN clients ON ( clients.client_id = usluzno_merenje.client_id )
                INNER JOIN places ON ( places.place_id = clients.place_id )
                WHERE usluzno_merenje.wearehouse_id= :wearehouse_id ORDER BY usluzno_id';
        header('Content-Type: application/json');
        $result = $this->model->get_values($sql, array(':wearehouse_id'=>Session::get('wearehouse_id')));
       /* foreach($result as $key=>$value){
            $result[$key]['usluzno_id'] = is_int($result[$key]['usluzno_id']) ? (int)$result[$key]['usluzno_id'] : (int)$result[$key]['usluzno_id'];
           // var_dump($result[$key]['usluzno_id']);
           // var_dump(is_int($result[$key]['usluzno_id']));
        }*/
        //var_dump(is_int($result[0]['usluzno_id']));
        echo json_encode($result, JSON_NUMERIC_CHECK);
        //print_r($session_id);
    }

    public function get_usluzno($session_id=null){
        $data = json_decode(file_get_contents("php://input"));
        $session = $session_id==null ? (string) $data->session_id : (string) $session_id;
        $check_session = $this->check_logedIn($session);
        /*print_r($session);*/
        $sql = 'SELECT usluzno_merenje.usluzno_id AS br_prijemnice,
                DATE_FORMAT(DATE ( datum_ulaza ),"%d.%m.%Y") as datum,
                clients.firm_name AS naziv_firme,
                clients.client_address AS adresa_firme,
                CONCAT(places.post_number, " ", places.place_name) AS mesto_firme,
                usluzno_merenje.good_name AS roba_za_merenje,
                usluzno_merenje.vozac,
                usluzno_merenje.reg_vozila AS registracija_vozila,
                usluzno_merenje.tara,
                usluzno_merenje.bruto,
                usluzno_merenje.neto,
                wearehouses.wearehouse_name AS magacin,
                CONCAT(users.name, " ", users.surname) AS magacioner,

                TIME( usluzno_merenje.datum_ulaza ) AS vreme
                FROM usluzno_merenje
                INNER JOIN wearehouses ON ( wearehouses.wearehouse_id = usluzno_merenje.wearehouse_id )
                INNER JOIN users ON ( users.user_id = usluzno_merenje.user_id )
                INNER JOIN clients ON ( clients.client_id = usluzno_merenje.client_id )
                INNER JOIN places ON ( places.place_id = clients.place_id )
                WHERE usluzno_merenje.wearehouse_id= :wearehouse_id ORDER BY usluzno_merenje.usluzno_id';
        header('Content-Type: application/json');
        $result = $this->model->get_values($sql, array(':wearehouse_id'=>Session::get('wearehouse_id')));
        /* foreach($result as $key=>$value){
             $result[$key]['usluzno_id'] = is_int($result[$key]['usluzno_id']) ? (int)$result[$key]['usluzno_id'] : (int)$result[$key]['usluzno_id'];
            // var_dump($result[$key]['usluzno_id']);
            // var_dump(is_int($result[$key]['usluzno_id']));
         }*/
        //var_dump(is_int($result[0]['usluzno_id']));
        echo json_encode($result, JSON_NUMERIC_CHECK);
        //print_r($session_id);
    }
}

?>