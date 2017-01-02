<?php
 // Wicked Software Application Manager - Kernel class 1.0.0
 // --> Desc.: This is the mothership, where most of the magic occurs.
 // (c) 2011-2017 Wicked Software

 namespace wsam\web;

 class query { 
  const VER = "1.0.0";
  const CREATEDON = "01/01/2017";
  const DESC = "this package does something.";
  const HASDB = FALSE;
   
  function __construct() {
  }
 	
  function __destruct() {
  }  

  private function init() { }

  static function ver() { return self::VER; }
  static function name() { return get_class(); }
  static function createdon() { return self::CREATEDON; }
  static function desc() { return self::DESC; }
  static function pkg_hasdb() { return self::HASDB; }
  
  static function build() {
  	return "test";
  }
 }
?>