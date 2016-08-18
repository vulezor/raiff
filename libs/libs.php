<?php
function dodajNulu($num){
   $num = $num<=9 ? '0'.$num : $num;
   return $num;
}

function zameniKategoriju($i){
  switch ($i) {
    case 'id_radm':
        return "radnom mestu";
        break;
    case 'id_sec':
        return "sektoru";
        break;
     case 'id_rj':
        return "radnoj jedinici";
        break;

}
}

function zameniCollKategoriju($a){
  switch ($a) {
    case 'id_radm':
        return "radno_mesto";
        break;
    case 'id_sec':
        return "sektori";
        break;
     case 'id_rj':
        return "poslovne_jedinice";
        break;

}
}


function switchIdent($i){
     switch ($i) {
    case 'ime_firme':
        return "firme,naziv_firme";
        break;
    case 'ime_rj':
        return "poslovne_jedinice,naziv_pj";
        break;
    case 'ime_sec':
        return "sektori,naziv_sektora";
        break;
    case 'ime_cent':
        return "centar,naziv_centra";
        break;
    case 'ime_rm':
        return "radno_mesto,radno_mesto";
        break;
    case 'ime_ps':
        return "gradovi,posta_mesto";
        break;
    case 'ime_od':
        return "odeljenja,odeljenje";
        break;
    case 'ime_sts':
        return "status_radnika,sts";
        break;
     }
}
function velikoSlovo($z){
 $povecan = ucwords(mb_convert_case($z, MB_CASE_TITLE, "UTF-8"));
 return $povecan;
}

function danUnedelji($dan){
    switch($dan){
      case "Sunday":
        return "Nedelja";
        break;
      case "Monday":
        return "Ponedeljak";
        break;
      case "Tuesday":
        return "Utorak";
        break;
      case "Wednesday":
        return "Sreda";
        break;
      case "Thursday":
        return "Četvrtak";
        break;
      case "Friday":
        return "Petak";
        break;
      case "Saturday":
        return "Subota";
        break;
    }
  }


function sekunde_u_sate($t,$f=':') // t = seconds, f = separator
{
  return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
}

function sate_u_sekunde($hour){
  $hour = explode(':', $hour);
  $sat = $hour[0] * 3600;
  $min = $hour[1] * 60;
  return $total_sec = $sat + $min + $hour[2];
}

class imeDana {
  public $_dan;

  public function __construct($dan){
    $this->_dan = $dan;
    echo $this->konvertuj();
  }

  public function konvertuj(){
    switch($this->_dan){
      case "Sunday":
        return "Nedelja";
        break;
      case "Monday":
        return "Ponedeljak";
        break;
      case "Tuesday":
        return "Utorak";
        break;
      case "Wednesday":
        return "Sreda";
        break;
      case "Thursday":
        return "Četvrtak";
        break;
      case "Friday":
        return "Petak";
        break;
      case "Saturday":
        return "Subota";
        break;
    }
  }
}

class kontrolaEviden{

  private $_datumi;
  private $_db;
  private $_kontrolori;
  private $_zaposleni;

  public function __construct($datum, $konekcija){
    $this->_db = $konekcija;
    $this->_datumi = $datum;
  }

 public function kontrolori(){
  $stmt = $this->_db->prepare('SELECT id_zaposlenog FROM korisnici WHERE status="Kontrolor"');
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $stmt->execute();
  $data = $stmt->fetchAll();
  $this->_kontrolori = $data;
 }

 public function proveraUpisa(){
   foreach($this->_kontrolori as $value){
     $stmt = $this->_db->prepare("SELECT id, kontrolor_izmena, kontrolor_id FROM zaposleni WHERE kontrolor_id = :kontrolor ORDER BY ime ASC");
     $stmt->setFetchMode(PDO::FETCH_ASSOC);
     $stmt->execute(array(':kontrolor'=>$value['id_zaposlenog']));
     $data = $stmt->fetchAll();
     $this->_zaposleni = $data;
       foreach($this->_zaposleni as $zaposlen){
        if($this->_datumi>=$zaposlen['kontrolor_izmena']){
          $proveri = $this->_db->prepare('SELECT * FROM evidencija WHERE datum = :datum AND kontrolor_id= :kontrolor AND id_zaposlenog= :zaposleni_id');
          $proveri->execute(array(':datum'=>$this->_datumi, ':kontrolor' => $zaposlen['kontrolor_id'], ':zaposleni_id' => $zaposlen['id']));
          $count = $proveri->rowCount();
          if($count<=0){
            $upisi = $this->_db->prepare('INSERT INTO evidencija SET datum=:datum, id_zaposlenog= :zaposleni_id, kontrolor_id= :kontrolor');
            $upisi->execute(array(':datum'=>$this->_datumi, ':kontrolor' => $zaposlen['kontrolor_id'], ':zaposleni_id' => $zaposlen['id']));

         }
       }
     }
   }
 }

