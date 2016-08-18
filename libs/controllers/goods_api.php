<?php
class Goods_Api extends Controller{

    public function __construct(){
        parent::__construct();
    }

    public function get_goods($id=null){
        if($id==null){
            $sql = 'SELECT goods.goods_id, goods.goods_cypher, goods.goods_name, sort_of_goods.goods_sort,
                    type_of_goods.goods_type, type_of_measurement_unit.measurement_unit, type_of_measurement_unit.measurement_name, @curRow := @curRow + 1 AS row_number
                    FROM goods
                    INNER JOIN sort_of_goods ON (sort_of_goods.sort_of_goods_id = goods.sort_of_goods_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = goods.type_of_goods_id)
                    INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                    JOIN (SELECT @curRow := 0) r
                    ORDER BY goods.goods_name DESC';
        } else {
            $sql = 'SELECT *
                    FROM goods
                    WHERE goods_id = :goods_id
                    ORDER BY goods_id  DESC';
            $id = array(':goods_id'=>$id);
        }
        $result = $this->model->get_values($sql, $id);
        header('Content-Type: application/json');
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //------------------------------------------------------------------------------------------------------------------------------------------

    public function get_measurement_unit($id=null){
        if($id==null){
            $sql = 'SELECT *
                    FROM type_of_measurement_unit
                    ORDER BY measurement_unit_id';
        }
        $result = $this->model->get_values($sql, $id);
        header('Content-Type: application/json');
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //------------------------------------------------------------------------------------------------------------------------------------------

    public function insert_measurement_unit(){
        $data = json_decode(file_get_contents("php://input"));
        $table = 'type_of_measurement_unit';
        $obj  = array(
            'measurement_unit'=> strip_tags($data->measurement_unit),
            'measurement_name'=> strip_tags($data->measurement_name)
        );

        header('Content-Type: application/json');
        $check_measurement_name = 'SELECT * FROM '.$table.' WHERE measurement_name= :measurement_name';
        $check_measurement_nameobj = array(':measurement_name'=>strip_tags(trim($data->measurement_name)));
        $exists = $this->model->check_exists($check_measurement_name, $check_measurement_nameobj);

        if($exists){
            echo json_encode(array('success'=>$exists, 'error_msg'=>'Naziv merne jedinice već postoji u bazi podataka!', 'field'=>'measurement_name'), JSON_NUMERIC_CHECK);
            return false;
        }

        $check_measurement_unit = 'SELECT * FROM '.$table.' WHERE measurement_unit= :measurement_unit';
        $check_measurement_unitobj = array(':measurement_unit'=>strip_tags(trim($data->measurement_unit)));
        $exists = $this->model->check_exists($check_measurement_unit, $check_measurement_unitobj);

        if($exists){
            echo json_encode(array('success'=>$exists, 'error_msg'=>'Oznaka merne jedinice već postoji u bazi podataka!', 'field'=>'measurement_unit'), JSON_NUMERIC_CHECK);
            return false;
        }
        $good_id = $this->model->set_values($table, $obj);
        echo json_encode(array('success'=>$exists,'result'=>$good_id), JSON_NUMERIC_CHECK);
    }


    //------------------------------------------------------------------------------------------------------------------------------------------

    public function get_goods_type($sort = null){
        if($sort == null){
            $sql = 'SELECT *
                    FROM type_of_goods
                    ORDER BY type_of_goods_id';
        } else if ($sort==1) {
            $sql = "SELECT * FROM type_of_goods WHERE goods_type='kukuruz' || goods_type='psenica' || goods_type='suncokret' || goods_type='soja' || goods_type='jecam' || goods_type='kukuruz tel-kel' || goods_type='uljana repica' || goods_type='sacma' ";
        } else if ($sort==2) {
            $sql = "SELECT * FROM type_of_goods WHERE goods_type != 'kukuruz' AND goods_type != 'psenica' AND goods_type != 'suncokret' AND goods_type != 'soja' AND goods_type != 'jecam' AND goods_type!='kukuruz tel-kel' AND goods_type!='uljana repica' AND goods_type!='sacma'";
        }
        $result = $this->model->get_values($sql, $id);
        header('Content-Type: application/json');
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //------------------------------------------------------------------------------------------------------------------------------------------

    public function get_goods_class($id=null){
        if($id==null){
            $sql = 'SELECT *
                    FROM sort_of_goods
                    ORDER BY sort_of_goods_id';
        }
        $result = $this->model->get_values($sql, $id);
        header('Content-Type: application/json');
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    //------------------------------------------------------------------------------------------------------------------------------------------

    public function insert_goods(){
        $data = json_decode(file_get_contents("php://input"));
       // print_r($data); return false;
        $table = 'goods';
        $obj  = array(
            'goods_cypher'=> strip_tags(trim($data->goods_cypher)),
            'sort_of_goods_id'=> strip_tags(trim($data->sort_of_goods_id)),
            'type_of_goods_id'=>strip_tags(trim($data->sort_of_type_id)),
            'goods_name'=>strip_tags(trim($data->goods_name)),
            'measurement_unit_id'=>strip_tags(trim($data->measurement_unit_id))
        );

        header('Content-Type: application/json');
        $check_goods_cypher = 'SELECT * FROM '.$table.' WHERE goods_cypher= :goods_cypher';
        $check_goods_cypherobj = array(':goods_cypher'=>strip_tags(trim($data->goods_cypher)));
        $exists = $this->model->check_exists($check_goods_cypher, $check_goods_cypherobj);

        if($exists){
            echo json_encode(array('success'=>$exists, 'error_msg'=>'Šifra koju ste ukucali za robu '.$data->goods_name.' je zauzeta!', 'field'=>'goods_cypher'), JSON_NUMERIC_CHECK);
            return false;
        }

        header('Content-Type: application/json');
        $check_goods_name = 'SELECT * FROM '.$table.' WHERE goods_name= :goods_name';
        $check_goods_nameobj = array(':goods_name'=>strip_tags(trim($data->goods_name)));
        $exists = $this->model->check_exists($check_goods_name, $check_goods_nameobj);

        if($exists){
            echo json_encode(array('success'=>$exists, 'error_msg'=>'Naziv robe '.$data->goods_name.' već postoji u bazi podataka!', 'field'=>'goods_name'), JSON_NUMERIC_CHECK);
            return false;
        }

        $good_id = $this->model->set_values($table, $obj);
        echo json_encode(array('success'=>$exists,'result'=>$good_id), JSON_NUMERIC_CHECK);
    }

    //------------------------------------------------------------------------------------------------------------------------------------------

    public function update_goods($id){
        $data = json_decode(file_get_contents("php://input"));
        $table = 'goods';
        $obj  = array(
            'goods_cypher'=> strip_tags(trim($data->goods_cypher)),
            'sort_of_goods_id'=> strip_tags(trim($data->sort_of_goods_id)),
            'type_of_goods_id'=>strip_tags(trim($data->sort_of_type_id)),
            'goods_name'=>strip_tags(trim($data->goods_name)),
            'measurement_unit_id'=>strip_tags(trim($data->measurement_unit_id))
        );

        header('Content-Type: application/json');

        $check_goods_cypher = 'SELECT * FROM '.$table.' WHERE goods_cypher= :goods_cypher AND goods_id != :goods_id';
        $check_goods_cypherobj = array(':goods_cypher'=>strip_tags(trim($data->goods_cypher)), ':goods_id'=>$id);
        $exists = $this->model->check_exists($check_goods_cypher, $check_goods_cypherobj);

        if($exists){
            echo json_encode(array('success'=>$exists, 'error_msg'=>'Šifra koju ste ukucali za robu '.$data->goods_name.' je zauzeta!', 'field'=>'goods_cypher'), JSON_NUMERIC_CHECK);
            return false;
        }

        header('Content-Type: application/json');
        $check_goods_name = 'SELECT * FROM '.$table.' WHERE goods_name= :goods_name AND goods_id != :goods_id';
        $check_goods_nameobj = array(':goods_name'=>strip_tags(trim($data->goods_name)), ':goods_id'=>$id);
        $exists = $this->model->check_exists($check_goods_name, $check_goods_nameobj);

        if($exists){
            echo json_encode(array('success'=>$exists, 'error_msg'=>'Naziv robe '.$data->goods_name.' već postoji u bazi podataka!', 'field'=>'goods_name'), JSON_NUMERIC_CHECK);
            return false;
        }

        $where = 'goods_id='.$id;
        $this->model->update_values($table, $obj, $where);
        echo json_encode(array('success'=>0), JSON_NUMERIC_CHECK);



    }
    public function fileUpload(){
        if((!empty($_FILES["file"])) && ($_FILES['file']['error'] == 0)) {

            $limitSize	= 45000000; //(15 kb) - Maximum size of uploaded file, change it to any size you want
            $fileName	= basename($_FILES['file']['name']);
            $fileSize	= $_FILES["file"]["size"];
            $fileExt	= substr($fileName, strrpos($fileName, '.') + 1);

            if (($fileExt == "xlsx") && ($fileSize < $limitSize)) {

                $getWorksheetName = array();
                $xlsx = new Simple_Xlsx( $_FILES['file']['tmp_name'] );
                $getWorksheetName = $xlsx->getWorksheetName();

                for($j=1;$j <= $xlsx->sheetsCount();$j++){
                    list($cols,) = $xlsx->dimension($j);
                    if($cols<3){
                        header('Content-Type: application/json');
                        echo json_encode(array("error"=>1, "msg"=>"Ispisana forma dokumenta nije validna ima više kolumna nego što aplikacija očekuje\nMolim vas a ubacite ispavan dokument sa najmanje 3 ili najviše 4 kolumne!"));
                        die;
                    }
                    //Prepare table
                    $rez_arr = array();
                    foreach( $xlsx->rows($j) as $k => $r) {
                        $row_arr = array();
                        for( $i = 0; $i < $cols; $i++) {

                            if($i==2){
                                $row_arr[] = $r[$i];

                            } else {
                                $row_arr[] = ( (isset($r[$i])) ? ucwords(strtolower($r[$i])) : '' );
                            }
                        }
                        $rez_arr[] = $row_arr;
                    }
                }
            }else{
                echo '<script>alert("Sory, this demo page only allowed .xlsx file under '.($limitSize/1000).' Kb!\nIf you want to try upload larger file, please download the source and try it on your own webserver.")</script>';
            }
            //print_r($rez_arr);
            $sqlm = 'SELECT * FROM type_of_measurement_unit ORDER BY measurement_unit_id';
            $measurement_unit = $this->model->get_values($sqlm, $id=null);

            $sqlk = 'SELECT * FROM type_of_goods ORDER BY type_of_goods_id';
            $gods_type_arr = $this->model->get_values($sqlk, $id=null);

            $ctrl_arr = array();
            foreach($rez_arr as $k=>$v){
                if($k !== 0){
                    $measurement_unit_id = $this->measure_unit($v[2], $measurement_unit);
                    $measurement_unit_id = $measurement_unit_id == '' ? 1 : $measurement_unit_id;

                    $type_of_goods_id = $this->type_of_goods($v[3], $gods_type_arr);
                    $type_of_goods_id = $type_of_goods_id == '' ? 10 : $type_of_goods_id;

                    $goods = array(
                        'goods_cypher'=>$v[0],
                        'sort_of_goods_id'=> 2,
                        'type_of_goods_id'=> $type_of_goods_id,
                        'goods_name'=> $v[1],
                        'measurement_unit_id'=>$measurement_unit_id,
                    );
                    $this->model->set_values('goods', $goods);

                    $ctrl_arr[] = $goods;
                }
            }

            echo'<pre>';
            print_r($ctrl_arr);
        }
    }

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    private function measure_unit($measure, $measurement_unit){
        foreach($measurement_unit as $key=>$value){
            // echo $value['measurement_unit'].'<br>';
            if($measure === $value['measurement_unit']){
                return $value['measurement_unit_id'];
                break;
            }
        }
    }

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    private function type_of_goods($goods_type_search, $gods_type_arr){
        foreach($gods_type_arr as $key=>$value){
            $gt = (string) $value['goods_type'];
            $st = (string) strtolower($goods_type_search);
            if($st === $gt){
                return $value['type_of_goods_id'];
                break;
            }
        }
    }

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function printExcel(){
        $sql = 'SELECT goods.goods_id, goods.goods_cypher, goods.goods_name, sort_of_goods.goods_sort,
                    type_of_goods.goods_type, type_of_measurement_unit.measurement_unit, type_of_measurement_unit.measurement_name, @curRow := @curRow + 1 AS row_number
                    FROM goods
                    INNER JOIN sort_of_goods ON (sort_of_goods.sort_of_goods_id = goods.sort_of_goods_id)
                    INNER JOIN type_of_goods ON (type_of_goods.type_of_goods_id = goods.type_of_goods_id)
                    INNER JOIN type_of_measurement_unit ON (type_of_measurement_unit.measurement_unit_id = goods.measurement_unit_id)
                    JOIN (SELECT @curRow := 0) r
                    ORDER BY goods.goods_name DESC';

        $data = $this->model->get_values($sql, $id);


        // echo '<pre>';
        //print_r($data); return false;
        $xml = new ExcelWriterXML('Spisak robe.xls');
        $xml->docAuthor('Raiffeisen Agro');

        $format = $xml->addStyle('StyleHeader');
        $format->fontBold();

        $sheet = $xml->addSheet('Spisak robe');
        $sheet->columnWidth(1,'70');
        $sheet->columnWidth(2,'200');
        $sheet->columnWidth(3,'150');
        $sheet->columnWidth(4,'150');
        $sheet->columnWidth(5,'50');
        $sheet->columnWidth(6,'100');




        $sheet->writeString(1, 1,'Sifra robe', 'StyleHeader');
        $sheet->writeString(1, 2, 'Naziv robe', 'StyleHeader');
        $sheet->writeString(1, 3, 'Vrsta robe', 'StyleHeader');
        $sheet->writeString(1, 4, 'Tip robe', 'StyleHeader');
        $sheet->writeString(1, 5, 'Oznaka', 'StyleHeader');
        $sheet->writeString(1, 6, 'Merna jedinica', 'StyleHeader');


        $row_br=1;
        foreach ($data as $value){
            $row_br  = $row_br +1;
            $sheet->writeNumber($row_br,1,$value['goods_cypher']);
            $sheet->writeString($row_br,2,$value['goods_name']);
            $sheet->writeString($row_br,3,$value['goods_sort']);
            $sheet->writeString($row_br,4,$value['goods_type']);
            $sheet->writeString($row_br,5,$value['measurement_unit']);
            $sheet->writeString($row_br,6,$value['measurement_name']);
        };










        $xml->sendHeaders();
        $xml->writeData();
    }

}
?>