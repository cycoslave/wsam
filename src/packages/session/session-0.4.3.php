<?php
 // Session class 0.4.3
 // --> Desc.: Creates a session object that support different backend.
 // --> Types of backends: php - uses PHP sessions storage backend
 //                        file - saves sessions to the servers filesystem 
 //                        mysql - store the session data in mysql (not coded yet)
 // (c) 2008-2012 Wicked Software


 class session {
  private $ver = "0.4.3";
  private $test = true;   // works backward, false = test mode 

  private $pkg_wsam = TRUE;
  private $pkg_wsamver = "2.3";
  private $pkg_name = "session";
  private $pkg_fullname = "Session";
  private $pkg_desc ="Creates a session object that support different backend.";
  private $pkg_location = "/src/class/";  
  private $pkg_upgradable  = FALSE;
  private $pkg_priority = 1;
  private $pkg_dependencies = array ( 0 => array ( 0 => 0, "name" => "", "ver" => "", "sublocation" => FALSE, "hash" => "" ) );
  private $pkg_lastver = "0.4.2";    
  private $pkg_released = "22/08/2012";  
  private $pkg_copyright = "(c) 2012 Wicked Software";
  private $pkg_hasdatabase = FALSE;
  private $pkg_dbstruct = array ( 0 => array( 0 => 0, "Field" => "", "Type" => "", "Null" => "NO", "Key" => "", "Default" => "", "Extra" => "" ) );        
  private $dbtable = "";  
  
  private $usecookie = false; 
  private $type; 
  private $id;
  private $timeout = 900; // in secs
  private $fingerprint = "WSPHPSESS042@#%!@!%~";
  private $hijack = FALSE;
  var $ts;
  
  
  function __construct($type="test", $arg1=FALSE, $arg2=FALSE, $arg3=FALSE, $arg4=FALSE, $arg5=FALSE) {   
   if ($type == "test") {
    $this->test = false;
    $this->type = $type;
   } else {
    if ($type == "file") { 
     $this->type = $type; 
     session_set_save_handler(array(&$this, 'file_open'),
                              array(&$this, 'file_close'), 
                              array(&$this, 'file_read'), 
                              array(&$this, 'file_write'), 
                              array(&$this, 'file_destroy'), 
                              array(&$this, 'file_gc'));
    } elseif ($type == "mysql") { 
     $this->type = $type; 
    } else {
     //when everything else fails use php
     $this->type = "php";   
     if ($arg1 != "") { session_id($arg1); }
    }
    $this->ts = time();
    register_shutdown_function('session_write_close');
    $this->start();
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
  
  function start() {
   session_start();
   if (!isset($_SESSION['initiated'])) { 
    $_SESSION['initiated'] = TRUE;
    session_regenerate_id();   
   }
   $this->id = session_id();
   if (isset($_SESSION['HTTP_USER_AGENT'])) {
    if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'].$this->fingerprint)) { $this->hijack = TRUE; }  
   } else {
    $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT'].$this->fingerprint);
   }
  }
  
  function stop() {
   session_destroy();
  }
  
  function enable_cookie() {
   $this->usecookie = true;
  }
  
  function disable_cookie() {
   $this->usecookie = false;
  }
  
  function use_cookie() {
   return $this->usecookie;
  }
 
  function isHijacked() {
   return $this->hijack;
  }




  function addvar($var,$value) {
   if (!isset($_SESSION[$var])) { 
    $_SESSION[$var] = $value; 
    return true;   
   } else {
    return false;   
   } 
  }
  
  function setvar($var,$value) {
   if (!isset($_SESSION[$var])) {
    $_SESSION[$var] = $value; 
    return true;   
   } else {
    return false;
   }  
  }
  
  function delvar($var) {
   if (!isset($_SESSION[$var])) {
    unset($_SESSION[$var]);
    return true; 
   } else {
    return false;   
   }  
  }
  
  function getvar($var) {
   if (!isset($_SESSION[$var])) { 
    return $_SESSION[$var];
   } else {
    return false;
   }  
  }
 
  function file_open($save_path, $session_name) {
   global $sess_save_path;
   $sess_save_path = $save_path;
   return(true);
  }

  function file_close() {
   return(true);
  }

  function file_read($id) {
   global $sess_save_path;

   $sess_file = "$sess_save_path/sess_$id";
   return (string) @file_get_contents($sess_file);
  }

  function file_write($id, $sess_data) {
   global $sess_save_path;

   $sess_file = "$sess_save_path/sess_$id";
   if ($fp = @fopen($sess_file, "w")) {
     $return = fwrite($fp, $sess_data);
     fclose($fp);
     return $return;
   } else {
     return(false);
   }
  }

  function file_destroy($id) {
   global $sess_save_path;

   $sess_file = "$sess_save_path/sess_$id";
   return(@unlink($sess_file));
  }

  function file_gc($maxlifetime) {
   global $sess_save_path;

   foreach (glob("$sess_save_path/sess_*") as $filename) {
    if (filemtime($filename) + $maxlifetime < time()) {
      @unlink($filename);
    }
   }
   return true;
  } 
  
  
  function showID() {
   return $this->id;  
  }  
  
 }
 
?>