<?php 
 // Wicked Software Application Manager - Kernel class 1.0.0
 // --> Desc.: This is the mothership, where most of the magic occurs.
 // (c) 2011-2017 Wicked Software

 namespace wsam\web;

 class query { 
 
  function __construct() {
  }
 	
  function __destruct() {
  }  

  private function init() {
  	$this->pkg['ver'] = "1.0.0";
  	$this->pkg['creationdate'] = "31/12/2016";
  	$this->pkg['name'] = "WSAM";
  	$this->pkg['desc'] = "Manages packages from an application built with the framework.";
  	$this->pkg['loadedpkgs'] = array();
  	$this->pkg['loadedpkgs'][0] = 1;
  	$this->pkg['loadedpkgs'][1] = "wsam";
  	$this->pkg['config'] = "/web/wsam/etc/app.conf";
  }


 }
?>