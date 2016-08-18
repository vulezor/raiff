<?php
class Dispozicije_Api extends Controller
{
    public function __consruct(){
        parent::__construct();

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

    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_all_goods(){
        $client_id = strip_tags($_GET['client_id']);
        $wearehouse_id = strip_tags($_GET['wearehouse_id']);
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $sql_repromaterijal = 'SELECT goods.*, type_of_measurement_unit.measurement_unit, type_of_measurement_unit.measurement_name FROM goods
                    INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                    WHERE goods.sort_of_goods_id="2"
                    OR goods.type_of_goods_id="6"
                    OR goods.type_of_goods_id="7"
                    OR goods.type_of_goods_id="9"
                    OR goods.type_of_goods_id="15"
                    ORDER BY goods_id';
            $repromaterijal = $this->model->get_values($sql_repromaterijal, $id=null);

            $sql_merkantila = 'SELECT goods.*, type_of_measurement_unit.measurement_unit, type_of_measurement_unit.measurement_name FROM goods
                    INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                    WHERE goods.sort_of_goods_id = "1"
                    AND goods.type_of_goods_id != "6"
                    AND goods.type_of_goods_id != "7"
                    AND goods.type_of_goods_id != "9"
                    AND goods.type_of_goods_id != "15"
                    ORDER BY goods_id';
            $merkantila = $this->model->get_values($sql_merkantila, $id=null);

            $sql_rezervisano = 'SELECT reservation.reservation_id, reservation.kolicina, goods.*, type_of_measurement_unit.measurement_unit, type_of_measurement_unit.measurement_name FROM reservation
                                INNER JOIN goods ON goods.goods_id = reservation.goods_id
                                INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                                WHERE reservation.wearehouse_id = :wearehouse_id
                                AND reservation.client_id = :client_id
                                AND reservation.realizovana = :realizovana
                                AND reservation.stornirana = :stornirana';
            $rezervisano = $this->model->get_values($sql_rezervisano, array(':wearehouse_id'=>$wearehouse_id, ':client_id'=>$client_id, ':realizovana'=>'n', ':stornirana'=>'n'));
           /* echo'<pre>';
            print_r(array('repromaterijal'=>$repromaterijal, 'merkantila'=>$merkantila, 'rezervisano'=>$rezervisano));*/
            header('Content-Type: application/json');
            echo json_encode(array('repromaterijal'=>$repromaterijal, 'merkantila'=>$merkantila, 'rezervisano'=>$rezervisano));
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }

    }


    public function save_disposition(){
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $data = json_decode(file_get_contents("php://input"));//take data from json object
            $date = new DateTime();

            //save dispozicija
            $dispozicija= array(
                "client_id"=>$data->client_id,
                "user_id"=>Session::get('user_id'),
                "wearehouse_id"=>$data->wearehouse_id,
                "datum_kreiranja"=>$date->format('Y-m-d H:i:s')
            );
            $dispozicija_id = $this->model->set_values('dispozicija',  $dispozicija); //vraca id dispozicije

            foreach($data->reservation as $obj){
                $vozilo_dispozicija= array(
                    "dispozicija_id"=>$dispozicija_id,
                    "ime_vozaca"=>$obj->vozac,
                    "reg_table"=>$obj->reg_table,
                    "datum_utovara"=>date('Y-m-d', strtotime($obj->datum_utovara)),
                    "krajnja_mesta"=>$obj->mesta_istovara
                );
                $vozilo_id = $this->model->set_values('dispozicija_vozila',  $vozilo_dispozicija); //vraca vozilo_id
                foreach($obj->goods as $good){
                    $dispozicija_stavke = array(
                        "vozilo_id"=>$vozilo_id,
                        "dispozicija_id"=>$dispozicija_id,
                        "goods_id"=>$good->goods_id,
                        "kolicina"=>$good->quantity,
                        "lot"=>$good->lot
                    );
                    if($good->sa_rezervacije === "y"){
                        $dispozicija_stavke['sa_rezervacije'] = $good->sa_rezervacije;
                        $dispozicija_stavke['reservation_id'] = $good->reservation_id;
                    }
                    $this->model->set_values('dispozicija_stavke',  $dispozicija_stavke); //vraca vozilo_id
                }
            }

            $this->send_email($dispozicija_id, $data->emails);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }



