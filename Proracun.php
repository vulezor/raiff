<?php
class Proracun
{
    private $_kultura           = null;
    private $_nacin_obracuna_vlage = null;
    private $_bruto             = 0.00;
    private $_tara              = 0.00;
    private $_neto              = 0.00;
    private $_netox             = 0.00;
    private $_vlaga             = 0.00;
    private $_primese           = 0.00;
    private $_hektolitar        = 0.00;
    private $_lom               = 0.00;
    private $_defekt            = 0.00;
    private $_kalo_koeficient;
    private $_kalo;
    private $_dnv;
    private $_dnp;
    private $_dnh;
    private $_dnl;
    private $_dnd;
    private $_srps;
    private $_trs;
    private $_suvo_zrno;
    //xvrednosti
    private $_neto_x_vlaga;
    private $_neto_x_primese;
    private $_neto_x_hektolitar;
    private $_neto_x_lom;
    private $_neto_x_defekt;
    //srps area
    private $_srps_vlaga        = 0.00;
    private $_srps_primese      = 0.00;
    private $_srps_hektolitar   = 0.00;
    private $_srps_lom          = 0.00;
    private $_srps_defekt       = 0.00;
    //bonifikacija area from database
    private $_vlaga_donja       = 0;
    private $_vlaga_gornja      = 0;
    private $_primesa_donja     = 0;
    private $_primesa_gornja    = 0;
    private $_hektolitar_donja  = 0;
    private $_hektolitar_gornja = 0;
    private $_lom_donja         = 0;
    private $_lom_gornja        = 0;
    private $_defekt_donja      = 0;
    private $_defekt_gornja     = 0;
    //tablica susenja kukuruz
    private $_a14               = 0;
    private $_a14_5             = 0;
    private $_a15               = 0;
    private $_a15_5             = 0;
    private $_a16               = 0 ;
    private $_a16_5             = 0 ;
    private $_a17               = 0;
    private $_a17_5             = 0;
    private $_a18               = 0;
    private $_a18_5             = 0;
    private $_a19               = 0;
    private $_a19_5             = 0;
    private $_a20               = 0;
    private $_a20_5             = 0;
    private $_a21               = 0;
    private $_a21_5             = 0;
    private $_a22               = 0;
    private $_a22_5             = 0;
    private $_a23               = 0;
    private $_a23_5             = 0;
    private $_a24               = 0;
    private $_a24_5             = 0;
    private $_a25               = 0;
    private $_a25_5             = 0;
    private $_a26               = 0;
    private $_a26_5             = 0;
    private $_a27               = 0;
    private $_a27_5             = 0;
    private $_a28               = 0;
    private $_a28_5             = 0;
    private $_a29               = 0;
    private $_a29_5             = 0;
    //------------------------------------------------------------------------------------------------

    public function __construct(){

    }

    //------------------------------------------------------------------------------------------------

    public function set_property($arr, $value=null){
        if(is_array($arr)){
            foreach($arr as $key=>$value){
                $this->$key = $value;
            }
        } else {
            $this->$arr = $value;
        }
    }

    //------------------------------------------------------------------------------------------------

    public function get_property($key){
        return $this->$key;
    }

    //------------------------------------------------------------------------------------------------

    public function proracun_kukuruza(){
        $this->_calculateKaloKoeficient();          //postavlja kalo koeficient u zavisnosti od vlage
        $this->_calculateNeto();                    //proracunava NETO
        $this->_calculateNetox();                   //proracunava NETOX
        $this->_calculateX_osnovno();               //proracunava  X  vrednosti za vlagu i primese treba kod kalkulisanja pondera
        $this->_calculateX_kukuruz();               //proracunava  X  vrednosti za lom i defekt treba kod kalkulisanja pondera
        $this->_calculateDnv();                     //Dobitak ili odbitak na vlagu
        $this->_calculateDnp();                     //Dobitak ili odbitak na primese
        $this->_calculateDnl();                     //Dobitak ili odbitak na lom
        $this->_calculateDnd();                     //Dobitak ili odbitak na defekt
        $this->_srps =  $this->_netox + $this->_dnp + $this->_dnl + $this->_dnd + $this->_dnv;   //proracun Srpsa
        $this->_kukuruzTrosakSusenja();             //Proracun troskova susenja
        $this->_suvoZrnoNaRaspolaganju();           //Proracun koliko ostaje suvog zrna da se plati;
    }

    //------------------------------------------------------------------------------------------------

    public function proracun_uljarica(){
        $this->_calculateKaloKoeficient();          //postavlja kalo koeficient u zavisnosti od vlage
        $this->_calculateNeto();                    //proracunava NETO
        $this->_calculateX_osnovno();               //proracunava  X  vrednosti za vlagu i primese treba kod kalkulisanja pondera
        $this->_calculateDnv();                     //Dobitak ili odbitak na vlagu
        $this->_calculateDnp();                     //Dobitak ili odbitak na primese
        $this->_srps =  $this->_neto + $this->_dnv + $this->_dnp;   //proracun Srpsa
    }

