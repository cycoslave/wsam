<?php
 //  Template 0.2.0
 // --> Desc.: pkg
 // (c) 2013-2017 Wicked Software 

 class pkg {
  private $pkg = array();
  
  const VER = "0.1.0";
  const CREATEDON = "01/01/2017";
  const DESC = "this package does something.";
  const HASDB = FALSE;

  function __construct () {
  	$this->init();
  }

  function __destruct() { }

  private function init() { }

  static function ver() { return self::VER; }
  static function name() { return get_class(); }
  static function createdon() { return self::CREATEDON; }
  static function desc() { return self::DESC; }
  static function pkg_hasdb() { return self::HASDB; }

 }
?>