    public function send_email($dispozicija_id, $user_emails){
        $user_emails_arr = array();
        foreach ($user_emails as $value){
            $user_emails_arr[] = $value->email;
        }

        $emails = $user_emails_arr; //|| array('vulezor@gmail.com', 'zvulanovic@yahoo.com');
        $dispozicija = array();
       $sql = "SELECT
                dispozicija.dispozicija_id,
                clients.firm_name,
                clients.client_address,
                CONCAT(places.post_number, ' ', places.place_name ) as place,
                DATE(dispozicija.datum_kreiranja) as datum,
                wearehouses.wearehouse_name,
                CONCAT(users.name, ' ',users.surname) as name
                FROM dispozicija
                INNER JOIN clients ON clients.client_id = dispozicija.client_id
                INNER JOIN places ON places.place_id = clients.place_id
                INNER JOIN wearehouses ON wearehouses.wearehouse_id = dispozicija.wearehouse_id
                INNER JOIN users ON users.user_id = dispozicija.user_id
                WHERE dispozicija.dispozicija_id= :dispozicija_id AND dispozicija.stornirano= :stornirano";
        $osnovni_podaci = $this->model->get_values($sql, array(':dispozicija_id'=>$dispozicija_id, ':stornirano'=>"n"));
        $dispozicija['osnovni_podaci'] = $osnovni_podaci;

        $sql_vozila = "SELECT dispozicija_vozila.*, DATE(dispozicija_vozila.datum_utovara) as datum_utovara
                            FROM dispozicija_vozila
                            WHERE dispozicija_vozila.dispozicija_id= :dispozicija_id AND dispozicija_vozila.realizovano= :realizovano";
        $vozila = $this->model->get_values($sql_vozila, array(':dispozicija_id'=>$dispozicija_id, ':realizovano'=>"n"));

        $dispozicija['vozila'] = array();
        foreach($vozila as $vozilo){
            //print_r($vozilo);return false;
            $sql_stavka="SELECT
                        goods.goods_id,
                        goods.goods_name,
                        goods.goods_cypher,
                        sort_of_goods.goods_sort,
                        type_of_goods.goods_type,
                        dispozicija_stavke.kolicina,
                        dispozicija_stavke.lot,
                        type_of_measurement_unit.measurement_unit,
                        type_of_measurement_unit.measurement_name
                        FROM dispozicija_stavke
                        INNER JOIN goods ON ( goods.goods_id = dispozicija_stavke.goods_id )
                        INNER JOIN sort_of_goods ON sort_of_goods.sort_of_goods_id = goods.sort_of_goods_id
                        INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = goods.type_of_goods_id
                        INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                        WHERE dispozicija_stavke.vozilo_id= :vozilo_id";
            $stavke = $this->model->get_values($sql_stavka, array(':vozilo_id'=>$vozilo['vozilo_id']));
            $vozilo['roba'] = $stavke;
            $dispozicija['vozila'][] = $vozilo;
        }

        Session::init();
        $email_user = "SELECT email FROM users WHERE user_id= :user_id";
        $email = $this->model->get_values($email_user, array(':user_id'=>Session::get('user_id')));

        $email = $email[0]['email'];//"noriply@otkupsirovina.com";

        $to = implode(", ",$emails);

        $subject = 'Dispozicija - Nalog za utovar br: '.$dispozicija['osnovni_podaci'][0]['dispozicija_id'];

        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: ". $email . "\r\n";
        $headers .= "CC: ".$to."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $message = '<html><body>';

        $message .= '<div style="width:700px;font-family:arial;font-size:12px;line-height:1.5">';
        $message .= '<img src="'.URL.'public/img/raiffeisen_redovan2.png" width="300" alt="Raiffeisen Agro" />';
        $message .= '<center><h2>Dispozicija - Nalog za utovar br: '.$dispozicija['osnovni_podaci'][0]['dispozicija_id'].' </h2></center>';
        $message .= '<b>Dokument kreirao/la:</b>  '.$dispozicija['osnovni_podaci'][0]['name'].' <b>Dana:</b> '.date('d.m.Y', strtotime($dispozicija['osnovni_podaci'][0]['datum'])).'<br/><br/>';
        $message .= '<b>Nalog za utovar iz magacina '.$dispozicija['osnovni_podaci'][0]['wearehouse_name'].' za:</b><br/>';
        $message .= '<b>Naziv firme/gazdinstva:</b> '.$dispozicija['osnovni_podaci'][0]['firm_name'].'<br/>';
        $message .= '<b>Adresa i mesto:</b> '.$dispozicija['osnovni_podaci'][0]['client_address'].', '.$dispozicija['osnovni_podaci'][0]['place'].'<br/><br/>';
       $br = 1;
        foreach($dispozicija['vozila'] as $vozi){
           // print_r($vozi);
            $message .='<b><u>Vozilo: '.$br.'</u></b><br/>';
            $message .='<b>Vozac i registracija:</b> '.$vozi['ime_vozaca'].' '.$vozi['reg_table'].'<br/>';
            $message .='<b>Datum utovara:</b> '.$vozi['datum_utovara'].'<br/>';
            $message .='<b>Krajnja mesta otpreme:</b> '.$vozi['krajnja_mesta'].'<br/>';
            $message .='<table cellpadding="3" cellspacing="0" width="100%" style="font-size:12px"><thead>';
            $message .='<tr><td width="50%"><b>Naziv robe</b></td>';
            $message .='<td  width="10%"><b>Lot</b></td>';
            $message .='<td  width="20%" align="right"><b>Kolicina</b></td>';
            $message .='<td  width="20%"><b>Merna jedinica</b></td></tr>';
            $message .='</thead>';
            $message .='<tbody>';
           // print_r($vozi);
            $brd = 1;
            foreach($vozi['roba'] as $value){
                //print_r($vozilo['roba']);
                $message .='<tr><td width="50%">'.$brd.') '.$value['goods_name'].'</td>';
                $message .='<td width="10%">&nbsp;'.$value['lot'].'</td>';
                $message .='<td width="25%" align="right">'.$value['kolicina'].'</td>';
                $message .='<td width="20%">'.$value['measurement_unit'].' </td></tr>';
                $brd++;
            }
            $message .='</tbody>';
            $message .='</table>';
            $message .='<hr />';

            $br++;
        }
        $message .='<p style="font-size:11px;color:#999999">Ova elektronska posta je kreirana elektronskim putem i prosleđene informacije su namenjene isključivo osobama ili entitetima na koje je poruka adresirana. Bilo kakvo širenje informacija, njihovo kopiranje i parafraziranje ili bilo koja druga upotreba ili preduzimanje akcije od strane lica ili entiteta na koje poruka nije adresirana, a vezano za informacije iz poruke i bez izričite dozvole pošiljaoca se zabranjuje. Pošiljalac nije odgovoran ni za kakav prenos komunikacija, niti za kašnjenje poruke.</p>';
        $message .= "</body></html>";
        //echo $message;
        mail($to, $subject, $message, $headers);
    }


