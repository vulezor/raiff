<?php
class Setup_Api extends Controller{

    public function __construct(){
        parent::__construct();
        Session::init();
        $logged = Session::get('loggedIn');
        $status = Session::get('role');
        if($logged == false && $status != 'administrator'){
            unset($logged);
            unset($status);
            Session::destroy();
            echo json_encode(array('logout'=>0));
            die;
        }
    }


    public function get_params(){
        $params = array();
        $params['srps_parametri'] = $this->get_srps();
        $params['bonifikacija'] = $this->get_bonifikacija();
        $params['tabela_kukuruz'] = $this->get_tabela_kukuruz();
        $params['tabela_psenica'] = $this->get_tabela_psenica();
        $params['obracun_vlage'] = $this->get_nacin_obracuna_vlage();
        header('Content-Type: application/json');
        echo json_encode($params, JSON_NUMERIC_CHECK);
    }

    public function get_srps(){
        $sql = 'SELECT * FROM srps WHERE id="1"';
        $result = $this->model->get_values($sql,$id=null);
        return $result[0];
    }

    public function get_bonifikacija(){
        $sql = 'SELECT * FROM bonifikacija WHERE id="1"';
        $result = $this->model->get_values($sql, $id=null);
        return $result[0];
    }

    public function get_tabela_kukuruz(){
        $sql = 'SELECT * FROM vlaga_kukuruz WHERE id="1"';
        $result = $this->model->get_values($sql, $id=null);
        return $result[0];
    }

    public function get_tabela_psenica(){
        $sql = 'SELECT * FROM vlaga_psenica WHERE id="1"';
        $result = $this->model->get_values($sql, $id=null);
        return $result[0];
    }

    public function get_nacin_obracuna_vlage(){
        $sql = 'SELECT * FROM obracun_vlage WHERE id="1"';
        $result = $this->model->get_values($sql, $id=null);
        return $result[0];
    }

    public function update_srps(){
        $data = json_decode(file_get_contents("php://input"));
        $table = 'srps';
        $data = array(
            'soja_vlaga' => $data->soja_vlaga,
            'soja_primese' => $data->soja_primese,
            'suncokret_vlaga' => $data->suncokret_vlaga,
            'suncokret_primese' => $data->suncokret_primese,
            'uljana_vlaga' => $data->uljana_vlaga,
            'uljana_primese' => $data->uljana_primese,
            'kukuruz_vlaga' => $data->kukuruz_vlaga,
            'kukuruz_primese' => $data->kukuruz_primese,
            'kukuruz_lom' => $data->kukuruz_lom,
            'kukuruz_defekt' => $data->kukuruz_defekt,
            'psenica_vlaga' => $data->psenica_vlaga,
            'psenica_primese' => $data->psenica_primese,
            'psenica_hektolitar' => $data->psenica_hektolitar,
            'jecam_vlaga' => $data->jecam_vlaga,
            'jecam_primese' => $data->jecam_primese,
            'jecam_hektolitar' => $data->jecam_hektolitar
        );
        $where = 'id="1"';
        $this->model->update_values($table, $data, $where);
        header('Content-Type: application/json');
        echo json_encode(array('success'=>1));
    }

    public function update_bonifikacija(){
        $data = json_decode(file_get_contents("php://input"));
        $table = 'bonifikacija';
        $obj = array(
            'donja_vlps'=>$data->psenica_donja_vlaga,
            'gornja_vlps'=>$data->psenica_gornja_vlaga,
            'donja_prps'=>$data->psenica_donja_primesa,
            'gornja_prps'=>$data->psenica_gornja_primesa,
            'donja_pshl_bo'=>$data->psenica_donja_hektolitar,
            'gornja_pshl_bo'=>$data->psenica_gornja_hektolitar,
            'jecam_donja_vlaga'=>$data->jecam_donja_vlaga,
            'jecam_gornja_vlaga'=>$data->jecam_gornja_vlaga,
            'jecam_donja_primesa'=>$data->jecam_donja_primesa,
            'jecam_gornja_primesa'=>$data->jecam_gornja_primesa,
            'jecam_donja_hektolitar'=>$data->jecam_donja_hektolitar,
            'jecam_gornja_hektolitar'=>$data->jecam_gornja_hektolitar,
            'donja_uljvl'=>$data->uljana_donja_vlaga,
            'gornja_uljvl'=>$data->uljana_gornja_vlaga,
            'donja_uljpr'=>$data->uljana_donja_primesa,
            'gornja_uljpr'=>$data->uljana_gornja_primesa,
            'donja_sunvl'=>$data->suncokret_donja_vlaga,
            'gornja_sunvl'=>$data->suncokret_gornja_vlaga,
            'donja_sunpr'=>$data->suncokret_donja_primesa,
            'gornja_sunpr'=>$data->suncokret_gornja_primesa,
            'donja_sovl'=>$data->soja_donja_vlaga,
            'gornja_sovl'=>$data->soja_gornja_vlaga,
            'donja_sopr'=>$data->soja_donja_primesa,
            'gornja_sopr'=>$data->soja_gornja_primesa,

            'donja_kuvl'=>$data->kukuruz_donja_vlaga,
            'gornja_kuvl'=>$data->kukuruz_gornja_vlaga,
            'donja_kupr'=>$data->kukuruz_donja_primesa,
            'gornja_kupr'=>$data->kukuruz_gornja_primesa,
            'donja_kulo'=>$data->kukuruz_donja_lom,
            'gornja_kulo'=>$data->kukuruz_gornja_lom,
            'donja_kude'=>$data->kukuruz_donja_defekt,
            'gornja_kude'=>$data->kukuruz_gornja_defekt

        );
        $where = 'id="1"';
        $this->model->update_values($table, $obj, $where);
        header('Content-Type: application/json');
        echo json_encode(array('success'=>1));
    }

    public function update_kukuruz_tabela(){
        $data = json_decode(file_get_contents("php://input"));
        $data = $this->objectToArray($data);
        $table = 'vlaga_kukuruz';
        $where = 'id="1"';
        $this->model->update_values($table, $data, $where);
        header('Content-Type: application/json');
        echo json_encode(array('success'=>1));
    }

    public function update_psenica_tabela(){
        $data = json_decode(file_get_contents("php://input"));
        $data = $this->objectToArray($data);
        $table = 'vlaga_psenica';
        $where = 'id="1"';
        $this->model->update_values($table, $data, $where);
        header('Content-Type: application/json');
        echo json_encode(array('success'=>1));
    }



    public function update_nacin_obracuna_vlage(){
        $data = json_decode(file_get_contents("php://input"));
        $data = $this->objectToArray($data);
        $table = 'obracun_vlage';
        $where = 'id="1"';
        $this->model->update_values($table, $data, $where);
        header('Content-Type: application/json');
        echo json_encode(array('success'=>1));
    }

    private function objectToArray($d)
    {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __METHOD__ (Magic constant)
            * for recursive call
            */
            return array_map(__METHOD__, $d);
        } else {
            // Return array
            return $d;
        }
    }
}
?>