 public function evidencionaLista(){
   $stmt = $this->_db->prepare('SELECT zaposleni.ime, zaposleni.prezime, zaposleni.praznik_slava_id, zaposleni.slika, evidencija.*
                                FROM evidencija
                                 INNER JOIN zaposleni ON( evidencija.id_zaposlenog = zaposleni.id )
                                   WHERE datum="'.$this->_datumi.'" ORDER BY zaposleni.ime ASC');
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();
    $data = $stmt->fetchAll();
    //echo json_encode($data);
     $result = array();
    foreach($data as $value){
      $st = $this->_db->prepare('SELECT ime, prezime FROM zaposleni WHERE id= :id');
      $st->execute(array(':id'=>$value['kontrolor_id']));
      $dat = $st->fetch(PDO::FETCH_OBJ);
      $value['kontrolor'] = $dat->ime.' '.$dat->prezime;

        $bol = $this->_db->prepare('SELECT * FROM bolovanja WHERE id_zaposlenog= :id_zaposlenog AND otvoreno <= :datum ORDER BY id');
        $bol->setFetchMode(PDO::FETCH_ASSOC);
        $bol->execute(array(':id_zaposlenog'=>$value['id_zaposlenog'], ':datum'=>$this->_datumi));
        $value['boluje'] = $bol->fetchAll();

         $list_praznik = $this->_db->prepare('SELECT id, praznik FROM praznici WHERE datum <= :datumi AND kr_datum >= :datumi AND p_kategorija="Verski"');
         $list_praznik->setFetchMode(PDO::FETCH_ASSOC);
         $list_praznik->execute(array(':datumi'=>$this->_datumi));
         $praznik = $list_praznik->fetch();

         $praznik_slavi = $this->_db->prepare('SELECT id_zaposlenog FROM evidencija_praznika WHERE id_zaposlenog= :id_zaposlenog AND id_praznika= :id_praznika AND status="on"');
         $praznik_slavi->setFetchMode(PDO::FETCH_ASSOC);
         $praznik_slavi->execute(array(':id_zaposlenog'=>$value['id_zaposlenog'], ':id_praznika'=>$praznik['id']));
         $p_slavi = $praznik_slavi->fetch();

         $value['slavljenik_id']=$p_slavi['id_zaposlenog'];
         $value['verski_praznik']=$praznik['praznik'];
         //print_r($value['slavljenik_id'].', '.$value['verski_praznik']);die;


         $slava = $this->_db->prepare('SELECT praznik FROM praznici WHERE datum >= :datum  AND kr_datum <= :datum AND p_kategorija="Verski" AND id= :id_slave');
         $slava->setFetchMode(PDO::FETCH_ASSOC);
         $slava->execute(array(':datum'=>$this->_datumi, ':id_slave'=>$value['praznik_slava_id']));
         $p_slava = $slava->fetch();
         $value['ime_slave'] = $p_slava['praznik'];
         $value['slavi'] = $slava->rowCount();

         $vanredni = $this->_db->prepare('SELECT id FROM vanredni_izlazak WHERE id_zaposlenog= :id_zaposlenog AND datum= :datum');
         $vanredni->setFetchMode(PDO::FETCH_ASSOC);
         $vanredni->execute(array(':id_zaposlenog'=>$value['id_zaposlenog'], ':datum'=>$this->_datumi));
        // $broj_vanrednih = $vanredni->fetchAll();
         $value['broj_vanrednih'] = $vanredni->rowCount();

      //echo json_encode($value);
      //array_push($bpodatak['boluje'], $value);
      //($bpodatak);
      $result[] = $value;
     // print_r($value);
     //  exit;
     $st=null; $dat=null; $bol=null; $list_praznik=null; $praznik_slavi=null; $slava=null; $stmt = null;
    };

     
      $q='SELECT * FROM praznici WHERE datum<= :dat AND kr_datum>= :kr_dat';
      
      $vdpraznik = $this->_db->select($q, array(':dat'=>$this->_datumi, ':kr_dat'=>$this->_datumi));
  

    echo json_encode(array('evInfo'=>$result,'vdpraznik'=>$vdpraznik));

 }
}


class daniUgodine {
  public function __construct($niz, $kolumna1, $kolumna2){
    $this->niz = $niz;
    $this->collDat1 = $kolumna1;
    $this->collDat2 = $kolumna2;
  }

  public function proracunDana(){
    $totalno_dana = '';
    for($i=0; $i<count($this->niz);$i++){
      $d_od = $this->niz[$i][$this->collDat1];
      $d_do = $this->niz[$i][$this->collDat2]=='0000-00-00' ? date("Y-m-d") : $this->niz[$i][$this->collDat2];
      $d_od = new DateTime($d_od);
      $d_do = new DateTime($d_do);
      $diff2 = $d_do->diff($d_od)->format("%a");
      $totalno_dana +=$diff2;
      }
   return $totalno_dana;
 }
}




?>