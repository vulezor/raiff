<?php
  class Session {

      public static function set_session_id($session_id){
          session_id($session_id);
      }

    //initiate session
    public static function init(){
      @session_start();
    }

    // @param key for session
    // @param value for session
    // Set session key and value
    public static function set($key, $value){
      $_SESSION[$key] = $value;
    }

    //Get session value from specified array keys from set of Session::set();
    public static function get($key){
      if(isset($_SESSION[$key]))
      return $_SESSION[$key];
    }

    //destroy sessions
    public static function destroy(){
      session_destroy();
    }

    //for testing purposes only
    public static function check(){
       print_r($_SESSION);
    }
  }
?>