<?php
 //  Template 0.1.2
 // --> Desc.: pkg
 // (c) 2013-2014 Wicked Software 

 class pkg {
  private $ver = "0.1.0";
  private $test = true;   // works backward, false = test mode
  
  private $pkg_wsam = TRUE;
  private $pkg_wsamver = "2.3";
  private $pkg_name = "pkg";
  private $pkg_fullname = "pkg";
  private $pkg_desc ="pkg desc.";
  private $pkg_location = "/src/class/";  
  private $pkg_upgradable  = 1;
  private $pkg_priority = 1;
  private $pkg_dependencies = array ( 0 => array ( 0 => 0, "name" => "", "ver" => "", "sublocation" => FALSE, "hash" => "" ) );
  private $pkg_lastver = "0.0.0";    
  private $pkg_released = "04/01/2014";  
  private $pkg_copyright = "(c) 2014 Wicked Software";
  private $pkg_hasdatabase = FALSE;
  private $pkg_dbstruct = array ( 0 => array( 0 => 0, "Field" => "", "Type" => "", "Null" => "NO", "Key" => "", "Default" => "", "Extra" => "" ) );
  private $dbtable = "";  

  
  function __construct ($mode="test") {
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

  function init() {
  }

 }
?>