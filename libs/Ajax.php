<?php
class Ajax{
  /*
  * DESCRIPTION:
  * This static method can call in controllers methods like first line of code like this: "Ajax::ajaxCheck();".
  * Use only if you want strictly call methods with type of async call.
  */
    public static function ajaxCheck(){
       if(empty($_SERVER['HTTP_Xs_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
         echo 'Only async request can access the data';
         die;
       }
    }
}

?>