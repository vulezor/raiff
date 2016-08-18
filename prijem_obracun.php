<?php
session_start();
if(isset($_SESSION['magacin']) && isset($_SESSION['ime'])){
 include('../konekcija.php');
 $ident = $_GET['ident'];
 $tara  = $_GET['tara'];

 //Uzimanje informacija sa prijem_ulaz tabele
 $zahtevamo = mysql_query('SELECT * FROM prijem_ulaz WHERE id="'.$ident.'"');
 while($saulaza = mysql_fetch_assoc($zahtevamo)){
 $rednibr = $saulaza['id'];
 $koop_id = $saulaza['koop_id'];
 $predaje = $saulaza['predaje_za'];
 $kultura = $saulaza['kultura'];
 $vozac   = $saulaza['vozac'];
 $reg     = $saulaza['reg'];
 $bruto   = $saulaza['bruto'];
 $vlaga   = $saulaza['vlaga'];
 $primese = $saulaza['primese'];
 $status  = $saulaza['status'];
 $cena    = $saulaza['cena'];
 $rok     = $saulaza['rok_placanja'];

 if($kultura == 'PŠENICA' or $kultura == 'ЈЕČAM'){
   $hektolitar = $saulaza['hektolitar'];
   /*print_r($hektolitar);
   return false;*/
 }
 if($kultura == 'KUKURUZ SIROVI' or $kultura == 'KUKURUZ SUVI'){
 $lom    = $saulaza['lom'];
 $defekt = $saulaza['defekt'];
 }
}

//Brisanje sa reda sa $ident prijem_ulaza
mysql_query('DELETE FROM prijem_ulaz WHERE id="'.$ident.'"');

//Uzimanje srps standarda
 $qwu = mysql_query('SELECT * FROM srps_standard WHERE id="1"');
 while($redic = mysql_fetch_assoc($qwu)){
    $so_vl   = $redic['soja_vlaga'];
    $so_pr   = $redic['soja_primese'];
    $sun_vla = $redic['suncokret_vlaga'];
    $sun_pri = $redic['suncokret_primese'];
    $ul_vl   = $redic['uljana_vlaga'];
    $ul_pr   = $redic['uljana_primese'];
    $ku_vl   = $redic['kukuruz_vlaga'];
    $ku_pr   = $redic['kukuruz_primese'];
    $ku_lo   = $redic['kukuruz_lom'];
    $ku_de   = $redic['kukuruz_defekt'];
    $ps_vl   = $redic['psenica_vlaga'];
    $ps_pr   = $redic['psenica_primese'];
    $ps_hl   = $redic['psenica_hektolitar'];
 }

 //Uzimanje Bonifikacije
 $bonifikacija = mysql_query('SELECT * FROM bonifikacija WHERE id="1"');
 while($re = mysql_fetch_assoc($bonifikacija)){
    $donja_sovl  = $re['donja_sovl'];
    $gornja_sovl = $re['gornja_sovl'];
    $donja_sopr  = $re['donja_sopr'];
    $gornja_sopr = $re['gornja_sopr'];

    $donja_sunvl  = $re['donja_sunvl'];
    $gornja_sunvl = $re['gornja_sunvl'];
    $donja_sunpr  = $re['donja_sunpr'];
    $gornja_sunpr = $re['gornja_sunpr'];

    $donja_uljvl  = $re['donja_uljvl'];
    $gornja_uljvl = $re['gornja_uljvl'];
    $donja_uljpr  = $re['donja_uljpr'];
    $gornja_uljpr = $re['gornja_uljpr'];

    $gornja_pshl_bo = $re['gornja_pshl_bo'];
    $donja_pshl_bo  = $re['donja_pshl_bo'];
    $donja_vlps     = $re['donja_vlps'];
    $gornja_vlps    = $re['gornja_vlps'];
    $donja_prps     = $re['donja_prps'];
    $gornja_prps    = $re['gornja_prps'];

    $donja_kuvl     = $re['donja_kuvl'];
    $gornja_kuvl    = $re['gornja_kuvl'];
    $donja_kupr     = $re['donja_kupr'];
    $gornja_kupr    = $re['gornja_kupr'];
    $donja_kulo     = $re['donja_kulo'];
    $gornja_kulo   = $re['gornja_kulo'];
    $donja_kude     = $re['donja_kude'];
    $gornja_kude   = $re['gornja_kude'];
 }

//Uzimanje vlage za kukuruz
//Uzimanje vlage za kukuruz
$uzimanje = mysql_query('SELECT * FROM vlaga_kuk WHERE id="1"');
while($uzmi = mysql_fetch_assoc($uzimanje)){
$a14 = $uzmi['acetrna'];
$a14_5 = $uzmi['acetrna_pet'];
$a15 = $uzmi['apetna'];
$a15_5 = $uzmi['apetna_pet'];
$a16 = $uzmi['asesna'];
$a16_5 = $uzmi['asesna_pet'];
$a17 = $uzmi['asedam'];
$a17_5 = $uzmi['asedam_pet'];
$a18 = $uzmi['aosam'];
$a18_5 = $uzmi['aosam_pet'];
$a19 = $uzmi['adevetnaest'];
$a19_5 = $uzmi['adevetnaest_pet'];
$a20 = $uzmi['advad'];
$a20_5 = $uzmi['advad_pet'];
$a21 = $uzmi['advajed'];
$a21_5 = $uzmi['advajed_pet'];
$a22 = $uzmi['advadva'];
$a22_5 = $uzmi['advadva_pet'];
$a23 = $uzmi['advatri'];
$a23_5 = $uzmi['advatri_pet'];
$a24 = $uzmi['advacetiri'];
$a24_5 = $uzmi['advacetiri_pet'];
$a25 = $uzmi['advapet'];
$a25_5 = $uzmi['advapet_pet'];
$a26 = $uzmi['advasest'];
$a26_5 = $uzmi['advasest_pet'];
$a27 = $uzmi['advasedam'];
$a27_5 = $uzmi['advasedam_pet'];
$a28 = $uzmi['advaosam'];
$a28_5 = $uzmi['advaosam_pet'];
$a29 = $uzmi['advadevet'];
$a29_5 = $uzmi['advadevet_pet'];
$a30 = $uzmi['atrideset'];
}

//Uzimanje vlage za psenicu
$uzimanje = mysql_query('SELECT * FROM vlaga_pse WHERE id="1"');
while($uzmi = mysql_fetch_assoc($uzimanje)){
$pa14 = $uzmi['pa14'];
$pa14_50 = $uzmi['pa14_50'];
$pa15 = $uzmi['pa15'];
$pa15_50 = $uzmi['pa15_50'];
$pa16 = $uzmi['pa16'];
$pa16_50 = $uzmi['pa16_50'];
$pa17 = $uzmi['pa17'];
$pa17_50 = $uzmi['pa17_50'];
$pa18 = $uzmi['pa18'];
$pa18_50 = $uzmi['pa18_50'];
$pa19 = $uzmi['pa19'];
$pa19_50 = $uzmi['pa19_50'];
}

//Proračunavanje neta sve kulture
$neto = $bruto - $tara;

//zajednicko za psenicu, uljanu repicu, suncokret i soju
if($kultura == 'PŠENICA' or $kultura == 'ULJANA REPICA' or $kultura == 'SUNCOKRET' or $kultura == 'SOJA'){
   $neto_x_vlaga = $neto * $vlaga;
   $neto_x_primese = $neto * $primese;
}

 if($kultura == 'PŠENICA') {
$neto_x_htl = $neto * $hektolitar;
}

if($kultura == 'JEČAM') {
$neto_x_htl = $neto * $hektolitar;
}

//PRORACUN DODATKA NA VLAGU KOD KUKURUZA
 if($kultura == 'KUKURUZ SIROVI') {
 //proračun za sirovi rastur kod kukuruza
  $kalo = 0.005;
 if($vlaga>14){
   $kalo = 0.01;
 }
 $kalo = $neto * $kalo;
 $netox = $neto - $kalo;

 $neto_x_vlaga = $netox * $vlaga;
 $neto_x_primese = $netox * $primese;
 $neto_x_lom = $netox * $lom;
 $neto_x_defekt = $netox * $defekt;


if($vlaga<=$donja_kuvl){
$minus_kolicina = (100 - $donja_kuvl) / (100 - $ku_vl);
$minus_kolicina = number_format($minus_kolicina, 4, '.', ',');
$dnv = $neto - ($neto * $minus_kolicina);
}
elseif($vlaga>$donja_kuvl && $vlaga<=$ku_vl){
$minus_kolicina = (100 - $vlaga) / (100 - $ku_vl);
$minus_kolicina = number_format($minus_kolicina, 4, '.', ',');
$dnv = $neto - ($neto * $minus_kolicina);
}
elseif($vlaga>$ku_vl){
$minus_kolicina = (100 - $vlaga) / (100 - $ku_vl);
$minus_kolicina = number_format($minus_kolicina, 4, '.', ',');
$dnv = $neto - ($neto * $minus_kolicina);
}
elseif($vlaga>$gornja_kuvl){
$minus_kolicina = (100 - $gornja_kuvl) / (100 - $ku_vl);
$minus_kolicina = number_format($minus_kolicina, 4, '.', ',');
$dnv = $neto - ($neto * $minus_kolicina);
}

//PRORAČUN ODBITKA NA PRIMESAMA  KOD KUKURUZA
if($primese <= $donja_kupr){
$dnp = (($ku_pr-$donja_kupr)/'100')*$netox;
}
elseif($primese > $gornja_kupr){
$dnp = -((($primese-$ku_pr)/('100'-$ku_pr)*'100'+'0.5')/'100')*$netox;
}
else{
 $dnp = (($ku_pr-$primese)/'100')*$netox;
}

//PRORAČUN ODBITKA NA LOM KOD KUKURUZA
if($lom <= $donja_kulo){
$dnl = (($ku_lo-$donja_kulo)/'200')*$netox;
}
elseif($lom > $gornja_kulo){
$dnl = -((($lom-$ku_lo)/('200'-$ku_lo)*'100'+'0.5')/'100')*$netox;
}
else{
 $dnl = (($ku_lo-$lom)/'200')*$netox;
}

//PRORAČUN ODBITKA NA DEFEKTU KOD KUKURUZA
if($defekt <= $donja_kude){
$dnd = (($ku_de-$donja_kude)/'100')*$netox;
}
elseif($defekt > $gornja_kude){
$dnd = -((($defekt-$ku_de)/('100'-$ku_de)*'100'+'0.5')/'100')*$netox;
}
else{
 $dnd = (($ku_de-$defekt)/'100')*$netox;
}


//PRORAČUN SRPSA
//PRORAČUN SRPSA
if($vlaga>14){
    $srps =  ($netox + $dnp + $dnl + $dnd) - $dnv;
} else {
    $srps =  ($netox + $dnp + $dnl + $dnd) - $dnv;
}
//KRAJ PRORAČUNA ZA SIROVI RASTUR KOD KUKURUZA


//KRAJ PRORAČUNA SRPSA
//PRORAČUN TROŠKOVA SUŠENJA KOD KUKURUZA
  //PRORAČUN TROŠKOVA SUŠENJA KOD KUKURUZA
  if($vlaga > 14 && $vlaga <= 14.50 ){$trs = ($netox * $a14)/100;}
  if($vlaga > 14.50 && $vlaga <= 15){$trs = ($netox * $a14_5)/100;}
  if($vlaga > 15 && $vlaga <= 15.50 ){$trs = ($netox * $a15)/100;}
  if($vlaga > 15.50 && $vlaga <= 16){$trs = ($netox * $a15_5)/100;}
  if($vlaga > 16 && $vlaga <= 16.50 ){$trs = ($netox * $a16)/100;}
  if($vlaga > 16.50 && $vlaga <= 17){$trs = ($netox * $a16_5)/100;}
  if($vlaga > 17 && $vlaga <= 17.50 ){$trs = ($a17 / 100) * $netox;}
  if($vlaga > 17.50 && $vlaga <= 18){$trs =  ($a17_5 / 100) * $netox;}
  if($vlaga > 18 && $vlaga <= 18.50 ){$trs = ($netox * $a18)/100;}
  if($vlaga > 18.50 && $vlaga <= 19){$trs = ($netox * $a18_5)/100;}
  if($vlaga > 19 && $vlaga <= 19.50 ){$trs = ($netox * $a19)/100;}
  if($vlaga > 19.50 && $vlaga <= 20){$trs = ($netox * $a19_5)/100;}
  if($vlaga > 20 && $vlaga <= 20.50 ){$trs = ($netox * $a20)/100;}
  if($vlaga > 20.50 && $vlaga <= 21){$trs = ($netox * $a20_5)/100;}
  if($vlaga > 21 && $vlaga <= 21.50 ){$trs = ($netox * $a21)/100;}
  if($vlaga > 21.50 && $vlaga <= 22){$trs = ($netox * $a21_5)/100;}
  if($vlaga > 22 && $vlaga <= 22.50 ){$trs = ($netox * $a22)/100;}
  if($vlaga > 22.50 && $vlaga <= 23){$trs = ($netox * $a22_5)/100;}
  if($vlaga > 23 && $vlaga <= 23.50 ){$trs = ($netox * $a23)/100;}
  if($vlaga > 23.50 && $vlaga <= 24){$trs = ($netox * $a23_5)/100;}
  if($vlaga > 24 && $vlaga <= 24.50 ){$trs = ($netox * $a24)/100;}
  if($vlaga > 24.50 && $vlaga <= 25){$trs = ($netox * $a24_5)/100;}
  if($vlaga > 25 && $vlaga <= 25.50 ){$trs = ($netox * $a25)/100;}
  if($vlaga > 25.50 && $vlaga <= 26){$trs = ($netox * $a25_5)/100;}
  if($vlaga > 26 && $vlaga <= 26.50 ){$trs = ($netox * $a26)/100;}
  if($vlaga > 26.50 && $vlaga <= 27){$trs = ($netox * $a26_5)/100;}
  if($vlaga > 27 && $vlaga <= 27.50 ){$trs = ($netox * $a27)/100;}
  if($vlaga > 27.50 && $vlaga <= 28){$trs = ($netox * $a27_5)/100;}
  if($vlaga > 28 && $vlaga <= 28.50 ){$trs = ($netox * $a28)/100;}
  if($vlaga > 28.50 && $vlaga <= 29){$trs = ($netox * $a28_5)/100;}
  if($vlaga > 29 && $vlaga <= 29.50 ){$trs = ($netox * $a29)/100;}
  if($vlaga > 29.50 && $vlaga <= 30 ){$trs = ($netox * $a29_5)/100;}
  //$trs = round(floatval($trs),2);
 //PRORAČUN ZA SUVO ZRNO KOD KUKURUZA
  if(!isset($trs)){
    $trs = 0;
  }
 $trs = round($trs, 2);
 $suvo = $srps - $trs;
 $suvo = round($suvo,2);
 }
//KRAJ PRORAČUNA KUKURUZA(GLOBALNO);


if($kultura == 'KUKURUZ SUVI') {
 $neto_x_vlaga = $neto * $vlaga;
 $neto_x_primese = $neto * $primese;
 $neto_x_lom = $neto * $lom;
 $neto_x_defekt = $neto * $defekt;

$minus_kolicina = ('100' - $vlaga) / ('100' - $ku_vl);
$dnv = '0.00';
//KRAJ PRORACUNA DODATKA NA VLAGU KOD KUKURUZA-SUVOG

//PRORAČUN ODBITKA NA PRIMESAMA  KOD KUKURUZA-SUVOG
  if($primese <= '1'){
$dnp = '0.00' ;
}
elseif($primese > '10'){
$dnp = '0.00';
}
else{
 $dnp = '0.00';
}
//KRAJ PRORAČUN ODBITKA NA PRIMESAMA  KOD KUKURUZA-SUVOG

//PRORAČUN ODBITKA NA LOM KOD KUKURUZA-SUVOG
  if($lom <= '4'){
$dnl = '0.00';
}
elseif($lom > '8'){
$dnl = '0.00';
}
else{
 $dnl = '0.00';
}
//KRAJ PRORAČUN ODBITKA NA LOM KOD KUKURUZA-SUVOG

//PRORAČUN ODBITKA NA DEFEKTU KOD KUKURUZA-SUVOG
  if($defekt <= '2'){
$dnd = '0.00';
}
elseif($defekt > '4'){
$dnd = '0.00';
}
else{
 $dnd = '0.00';
}
//KRAJ PRORAČUN ODBITKA NA DEFEKTU KOD KUKURUZA-SUVOG
 $srps =  $neto;
 }
 //KRAJ PRORAČUNA KUKURUZA-SUVOG(GLOBALNO);


//PRORAČUN SUNCOKRETA GLOBALNO
 if($kultura == 'SUNCOKRET') {
 if($vlaga<$donja_sunvl){
 $dnv = $neto * (($sun_vla - $donja_sunvl) / '100');
}
 elseif($vlaga>=$donja_sunvl && $vlaga<=$sun_vla){
 $dnv = $neto * (($sun_vla - $vlaga) / '100');
 }
 elseif($vlaga>$gornja_sunvl){
   $dnv = $neto * (($sun_vla - $gornja_sunvl) / '100');
 }
 elseif($vlaga>$sun_vla){
 $dnv = $neto * (($sun_vla - $vlaga) / '100');
 }

  if($primese<$donja_sunpr){
    $dnp = $neto * (($sun_pri - $donja_sunpr) / '100');
   }
   elseif($primese>=$donja_sunpr && $primese<=$sun_pri){
    $dnp = $neto * (($sun_pri - $primese) / '100');
   }
   elseif($primese>$gornja_sunpr){
    $dnp = $neto * (($sun_pri - $gornja_sunpr) / '100');
   }
  elseif($primese>$sun_pri){
    $dnp = $neto * (($sun_pri - $primese) / '100');
   }
 $srps = $neto + $dnv + $dnp;

 }
//KRAJ PRORAČUNA SUNCOKRETA


// PRORAČUN SOJE
if($kultura == 'SOJA') {
/* if($vlaga<$donja_sovl){
 $minus_kolicina = ('100' - $donja_sovl) / ('100' - $so_vl);
 $dnv = $neto - ($neto * $minus_kolicina);
 $dnv = str_replace('-', '', $dnv);
}
 elseif($vlaga>=$donja_sovl && $vlaga<=$so_vl){
 $minus_kolicina = ('100' - $vlaga) / ('100' - $so_vl);
 $dnv = $neto - ($neto * $minus_kolicina);
 $dnv = str_replace('-', '', $dnv);
 }
 elseif($vlaga>$gornja_sovl){
 $minus_kolicina = ('100' - $gornja_sovl) / ('100' - $so_vl);
 $dnv = $neto - ($neto * $minus_kolicina);
 }
 elseif($vlaga>$so_vl){
 $minus_kolicina = ('100' - $vlaga) / ('100' - $so_vl);
 $dnv = $neto - ($neto * $minus_kolicina);
 } */


if($vlaga<$donja_sovl){
 $dnv = $neto * (($so_vl - $donja_sovl) / '100');
}
 elseif($vlaga>=$donja_sovl && $vlaga<=$so_vl){
 $dnv = $neto * (($so_vl - $vlaga) / '100');
 }
 elseif($vlaga>$gornja_sovl){
   $dnv = $neto * (($so_vl - $gornja_sovl) / '100');
 }
 elseif($vlaga>$so_vl){
 $dnv = $neto * (($so_vl - $vlaga) / '100');
 }

 if($primese<$donja_sopr){
   $dnp = $neto * (($so_pr - $donja_sopr) / '100');
 }
 elseif($primese>=$donja_sopr && $primese<=$so_pr){
 $dnp = $neto * (($so_pr - $primese) / '100');
 }
 elseif($primese>$gornja_sopr){
 $dnp = $neto * (($so_pr - $gornja_sopr) / '100');
 }
 elseif($primese>$so_pr){
 $dnp = $neto * (($so_pr - $primese) / '100');
 }
 $srps = $neto + $dnv + $dnp;
 }
//KRAJ PRORAČUN SOJE


//PRORAČUN ULJANE REPICE
 if($kultura == 'ULJANA REPICA') {
//PROCENAT
if($vlaga<$donja_uljvl){
 $dnv = $neto * (($ul_vl - $donja_uljvl) / '200');
}
 elseif($vlaga>=$donja_uljvl && $vlaga<=$ul_vl){
 $dnv = $neto * (($ul_vl - $vlaga) / '200');
 }
 elseif($vlaga>$gornja_uljvl){
   $dnv = $neto * (($ul_vl - $gornja_uljvl) / '100');
 }
 elseif($vlaga>$ul_vl){
 $dnv = $neto * (($ul_vl - $vlaga) / '100');
 }

 //formula
 /*
 if($vlaga<$donja_uljvl){
  $minus_kolicina = ('100' - $donja_uljvl) / ('100' - $ul_vl);
  $dnv = $neto - ($neto * $minus_kolicina);
  $dnv = str_replace('-', '', $dnv) / 2;
}
elseif($vlaga>=$donja_uljvl && $vlaga<=$ul_vl){
  $minus_kolicina = ('100' - $vlaga) / ('100' - $ul_vl);
  $dnv = $neto - ($neto * $minus_kolicina);
  $dnv = str_replace('-', '', $dnv) / 2;
}
elseif($vlaga>$gornja_uljvl){
  $minus_kolicina = ('100' - $gornja_uljvl) / ('100' - $ul_vl);
  $dnv = $neto - ($neto * $minus_kolicina);
  $dnv =  -$dnv;
}
elseif($vlaga>$ul_vl){
  $minus_kolicina = ('100' - $vlaga) / ('100' - $ul_vl);
  $dnv = $neto - ($neto * $minus_kolicina);
  $dnv = -$dnv;
}
 */

  if($primese<$donja_uljpr){
 $dnp = $neto * (($ul_pr - $donja_uljpr) / '200');
 }
 elseif($primese>=$donja_uljpr && $primese<=$ul_pr){
 $dnp = $neto * (($ul_pr - $primese) / '200');
 }
 elseif($primese>$gornja_uljpr){
 $dnp = $neto * (($ul_pr - $gornja_uljpr) / '100');
 }
 elseif($primese>$ul_pr){
 $dnp = $neto * (($ul_pr - $primese) / '100');
 }
 $srps = $neto + $dnv + $dnp;
}
//KRAJ PRORAČUNA ULJANE REPICE

/*************************************************** P Š E N I C A ***********************************************************/

//PRORAČUN PŠENICE
if($kultura == 'PŠENICA'){
//Proračun Hektolitra
if($hektolitar < $donja_pshl_bo){
$dnh = (((($hektolitar-$donja_pshl_bo)*'2')/'100')*$neto + ((($donja_pshl_bo-$ps_hl)/'2')/'100')*$neto);
}

if(($hektolitar >= 72 && $hektolitar < 74)){
$dnh = (((($hektolitar-74)*'1')/'100')*$neto+(((74-$ps_hl)/'2')/'100')*$neto);
}

if(($hektolitar >= 74) && ($hektolitar < $ps_hl)){   //proracun hektolitra ukoliko je vrednost hektolitra veci od donje bonifikacije hektolitra pšenice
$dnh = (((($hektolitar-$hektolitar)*'2')/'100')*$neto+((($hektolitar-$ps_hl)/'2')/'100')*$neto);
}

if(($hektolitar >= $ps_hl) && ($hektolitar <= $gornja_pshl_bo)){
$dnh = (((($hektolitar-$hektolitar)*'2')/'100')*$neto+((($hektolitar-$ps_hl)/'2')/'100')*$neto);
}
if($hektolitar > $gornja_pshl_bo){
$dnh = (((($gornja_pshl_bo-$gornja_pshl_bo)*'2')/'100')*$neto+((($gornja_pshl_bo-$ps_hl)/'2')/'100')*$neto);
}

//proračun vlage kod pšenice
if($vlaga < $donja_vlps){                               //proracun vlage ukoliko je vlaga manja od donje bonifikacije
$minus_kolicina=('100'- $donja_vlps)/('100'-$ps_vl);
$dnv=($neto * $minus_kolicina)-$neto;
$dnv=round(floatval($dnv), 2);
$dnv=str_replace('-', '', $dnv);
 }
elseif($vlaga>=$donja_vlps && $vlaga<=$ps_vl){          //proracun vlage ukoliko je vlaga veca od donje bonifikacije  i ukoliko je vlaga manjaili jednaka srps vrednosti vlage kod pšenice
$minus_kolicina = ('100' - $vlaga) / ('100' - $ps_vl);
$dnv = $neto - ($neto * $minus_kolicina);
$dnv =  round(floatval($dnv), 2);
$dnv = str_replace('-', '', $dnv);
 }
elseif($vlaga>$gornja_vlps){                           //proracun vlage ukoliko je vlaga veca od donje bonifikacije
$minus_kolicina = ('100' - $gornja_vlps) / ('100' - $ps_vl);
$dnv = $neto - ($neto * $minus_kolicina);
$dnv =  '-'.round(floatval($dnv), 2);
}
elseif($vlaga>$ps_vl){
$minus_kolicina = ('100' - $vlaga) / ('100' - $ps_vl);
$dnv = $neto - ($neto * $minus_kolicina);
$dnv =  '-'.round(floatval($dnv), 2);
}

//proračun primesa kod pšenice
if($primese<$donja_prps){
$dnp = $neto * (($ps_pr - $donja_prps) / '100');
$dnp = round(floatval($dnp), 2);
}
elseif($primese>=$donja_prps && $primese<=$ps_pr){
$dnp = $neto * (($ps_pr - $primese) / '100');
$dnp = round(floatval($dnp), 2);
}
elseif($primese>$gornja_prps){
$dnp = $neto * (($ps_pr - $gornja_prps) / '100');
$dnp = round(floatval($dnp), 2);
}
elseif($primese>$ps_pr){
$dnp = $neto * (($ps_pr - $primese) / '100');
$dnp = round(floatval($dnp), 2);
}


$srps = $neto+$dnv+$dnp+$dnh;

if($vlaga>14 && $vlaga<=14.50){
   $trs = ($neto * $pa14);
} elseif($vlaga>14.50 && $vlaga<=15.00){
   $trs = ($neto * $pa14_50);
} elseif($vlaga>15.00 && $vlaga<=15.50){
   $trs = ($neto * $pa15);
} elseif($vlaga>15.50 && $vlaga<=16.00){
   $trs = ($neto * $pa15_50);
} elseif($vlaga>16.00 && $vlaga<=16.50){
   $trs = ($neto * $pa16);
} elseif($vlaga>16.50 && $vlaga<=17.00){
   $trs = ($neto * $pa16_50);
} elseif($vlaga>17.00 && $vlaga<=17.50){
   $trs = ($neto * $pa17);
} elseif($vlaga>17.50 && $vlaga<=18.00){
   $trs = ($neto * $pa17_50);
} elseif($vlaga>18.10 && $vlaga<=18.50){
   $trs = ($neto * $pa18);
} elseif($vlaga>18.50 && $vlaga<=19.00){
   $trs = ($neto * $pa18_50);
} elseif($vlaga>19.00 && $vlaga<=19.50){
   $trs = ($neto * $pa19);
} elseif($vlaga>19.50 && $vlaga<=20.00){
   $trs = ($neto * $pa19_50);
}
// suvo zrno na raspolaganju
$suvo = $srps - $trs;

}
/*************************************************** E N D  P Š E N I C A ***********************************************************/

//$jus = round(floatval($srps),2);
$jus = number_format($srps, 2, '.', '');
$cena_ukupno = $jus * $cena;

//UPISIVANJE U BAZU ZA SUNCOKRET, SOJU I ULJANU REPICU
if($kultura == 'SUNCOKRET' or $kultura == 'SOJA' or $kultura == 'ULJANA REPICA'){
   $kvaeri = "INSERT INTO prijem SET koop_id = '".$koop_id."', predaje_za='".$predaje."', kultura = '".$kultura."', datum='".date('Y-m-d')."',vreme='".date('H:i')."',
                                     vozac = '".$vozac."', reg = '".$reg."', bruto = '".$bruto."', tara = '".$tara."', vlaga = '".$vlaga."',
                                     primese = '".$primese."', neto = '".$neto."', dnv = '".$dnv."', dnp = '".$dnp."', neto_x_vlaga = '".$neto_x_vlaga."',
                                     neto_x_primese = '".$neto_x_primese."', srps = '".$jus."', status='".$status."', cena='".$cena."', rok_placanja='".$rok."', cena_ukupno='".$cena_ukupno."', magacin = '".$_SESSION['magacin']."',
                                     magacioner = '".$_SESSION['ime']."'";
$unos = mysql_query($kvaeri) or die(mysql_error());
$id = mysql_insert_id();
 $update = mysql_query("UPDATE prijem SET prijem_id='".sha1(md5($id))."' WHERE id = '$id'") or die(mysql_error());
mysql_close($konekcija);
}

//UPISIVANJE U BAZU ZA PŠENICU
if($kultura == 'PŠENICA'){
   $kvaeri = "INSERT INTO prijem SET koop_id = '".$koop_id."', predaje_za='".$predaje."', kultura = '".$kultura."', datum='".date('Y-m-d')."',vreme='".date('H:i')."',
                                     vozac = '".$vozac."', reg = '".$reg."', bruto = '".$bruto."', tara = '".$tara."', vlaga = '".$vlaga."',
                                     primese = '".$primese."', hektolitar = '".$hektolitar."', neto = '".$neto."', dnv = '".$dnv."', dnp = '".$dnp."',
                                     dnh = '".$dnh."', neto_x_vlaga = '".$neto_x_vlaga."', neto_x_primese = '".$neto_x_primese."',
                                     neto_x_htl = '".$neto_x_htl."', srps = '".$jus."', status='".$status."', cena='".$cena."', rok_placanja='".$rok."',
                                     troskovi_susenja =  '".$trs."', suvo_zrno =  '".$suvo."',
                                     cena_ukupno='".$cena_ukupno."', magacin = '".$_SESSION['magacin']."', magacioner = '".$_SESSION['ime']."'";
$unos = mysql_query($kvaeri) or die(mysql_error());
$id = mysql_insert_id();
$update = mysql_query("UPDATE prijem SET prijem_id='".sha1(md5($id))."' WHERE id = '$id'") or die(mysql_error());
mysql_close($konekcija);
}


//UPISIVANJE U BAZU ZA KUKURUZ SIROVI
if($kultura == 'KUKURUZ SIROVI'){
   $kvaeri = "INSERT INTO prijem SET koop_id = '".$koop_id."', predaje_za='".$predaje."', kultura = '".$kultura."', datum = '".date('Y-m-d')."', vreme='".date('H:i')."',
                                     vozac = '".$vozac."', reg = '".$reg."', bruto = '".$bruto."', tara = '".$tara."', vlaga = '".$vlaga."',
                                     primese = '".$primese."', neto = '".$neto."', rastur = '".$netox."', lom = '".$lom."', defekt = '".$defekt."',
                                     dnv = '".$dnv."', dnp = '".$dnp."', dnl = '".$dnl."', dnd = '".$dnd."', kalo_i_rastur = '".$kalo."',
                                     neto_x_vlaga = '".$neto_x_vlaga."', neto_x_primese = '".$neto_x_primese."', neto_x_lom = '".$neto_x_lom."',
                                     neto_x_defekt = '".$neto_x_defekt."', srps = '".$jus."', status='".$status."', cena='".$cena."', rok_placanja='".$rok."', cena_ukupno='".$cena_ukupno."', troskovi_susenja =  '".$trs."', suvo_zrno =  '".$suvo."',
                                     magacin = '".$_SESSION['magacin']."', magacioner = '".$_SESSION['ime']."'";
$unos = mysql_query($kvaeri) or die(mysql_error());
$id = mysql_insert_id();
$update = mysql_query("UPDATE prijem SET prijem_id='".sha1(md5($id))."' WHERE id = '$id'") or die(mysql_error());
mysql_close($konekcija);
}

//UPISIVANJE U BAZU ZA KUKURUZ SUVI
if($kultura == 'KUKURUZ SUVI'){
   $kvaeri = "INSERT INTO prijem SET koop_id = '".$koop_id."', predaje_za='".$predaje."', kultura = '".$kultura."', datum = '".date('Y-m-d')."', vreme='".date('H:i')."',
                                     vozac = '".$vozac."', reg = '".$reg."', bruto = '".$bruto."', tara = '".$tara."', vlaga = '".$vlaga."',
                                     primese = '".$primese."', neto = '".$neto."', lom = '".$lom."', defekt = '".$defekt."', dnv = '".$dnv."',
                                     dnp = '".$dnp."', dnl = '".$dnl."', dnd = '".$dnd."', neto_x_vlaga = '".$neto_x_vlaga."',
                                     neto_x_primese = '".$neto_x_primese."', neto_x_lom = '".$neto_x_lom."', neto_x_defekt = '".$neto_x_defekt."',
                                     srps = '".$jus."', status='".$status."', cena='".$cena."', rok_placanja='".$rok."', cena_ukupno='".$cena_ukupno."', magacin = '".$_SESSION['magacin']."', magacioner = '".$_SESSION['ime']."'";

$unos = mysql_query($kvaeri) or die(mysql_error());
$id = mysql_insert_id();
$update = mysql_query("UPDATE prijem SET prijem_id='".sha1(md5($id))."' WHERE id = '$id'") or die(mysql_error());
mysql_close($konekcija);
}
}

?>