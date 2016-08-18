<?php
class Login extends Controller{
    public function __construct(){
        parent::__construct();

    }

    public function index(){

        $this->view->css = array(
            'node_modules/bootstrap/dist/css/bootstrap.min.css',
            'node_modules/font-awesome-4.4.0/css/font-awesome.min.css',
            'public/css/smartadmin-production.min.css'
        );
        $this->view->render('login/index', true);

    }

    public function login(){
        $data = array();

        $data['username'] = strip_tags($_POST['username']);
        $data['password_enc'] = Hash::create('sha1', strip_tags($_POST['password']), HESH_SALT);
        $result = $this->model->login($data);

        Session::init();
        //pasing autentification
        if(count($result) > 0){
          
            Session::set('user_id', $result[0]['user_id']);
            Session::set('user_name', $result[0]['name'].' '.$result[0]['surname']);
            Session::set('role', $result[0]['role']);
            Session::set('loggedIn', true);
           /* print_r($_SESSION);
            return false;*/
            //role redirection
            if($result[0]['role']=='Administrator'){
               // print_r($_SESSION);die;
                header('location:'.URL.'administrator');
                return false;
            } else if($result[0]['role']=='Redovan korisnik'){
                header('location:'.URL.'redovan_korisnik');
               return false;
            }else if($result[0]['role']=='Logistika'){
                header('location:'.URL.'logistika');
                return false;
            } else {
                //return to login page
                Session::destroy();
                header('location: '.URL);
                exit;
            }
        } else {
            //return to login page
            Session::destroy();
            header('location: '.URL);
            exit;
        }
    }

    public function login_ajax(){
        $data = array();
        $dat= json_decode(file_get_contents("php://input"));

        $data['username'] = strip_tags(trim($dat->username));
        $data['password_enc'] = Hash::create('sha1', strip_tags(trim($dat->password)), HESH_SALT);

        $result = $this->model->login($data);
       // print_r($result);
        Session::init();
        //pasing autentification
        header('Content-Type: application/json');
        if(count($result) > 0){

            Session::set('user_id', $result[0]['user_id']);
            Session::set('user_name', $result[0]['name'].' '.$result[0]['surname']);
            Session::set('wearehouse_id', $result[0]['wearehouse_id']);
            Session::set('wearehouse_name', $result[0]['wearehouse_name']);
            Session::set('role', $result[0]['role']);
            Session::set('loggedIn', true);
            Session::set('scale_port', $result[0]['scale_port']);
            Session::set('scale_type', $result[0]['scale_type']);
            Session::set('bruto_polje', $result[0]['bruto_polje']);

           // print_r($_SESSION);
            //role redirection
            if( $result[0]['role'] == 'Magacioner' ){
                //header('location:'.URL.'administrator');
                echo json_encode(array('success'=>1, 'session_id'=>session_id(), 'login_data'=>$_SESSION), JSON_NUMERIC_CHECK);

            } else {
                //return to login page
                echo json_encode(array('success'=>0, 'loginError'=>true) , JSON_NUMERIC_CHECK);

            }
          } else {
            echo json_encode(array('success'=>0) , JSON_NUMERIC_CHECK);
            }
    }


    public function logout_ajax(){
        Session::init();
        Session::destroy();
        header('Content-Type: application/json');
        echo json_encode(array('success'=>0), JSON_NUMERIC_CHECK);
    }

    public function logout(){
        Session::init();
        Session::destroy();
        header('location: '.URL);
        exit;
    }
}
?>