    //------------------------------------------------------------------------------------------------

    private function _calculateKaloKoeficient(){
        $this->_kalo_koeficient = 0.005;
        if($this->_vlaga > 14){
            $this->_kalo_koeficient = 0.01;
        }
    }

    //------------------------------------------------------------------------------------------------

    private function _calculateNeto(){
        $this->_neto = $this->_bruto - $this->_tara;
    }

    //------------------------------------------------------------------------------------------------

    private function _calculateNetox(){
        $kalo = $this->_neto * $this->_kalo_koeficient;
        $this->_kalo = $kalo >= 0 ? floatval('-'.$kalo) : abs($kalo);
        $this->_netox = $this->_neto + $this->_kalo;
    }

    //------------------------------------------------------------------------------------------------

    private function _calculateX_osnovno(){
        $this->_neto_x_vlaga = $this->_neto * $this->_vlaga;
        $this->_neto_x_primese = $this->_neto * $this->_primese;
    }

    //------------------------------------------------------------------------------------------------

    private function _calculateX_zitarice(){
        $this->_neto_x_hektolitar = $this->_neto * $this->_hektolitar;
    }

    //------------------------------------------------------------------------------------------------

    private function _calculateX_kukuruz(){
        $this->_neto_x_lom = $this->_neto * $this->_lom;
        $this->_neto_x_defekt = $this->_neto * $this->_defekt;
    }

    //------------------------------------------------------------------------------------------------

    private function _calculateDnv(){
        if( $this->_vlaga < $this->_vlaga_donja ){
            if($this->_nacin_obracuna_vlage === 'formula'){
                $this->_dnv = ( $this->_neto * ((100 - $this->_vlaga_donja) / (100 - $this->_srps_vlaga)) ) - $this->_neto ;
            } else {
                $dnv = $this->_neto * (($this->_srps_vlaga - $this->_vlaga_donja) / 100);
            }
        }
        elseif( $this->_vlaga > $this->_vlaga_gornja ){
            if($this->_nacin_obracuna_vlage === 'formula'){
                $this->_dnv = ( $this->_neto * ((100 - $this->_vlaga_gornja) / (100 - $this->_srps_vlaga))) - $this->_neto;
            } else {
                $this->_dnv = $this->_neto * (($this->_srps_vlaga - $this->_vlaga_gornja) / 100);
            }
        } else {
            if($this->_nacin_obracuna_vlage === 'formula'){
                $this->_dnv = ( $this->_neto * ((100 - $this->_vlaga) / (100 - $this->_srps_vlaga)) ) - $this->_neto;
            } else {
                $this->_dnv  = $this->_neto * (($this->_srps_vlaga - $this->_vlaga) / 100);
            }
        }
    }

    //------------------------------------------------------------------------------------------------

    private function _calculateDnp(){
        $neto = $this->_kultura == 'kukuruz' ? $this->_netox : $this->_neto;
        if( $this->_primese < $this->_primesa_donja ){
            $this->_dnp = $neto * (($this->_srps_primese - $this->_primesa_donja) / 100);
        }
        elseif( $this->_primese >$this->_primesa_gornja){
            $this->_dnp = $neto * (( $this->_srps_primese - $this->_primesa_gornja ) / 100);
        } else {
            $this->_dnp = $neto * (( $this->_srps_primese - $this->_primese ) / 100);
        }
    }

    //------------------------------------------------------------------------------------------------

    private function _calculateDnl(){
        if( $this->_lom < $this->_lom_donja ){
            $this->_dnl = $this->_netox * (($this->_srps_lom - $this->_lom_donja) / 200);
        }
        elseif( $this->_primese >$this->_lom_gornja){
            $this->_dnl = $this->_netox * (( $this->_srps_lom - $this->_lom_gornja ) / 200);
        } else {
            $this->_dnl = $this->_netox * (( $this->_srps_lom - $this->_lom ) / 200);
        }
    }

    //------------------------------------------------------------------------------------------------

    private function _calculateDnd(){
        if( $this->_defekt < $this->_defekt_donja ){
            $this->_dnd = $this->_netox * (($this->_srps_defekt - $this->_defekt_donja) / 100);
        }
        elseif( $this->_defekt >$this->_defekt_gornja){
            $this->_dnd = $this->_netox * (( $this->_srps_defekt - $this->_defekt_gornja ) / 100);
        } else {
            $this->_dnd = $this->_netox * (( $this->_srps_defekt - $this->_defekt ) / 100);
        }
    }

    //------------------------------------------------------------------------------------------------

