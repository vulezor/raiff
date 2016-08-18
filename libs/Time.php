<?php
class Time
{
   /*
   *@param $t set value in seconds
   *@param $f default join for this object is :
   *@return clock in format hh:mm:ss
   */	
   public static function secToHour($t,$f=':'){
   return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
  }
  
  /*
   *@param $t set value in hour in format hh:mm:ss important
   *@description of action: make array spliting clock with ':' and setit values in array key order into list variables
   *@return total seconds of clock
   */	
  public static function hourToSec($t){
  list($hour, $minutes, $sec) = explode(':', $t);
  return ($hour * 3600) + ($minutes * 60) + ($sec);
  }
}
?>