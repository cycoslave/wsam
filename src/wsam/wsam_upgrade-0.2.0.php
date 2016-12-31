<?php
 // Software Update class 0.1.3
 // --> Desc.: Part of the Wicked Software Application Manager.
 // --> Req.: dbMySQL 0.3.6
 //               Log 0.1.4
 // (c) 2011 Wicked Software
 
 class wck_softupdate {
  private $ver = "0.1.3";
  private $test = true;   // works backward, false = test mode 

  private $wcksoft = TRUE;
  private $wck_copyright = "(c) 2011 Wicked Software";
  private $wck_year = "2011";  
  private $wck_name = "Software Update (part of Software Manager)";
  private $wck_desc ="";      
  
  private $db;
  private $log;
  
  
  function __construct($type, $db="", $log="") {
 	 if ($type == "test") {
    $this->test = false;
   } else {
    $this->db = $db;
    $this->log = $log;
 	}
  }
 	
  function __destruct() {
  }
 	
  function ver() {
   return $this->ver;
  }   
 
 }
?>