    //--------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function get_disposition(){
        Ajax::ajaxCheck();
        if(isset($_GET['session_id'])){
            $check_session = $this->check_logedIn(strip_tags($_GET['session_id'])); //checking if session exists
            if( $check_session['login'] == 1){
                Session::init();
                $sql = "SELECT
                dispozicija.dispozicija_id,
                clients.firm_name,
                clients.client_address,
                CONCAT(places.post_number, ' ', places.place_name ) as place,
                DATE(dispozicija.datum_kreiranja) as datum,
                wearehouses.wearehouse_name,
                CONCAT(users.name, ' ',users.surname) as name
                FROM dispozicija
                INNER JOIN clients ON clients.client_id = dispozicija.client_id
                INNER JOIN places ON places.place_id = clients.place_id
                INNER JOIN wearehouses ON wearehouses.wearehouse_id = dispozicija.wearehouse_id
                INNER JOIN users ON users.user_id = dispozicija.user_id
                WHERE dispozicija.stornirano= :stornirano
                AND dispozicija.realizovana= :realizovana
                AND dispozicija.wearehouse_id= :wearehouse_id";
                $osnovni_podaci = $this->model->get_values($sql, array(':stornirano'=>"n", ':realizovana'=>"n", ':wearehouse_id'=>Session::get('wearehouse_id')));
                header('Content-Type: application/json');
                echo json_encode($osnovni_podaci);
            } else {
                echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
            }
        }
    }


    public function view_disposition(){
        Ajax::ajaxCheck();
       // var_dump($_GET);return false;
        $check_session = $this->check_logedIn(strip_tags($_GET['session_id'])); //checking if session exists //checking if session exists
        $dispozicija_id = strip_tags($_GET['dispozicija_id']);
        if( $check_session['login'] == 1){
            $dispozicija = array();
            $sql = "SELECT
                dispozicija.dispozicija_id,
                clients.client_id,
                clients.firm_name,
                clients.client_address,
                CONCAT(places.post_number, ' ', places.place_name ) as place,
                DATE(dispozicija.datum_kreiranja) as datum,
                wearehouses.wearehouse_name,
                CONCAT(users.name, ' ',users.surname) as name
                FROM dispozicija
                INNER JOIN clients ON clients.client_id = dispozicija.client_id
                INNER JOIN places ON places.place_id = clients.place_id
                INNER JOIN wearehouses ON wearehouses.wearehouse_id = dispozicija.wearehouse_id
                INNER JOIN users ON users.user_id = dispozicija.user_id
                INNER JOIN dispozicija_vozila ON dispozicija_vozila.dispozicija_id = dispozicija.dispozicija_id
                WHERE dispozicija.dispozicija_id= :dispozicija_id AND dispozicija.stornirano= :stornirano";
            $osnovni_podaci = $this->model->get_values($sql, array(':dispozicija_id'=>$dispozicija_id, ':stornirano'=>"n"));
            $dispozicija['osnovni_podaci'] = $osnovni_podaci;

            $sql_vozila = "SELECT dispozicija_vozila.*, DATE(dispozicija_vozila.datum_utovara) as datum_utovara
                            FROM dispozicija_vozila
                            WHERE dispozicija_vozila.dispozicija_id= :dispozicija_id ";
            $vozila = $this->model->get_values($sql_vozila, array(':dispozicija_id'=>$dispozicija_id));//':realizovano'=>"n" AND dispozicija_vozila.realizovano= :realizovano

            $dispozicija['vozila'] = array();
            foreach($vozila as $vozilo){
                //print_r($vozilo);return false;
                $sql_stavka="SELECT
                        dispozicija_stavke.stavka_id,
                        goods.goods_id,
                        goods.goods_name,
                        goods.goods_cypher,
                        sort_of_goods.goods_sort,
                        type_of_goods.goods_type,
                        dispozicija_stavke.kolicina,
                        dispozicija_stavke.lot,
                        type_of_measurement_unit.measurement_unit,
                        type_of_measurement_unit.measurement_name
                        FROM dispozicija_stavke
                        INNER JOIN goods ON ( goods.goods_id = dispozicija_stavke.goods_id )
                        INNER JOIN sort_of_goods ON sort_of_goods.sort_of_goods_id = goods.sort_of_goods_id
                        INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = goods.type_of_goods_id
                        INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                        WHERE dispozicija_stavke.vozilo_id= :vozilo_id";
                $stavke = $this->model->get_values($sql_stavka, array(':vozilo_id'=>$vozilo['vozilo_id']));
                $vozilo['roba'] = $stavke;
                $dispozicija['vozila'][] = $vozilo;
            }
            echo json_encode($dispozicija);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }


    public function change_vozilo()
    {
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        $check_session = $this->check_logedIn(strip_tags($data->session_id)); //checking if session exists //checking if session exists
        $vozilo_id = strip_tags($data->vozilo_id);
        $ime_vozaca = strip_tags($data->ime_vozaca);
        $reg_table = strip_tags($data->reg_table);
        $lot = strip_tags($data->lot);
        if ($check_session['login'] == 1) {
            $date = new DateTime();
            $obj = array(
                'ime_vozaca' => $ime_vozaca,
                'reg_table' => $reg_table
            );
            $where = 'vozilo_id="' . $vozilo_id . '"';
            $this->model->update_values('dispozicija_vozila', $obj, $where); //vraca vozilo_id
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }


    public function change_kolicinu()
    {
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        $check_session = $this->check_logedIn(strip_tags($data->session_id)); //checking if session exists //checking if session exists
        $stavka_id = strip_tags($data->stavka_id);
        $kolicina = strip_tags($data->kolicina);
        $lot = strip_tags($data->lot);
        if ($check_session['login'] == 1) {
            $date = new DateTime();
            $obj = array(
                'kolicina' => $kolicina,
                'lot' => $lot,
                'datum_izmene' => $date->format('Y-m-d H:i:s'),
                'izmenio_user_id' => Session::get('user_id')
            );
            $where = 'stavka_id="' . $stavka_id . '"';
            $this->model->update_values('dispozicija_stavke', $obj, $where); //vraca vozilo_id
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

    }






    public function change_kolicinuAdmin()
    {
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        $check_session = $this->check_logedIn_admin(); //checking if session exists //checking if session exists
        $stavka_id = strip_tags($data->stavka_id);
        $kolicina = strip_tags($data->kolicina);
        $lot = strip_tags($data->lot);
        if ($check_session['login'] == 1) {
            $date = new DateTime();
            $obj = array(
                'kolicina' => $kolicina,
                'lot' => $lot,
                'datum_izmene' => $date->format('Y-m-d H:i:s'),
                'izmenio_user_id' => Session::get('user_id')
            );
            $where = 'stavka_id="' . $stavka_id . '"';
            $this->model->update_values('dispozicija_stavke', $obj, $where); //vraca vozilo_id
        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }

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

    public function realizacija_stavke_dispozicije(){
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
       // print_r($data);return false;
        $check_session = $this->check_logedIn(strip_tags($data->session_id)); //checking if session exists //checking if session exists
        $vozilo_id = strip_tags($data->vozilo_id);
        $end_point = strip_tags($data->end_point);
        if ($check_session['login'] == 1) {



            $sql_vozila = "SELECT dispozicija.dispozicija_id, clients.client_id, dispozicija_vozila.*
                            FROM dispozicija_vozila
                            INNER JOIN dispozicija ON dispozicija.dispozicija_id = dispozicija_vozila.dispozicija_id
                            INNER JOIN clients ON clients.client_id = dispozicija.client_id
                            WHERE dispozicija_vozila.vozilo_id= :vozilo_id
                            AND dispozicija_vozila.realizovano= :realizovano";
            $vozila = $this->model->get_values($sql_vozila, array(':vozilo_id'=>$vozilo_id, ':realizovano'=>"n"));

            $sql_stavka="SELECT
                        goods.goods_id,
                        goods.goods_name,
                        goods.goods_cypher,
                        sort_of_goods.goods_sort,
                        type_of_goods.goods_type,
                        sort_of_goods.sort_of_goods_id,
                        type_of_goods.type_of_goods_id,
                        dispozicija_stavke.kolicina,
                        dispozicija_stavke.lot,
                        dispozicija_stavke.sa_rezervacije,
                        dispozicija_stavke.reservation_id,
                        type_of_measurement_unit.measurement_unit,
                        type_of_measurement_unit.measurement_name
                        FROM dispozicija_stavke
                        INNER JOIN goods ON ( goods.goods_id = dispozicija_stavke.goods_id )
                        INNER JOIN sort_of_goods ON sort_of_goods.sort_of_goods_id = goods.sort_of_goods_id
                        INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = goods.type_of_goods_id
                        INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                        WHERE dispozicija_stavke.vozilo_id= :vozilo_id";
            $stavke = $this->model->get_values($sql_stavka, array(':vozilo_id'=>$vozilo_id));
            $vozila['stavke'] = $stavke;
           /* echo"<pre>";
            print_r($vozila);return false;*/

                $document_br = $this->set_document_number();
            if($vozila['stavke'][0]['goods_sort']==="repromaterijal"){ //ako je repromaterijal

                $output_records = array(
                    'document_br'=>$document_br,
                    'user_id'=>Session::get('user_id'),
                    'wearehouse_id'=>Session::get('wearehouse_id'),
                    'client_id'=>$vozila[0]['client_id'],
                    'output_date'=>date('Y-m-d H:i:s'),
                    'end_point'=>$end_point,
                    'exit_date'=>date('Y-m-d H:i:s'),
                    'driver_name'=>$vozila[0]['ime_vozaca'],
                    'vehicle_registration'=>$vozila[0]['reg_table'],
                    'dispozicija_id'=>$vozila[0]['dispozicija_id'],
                );
                $output_id = $this->model->set_values('output_records', $output_records);

                foreach($vozila['stavke'] as $stavka){
                    $st = array(
                        'output_id'=>$output_id,
                        'sort_of_goods_id'=>$stavka['sort_of_goods_id'],
                        'type_of_goods_id'=>$stavka['type_of_goods_id'],
                        'goods_id'=>$stavka['goods_id'],
                        'lot'=>$stavka['lot'],
                        'kolicina'=>$stavka['kolicina']
                    );
                    $this->model->set_values('output_repromaterijal', $st);
                    if($stavka['sa_rezervacije'] == "y"){
                        $where =  "reservation_id='".$stavka['reservation_id']."'";
                        $this->model->update_values('reservation', array('realizovana'=>"y"), $where); //ako je sa rezerrvacije skida stavku sa rezervacije
                    }
                }
                $update_vozilo = array(
                    'realizovano'=>'y',
                    'datum_realizacije'=>date('Y-m-d H:i:s'),
                    'realizovao_id'=>Session::get('user_id')
                );
                $where = "vozilo_id='".$vozilo_id."'";
                $this->model->update_values('dispozicija_vozila', $update_vozilo, $where); //vraca vozilo_id
                $this->realizuj_dispoziciju($vozila[0]['dispozicija_id']);
                echo json_encode(array('success'=>1));


            }else{//ako je merkantila

                $update_vozilo = array(
                    'realizovano'=>'y',
                    'datum_realizacije'=>date('Y-m-d H:i:s'),
                    'realizovao_id'=>Session::get('user_id')
                );
                $where = "vozilo_id='".$vozilo_id."'";
                $this->model->update_values('dispozicija_vozila', $update_vozilo, $where); //vraca vozilo_id
                $this->realizuj_dispoziciju($vozila[0]['dispozicija_id']);
            }




        } else {
            echo json_encode(array('logedIn' => 0), JSON_NUMERIC_CHECK);
        }
    }


    public function realizuj_dispoziciju($dispozicija_id){
        $sql_vozila = "SELECT dispozicija.dispozicija_id, clients.client_id, dispozicija_vozila.*
                            FROM dispozicija_vozila
                            INNER JOIN dispozicija ON dispozicija.dispozicija_id = dispozicija_vozila.dispozicija_id
                            INNER JOIN clients ON clients.client_id = dispozicija.client_id
                            WHERE dispozicija.dispozicija_id = :dispozicija_id
                            AND dispozicija_vozila.realizovano= :realizovano";
        $vozila = $this->model->check_exists($sql_vozila, array(':dispozicija_id'=>$dispozicija_id, ':realizovano'=>"n"));
        if(!$vozila){
            echo 'snimam';
            $where = 'dispozicija_id ="'.$dispozicija_id.'"';
            $this->model->update_values('dispozicija', array('realizovana'=>'y'), $where);
        }
    }

    public function get_dispozicija(){
        $sql = "SELECT dispozicija.*, DATE(dispozicija.datum_kreiranja) AS datum_kreiranja, clients.firm_name, wearehouses.wearehouse_name, CONCAT (users.name, ' ', users.surname) AS user_name
                FROM dispozicija
                INNER JOIN users ON users.user_id = dispozicija.user_id
                INNER JOIN clients ON clients.client_id = dispozicija.client_id
                INNER JOIN wearehouses ON wearehouses.wearehouse_id = dispozicija.wearehouse_id
                WHERE dispozicija.stornirano= :stornirano
                ORDER BY dispozicija_id DESC";
        $d = $this->model->get_values($sql, array(':stornirano'=>"n"));
        echo json_encode($d);
        /*$dispozicija = array();
       $sql = "SELECT
                dispozicija.dispozicija_id,
                clients.client_id,
                clients.firm_name,
                clients.client_address,
                CONCAT(places.post_number, ' ', places.place_name ) as place,
                DATE(dispozicija.datum_kreiranja) as datum,
                wearehouses.wearehouse_name,
                CONCAT(users.name, ' ',users.surname) as user_name,
                DATE(dispozicija.datum_kreiranja) AS datum_kreiranja
                FROM dispozicija
                INNER JOIN clients ON clients.client_id = dispozicija.client_id
                INNER JOIN places ON places.place_id = clients.place_id
                INNER JOIN wearehouses ON wearehouses.wearehouse_id = dispozicija.wearehouse_id
                INNER JOIN users ON users.user_id = dispozicija.user_id
                INNER JOIN dispozicija_vozila ON dispozicija_vozila.dispozicija_id = dispozicija.dispozicija_id
                WHERE dispozicija.stornirano= :stornirano";
        $osnovni_podaci = $this->model->get_values($sql, array(':stornirano'=>"n"));
        foreach($osnovni_podaci AS $key=>$value){
            $sql_vozila = "SELECT dispozicija_vozila.*, DATE(dispozicija_vozila.datum_utovara) as datum_utovara
                                FROM dispozicija_vozila
                                WHERE dispozicija_vozila.dispozicija_id= :dispozicija_id ";
            $vozila = $this->model->get_values($sql_vozila, array(':dispozicija_id'=>$value['dispozicija_id']));//':realizovano'=>"n" AND dispozicija_vozila.realizovano= :realizovano

            foreach($vozila as $vozilo){

                $sql_stavka="SELECT
                            dispozicija_stavke.stavka_id,
                            goods.goods_id,
                            goods.goods_name,
                            goods.goods_cypher,
                            sort_of_goods.goods_sort,
                            type_of_goods.goods_type,
                            dispozicija_stavke.kolicina,
                            dispozicija_stavke.lot,
                            type_of_measurement_unit.measurement_unit,
                            type_of_measurement_unit.measurement_name
                            FROM dispozicija_stavke
                            INNER JOIN goods ON ( goods.goods_id = dispozicija_stavke.goods_id )
                            INNER JOIN sort_of_goods ON sort_of_goods.sort_of_goods_id = goods.sort_of_goods_id
                            INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = goods.type_of_goods_id
                            INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                            WHERE dispozicija_stavke.vozilo_id= :vozilo_id";
                $stavke = $this->model->get_values($sql_stavka, array(':vozilo_id'=>$vozilo['vozilo_id']));

              //  array_merge($value, array('vozila'=>$vozilo));
            }
            array_merge($vozilo, array('stavke'=>$stavke));
            array_merge($osnovni_podaci[$key], $vozilo);
        }
        header('Content-Type: application/json');
        echo json_encode($osnovni_podaci);*/
    }

    //----------------------------------------------------------------------------------------------------------------------------------------------------

    public function storniraj_dokument()
    {
        Ajax::ajaxCheck();
        $data = json_decode(file_get_contents("php://input"));//take data from json object
        // print_r($data);return false;
        $check_session = $this->check_logedIn_admin(); //checking if session exists
        if( $check_session['login'] == 1){
            $table = 'dispozicija';
            $date = new DateTime();
            $new_data = array(
                'stornirano'       => 'y',
                'stornirano_napomena'  => $data->napomena,
                'stornirano_datum' => $date = $date->format('Y-m-d H:i:s'),
                "stornirao_id"     => Session::get('user_id')
            );
            //print_r($new_data);
            $where = 'dispozicija_id="' . $data->dispozicija_id . '"';
            $this->model->update_values($table, $new_data, $where);
            header('Content-Type: application/json');
            echo json_encode(array('success' => 1));
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }


    public function view_dispositionAdmin(){
        Ajax::ajaxCheck();
        $check_session = $this->check_logedIn_admin(); //checking if session exists //checking if session exists
        $dispozicija_id = strip_tags($_GET['dispozicija_id']);
        if( $check_session['login'] == 1){
            $dispozicija = array();
            $sql = "SELECT
                dispozicija.dispozicija_id,
                clients.client_id,
                clients.firm_name,
                clients.client_address,
                CONCAT(places.post_number, ' ', places.place_name ) as place,
                DATE(dispozicija.datum_kreiranja) as datum,
                wearehouses.wearehouse_name,
                CONCAT(users.name, ' ',users.surname) as name
                FROM dispozicija
                INNER JOIN clients ON clients.client_id = dispozicija.client_id
                INNER JOIN places ON places.place_id = clients.place_id
                INNER JOIN wearehouses ON wearehouses.wearehouse_id = dispozicija.wearehouse_id
                INNER JOIN users ON users.user_id = dispozicija.user_id
                INNER JOIN dispozicija_vozila ON dispozicija_vozila.dispozicija_id = dispozicija.dispozicija_id
                WHERE dispozicija.dispozicija_id= :dispozicija_id AND dispozicija.stornirano= :stornirano";
            $osnovni_podaci = $this->model->get_values($sql, array(':dispozicija_id'=>$dispozicija_id, ':stornirano'=>"n"));
            $dispozicija['osnovni_podaci'] = $osnovni_podaci;

            $sql_vozila = "SELECT dispozicija_vozila.*, DATE(dispozicija_vozila.datum_utovara) as datum_utovara
                            FROM dispozicija_vozila
                            WHERE dispozicija_vozila.dispozicija_id= :dispozicija_id ";
            $vozila = $this->model->get_values($sql_vozila, array(':dispozicija_id'=>$dispozicija_id));//':realizovano'=>"n" AND dispozicija_vozila.realizovano= :realizovano

            $dispozicija['vozila'] = array();
            foreach($vozila as $vozilo){
                $sql_stavka="SELECT
                        dispozicija_stavke.stavka_id,
                        goods.goods_id,
                        goods.goods_name,
                        goods.goods_cypher,
                        sort_of_goods.goods_sort,
                        type_of_goods.goods_type,
                        dispozicija_stavke.kolicina,
                        dispozicija_stavke.lot,
                        type_of_measurement_unit.measurement_unit,
                        type_of_measurement_unit.measurement_name
                        FROM dispozicija_stavke
                        INNER JOIN goods ON ( goods.goods_id = dispozicija_stavke.goods_id )
                        INNER JOIN sort_of_goods ON sort_of_goods.sort_of_goods_id = goods.sort_of_goods_id
                        INNER JOIN type_of_goods ON type_of_goods.type_of_goods_id = goods.type_of_goods_id
                        INNER JOIN type_of_measurement_unit ON type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id
                        WHERE dispozicija_stavke.vozilo_id= :vozilo_id";
                $stavke = $this->model->get_values($sql_stavka, array(':vozilo_id'=>$vozilo['vozilo_id']));
                $vozilo['roba'] = $stavke;
                $dispozicija['vozila'][] = $vozilo;
            }
            echo json_encode($dispozicija);
        } else {
            echo json_encode(array('logedIn'=>0), JSON_NUMERIC_CHECK);
        }
    }
}
?>