    private function _kukuruzTrosakSusenja(){
        if($this->_vlaga > 14 && $this->_vlaga <= 14.50 ){$trs = ($this->_netox * $this->_a14)/100;}
        if($this->_vlaga > 14.50 && $this->_vlaga <= 15){$trs = ($this->_netox * $this->_a14_5)/100;}
        if($this->_vlaga > 15 && $this->_vlaga <= 15.50 ){$trs = ($this->_netox * $this->_a15)/100;}
        if($this->_vlaga > 15.50 && $this->_vlaga <= 16){$trs = ($this->_netox * $this->_a15_5)/100;}
        if($this->_vlaga > 16 && $this->_vlaga <= 16.50 ){$trs = ($this->_netox * $this->_a16)/100;}
        if($this->_vlaga > 16.50 && $this->_vlaga <= 17){$trs = ($this->_netox * $this->_a16_5)/100;}
        if($this->_vlaga > 17 && $this->_vlaga <= 17.50 ){$trs = ($this->_a17 / 100) * $this->_netox;}
        if($this->_vlaga > 17.50 && $this->_vlaga <= 18){$trs =  ($this->_a17_5 / 100) * $this->_netox;}
        if($this->_vlaga > 18 && $this->_vlaga <= 18.50 ){$trs = ($this->_netox * $this->_a18)/100;}
        if($this->_vlaga > 18.50 && $this->_vlaga <= 19){$trs = ($this->_netox * $this->_a18_5)/100;}
        if($this->_vlaga > 19 && $this->_vlaga <= 19.50 ){$trs = ($this->_netox * $this->_a19)/100;}
        if($this->_vlaga > 19.50 && $this->_vlaga <= 20){$trs = ($this->_netox * $this->_a19_5)/100;}
        if($this->_vlaga > 20 && $this->_vlaga <= 20.50 ){$trs = ($this->_netox * $this->_a20)/100;}
        if($this->_vlaga > 20.50 && $this->_vlaga <= 21){$trs = ($this->_netox * $this->_a20_5)/100;}
        if($this->_vlaga > 21 && $this->_vlaga <= 21.50 ){$trs = ($this->_netox * $this->_a21)/100;}
        if($this->_vlaga > 21.50 && $this->_vlaga <= 22){$trs = ($this->_netox * $this->_a21_5)/100;}
        if($this->_vlaga > 22 && $this->_vlaga <= 22.50 ){$trs = ($this->_netox * $this->_a22)/100;}
        if($this->_vlaga > 22.50 && $this->_vlaga <= 23){$trs = ($this->_netox * $this->_a22_5)/100;}
        if($this->_vlaga > 23 && $this->_vlaga <= 23.50 ){$trs = ($this->_netox * $this->_a23)/100;}
        if($this->_vlaga > 23.50 && $this->_vlaga <= 24){$trs = ($this->_netox * $this->_a23_5)/100;}
        if($this->_vlaga > 24 && $this->_vlaga <= 24.50 ){$trs = ($this->_netox * $this->_a24)/100;}
        if($this->_vlaga > 24.50 && $this->_vlaga <= 25){$trs = ($this->_netox * $this->_a24_5)/100;}
        if($this->_vlaga > 25 && $this->_vlaga <= 25.50 ){$trs = ($this->_netox * $this->_a25)/100;}
        if($this->_vlaga > 25.50 && $this->_vlaga <= 26){$trs = ($this->_netox * $this->_a25_5)/100;}
        if($this->_vlaga > 26 && $this->_vlaga <= 26.50 ){$trs = ($this->_netox * $this->_a26)/100;}
        if($this->_vlaga > 26.50 && $this->_vlaga <= 27){$trs = ($this->_netox * $this->_a26_5)/100;}
        if($this->_vlaga > 27 && $this->_vlaga <= 27.50 ){$trs = ($this->_netox * $this->_a27)/100;}
        if($this->_vlaga > 27.50 && $this->_vlaga <= 28){$trs = ($this->_netox * $this->_a27_5)/100;}
        if($this->_vlaga > 28 && $this->_vlaga <= 28.50 ){$trs = ($this->_netox * $this->_a28)/100;}
        if($this->_vlaga > 28.50 && $this->_vlaga <= 29){$trs = ($this->_netox * $this->_a28_5)/100;}
        if($this->_vlaga > 29 && $this->_vlaga <= 29.50 ){$trs = ($this->_netox * $this->_a29)/100;}
        if($this->_vlaga > 29.50 && $this->_vlaga <= 30 ){$trs = ($this->_netox * $this->_a29_5)/100;}
        if(!isset($trs)){
            $this->_trs = 0;
        }
        $this->_trs = round($trs, 2);
    }

    private function _suvoZrnoNaRaspolaganju(){
        $suvo = $this->_srps - $this->_trs;
        $this->_suvo_zrno = round($suvo,2);
    }
}


?>