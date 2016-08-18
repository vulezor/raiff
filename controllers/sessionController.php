<?php
class SessionController  extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //ajax::ajaxCheck();
    }

    public function check_logedIn($session_id)
    {
        Session::set_session_id($session_id);
        Session::init();
        $logged = Session::get('loggedIn');
        $status = Session::get('role');
        if ($logged == false && $status != 'administrator') {
            unset($logged);
            unset($status);
            Session::destroy();
            header('Content-Type: application/json');
            return json_encode(array('logedin'=>0));
        } else {
            return json_encode(array('success'=>1));
        }
    }

    public function check_session()
    {
        $data = json_decode(file_get_contents("php://input"));
       // $session_id = $session_id==null ? $data->session_id : $session_id;
        header('Content-Type: application/json');
       /* echo $this->check_logedIn($data->session_id);
        print_r($_SESSION);*/

        /*$data = json_decode(file_get_contents("php://input"));
        echo $this->check_logedIn($data->session_id);*/


    }
}
?>