<?php
  class Model {
    function __construct(){
      //connection params is set in config file
      $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
    }
  }
?>