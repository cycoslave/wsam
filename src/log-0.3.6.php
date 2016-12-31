<?php
 // Log class 0.3.6
 // --> Desc.: Creates an object which creates logs based on the back-end selected.
 // --> Req.: dbmysql 0.8.5
 //           file 0.4.4
 // (c) 2011-2016 Wicked Software

 class log {
  private $ver = "0.3.6";
  private $test = true;   // works backward, false = test mode
  
  private $pkg_wsam = TRUE;
  private $pkg_wsamver = "2.3";
  private $pkg_name = "log";
  private $pkg_fullname = "Logs";
  private $pkg_desc ="Log object.";
  private $pkg_location = "/src/class/";  
  private $pkg_upgradable  = 1;
  private $pkg_priority = 1;
  private $pkg_dependencies = array ( 0 => array ( 0 => 2, "name" => "", "ver" => "", "sublocation" => FALSE, "hash" => "" ),
                                      1 => array ( 0 => 0, "name" => "file", "ver" => "0.4.4", "sublocation" => FALSE, "hash" => "" ),
                                      2 => array ( 0 => 0, "name" => "dbmysql", "ver" => "1.0.2", "sublocation" => FALSE, "hash" => "" )
                                    );
  private $pkg_lastver = "0.3.5";    
  private $pkg_released = "09/10/2016";  
  private $pkg_copyright = "(c) 2016 Wicked Software";
  private $pkg_hasdatabase = TRUE;
  private $pkg_dbstruct = array ( 0 => array( 0 => 3, "Field" => "", "Type" => "", "Null" => "NO", "Key" => "", "Default" => "", "Extra" => "" ),
                                  1 => array( 0 => 0, "Field" => "id", "Type" => "bigint(20) unsigned", "Null" => "NO", "Key" => "PRI", "Default" => "", "Extra" => "auto_increment" ),
                                  2 => array( 0 => 0, "Field" => "timestamp", "Type" => "timestamp", "Null" => "NO", "Key" => "", "Default" => "CURRENT_TIMESTAMP", "Extra" => "on update CURRENT_TIMESTAMP" ),
                                  3 => array( 0 => 0, "Field" => "log", "Type" => "longtext", "Null" => "YES", "Key" => "", "Default" => "", "Extra" => "" ) );    
  
  private $mode; // this can be test, mysql or file
  
  private $debugmode = TRUE; // used to tell everything that will go on in the script.
  private $securitymode = TRUE; // used to log anything security related.
  
  var $db = FALSE; 
  var $dbtable = "cLog";
  var $file = FALSE;
  var $fileloc = FALSE;
  
  private $timestamps = FALSE;
  private $showip = FALSE;
  
  function __construct ($mode="test", $arg1="", $arg2="", $arg3="", $arg4="", $arg5="") {
   if ($mode == "mysql") {
    $this->mode = "mysql";
    if ($arg1 == "") { die("Log: This class needs a valid database object."); }
    $this->db = $arg1;
    if ($this->debug()) { $this->add("Log class ".$this->ver()." loaded!"); }
    //if ($this->debug()) { if (mysql_error($this->db->dblink())) { echo mysql_error($this->db->dblink()); } }
   } elseif ($mode == "file") {
    $this->mode = "file";
    if ($arg1 == "") { 
     die("Log: This class needs a valid file object."); 
    } else {
     $this->fileloc = $arg1;
     $this->file = new file($this->fileloc, "a+");
    }
   } else {
     //runs in test mode by default.
    $this->mode = "test";
    $this->test = false;
   }
  }

  function __destruct() {
  }
  
  function ver() { return $this->ver; }
  function pkg_wsamver() { return $this->pkg_wsamver; } 
  function pkg_name() { return $this->pkg_name; }
  function pkg_fullname() { return $this->pkg_fullname; }
  function pkg_released() { return $this->pkg_released; }
  function pkg_desc() { return $this->pkg_desc; }
  function pkg_copyright() { return $this->pkg_copyright; }
  function pkg_hasdb() { return $this->pkg_hasdatabase; }
  function pkg_dbtable() { return $this->dbtable; }
  function pkg_dbstruct() { return $this->pkg_dbstruct; } 
  function pkg_dependencies() { return $this->pkg_dependencies; }
  function pkg_location() { return $this->pkg_location; }
  function pkg_checkdb() { return TRUE; }
  function pkg_createdb() { return FALSE; }
  
  function debug() {
   return $this->debugmode;  
  }
  
  function security() {
   return $this->securitymode;  
  }  
  
  function add($text) {
  	if ($this->timestamps) {
  	 $ts = date('[G:i:s d/m/Y]');
  	 $text = $ts."  ".$text;
  	}
  	if ($this->mode == "mysql") {
    $db = &$this->db; 	
    $db->query("INSERT INTO `".$this->dbtable."` ( `log` ) VALUES ( '".$text."' );"); 
    return $db->getlastidadded();
  	} elseif ($this->mode == "file") {
  	 $this->file->setContent($text."\n");
  	 if ($this->file->write()) {
  	  return TRUE;
  	 } else {
  	  return FALSE;
  	 }
  	} else {
  	 return FALSE;
  	}
  }
  
  function setTimestamp($value=FALSE) {
  	if ($value) {
  	 $this->timestamps = TRUE;
  	} else {
  	 $this->timestamps = FALSE;
  	}
  }
  
 }
?>