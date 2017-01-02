<?php
 // Wicked Software Application Manager - Kernel class 1.0.0
 // --> Desc.: This is the mothership, where most of the magic occurs.
 // (c) 2011-2017 Wicked Software

 namespace wsam\core;
 
 use wsam\web\response;

 class kernel { 
  const VER = "0.1.0";
  const CREATEDON = "01/01/2017";
  const DESC = "this package does something.";
  const HASDB = FALSE;
  private $pkg = array();
 
  function __construct($mode=FALSE) {
   $this->init();
   if ($mode == "web") {
    $this->pkg['mode'] = "web";
   } elseif ($mode == "console") {
    $this->pkg['mode'] = "console";
   } else {
    $this->pkg['mode'] = FALSE;
   }
  }
 	
  function __destruct() {
  }  

  private function init() {
  	$this->pkg['loadedpkgs'] = array();
  	$this->pkg['loadedpkgs'][0] = 1;
  	$this->pkg['loadedpkgs'][1] = "wsam";
  }

  static function ver() { return self::VER; }
  static function name() { return get_class(); }
  static function createdon() { return self::CREATEDON; }
  static function desc() { return self::DESC; }
  static function pkg_hasdb() { return self::HASDB; }

  private function loadconfig() {
  }

  private function loaddependencies() {
  	if ($this->pkg['mode'] == "web") {
  	} else if ($this->pkg['mode'] == "console") {
  	}
  }
  
  function response() {
  	if ($this->pkg['mode'] == "web") { return response::build; }
  }

 }
?>