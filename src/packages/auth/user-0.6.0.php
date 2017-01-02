<?php
 // User 0.6.0 // --> Desc.: Creates a user object.
 // --> Req.: dbmysql 1.0.0
 // --> DISCLAIMER: This package is not backward compatible with any of the previous packages. 
 //                 Be cautious when upgrading. // (c) 2011-2014 Wicked Software
 
 class user {
  private $ver = "0.6.0";
  private $test = true;   // works backward, false = test mode
  
  private $pkg_wsam = TRUE;
  private $pkg_wsamver = "2.3";
  private $pkg_name = "user";
  private $pkg_fullname = "User";
  private $pkg_desc ="User authenticator.";
  private $pkg_location = "/src/class/";  
  private $pkg_upgradable  = 1;
  private $pkg_priority = 1;
  private $pkg_dependencies = array ( 0 => array ( 0 => 0, "name" => "", "ver" => "", "sublocation" => FALSE, "hash" => "" ) );
  private $pkg_lastver = "0.5.4";    
  private $pkg_released = "22/07/2014";  
  private $pkg_copyright = "(c) 2011-2014 Wicked Software";
  private $pkg_hasdatabase = FALSE;
  private $pkg_dbstruct = array ( 0 => array( 0 => 0, "Field" => "", "Type" => "", "Null" => "NO", "Key" => "", "Default" => "", "Extra" => "" ) );
  private $dbtable = "Users";  
  
  var $db = FALSE;
  var $failedattempt = 0;
  var $lastattempt = 0;
  var $islogged = FALSE;
  var $userid = FALSE;
  var $username = FALSE;
  
  // Seeds used to encrypt passwords
  // NOTE: if you change those, the system will no longer be able to decrypt the passwords stored in your database.
  var $seed1 = "d3f4u|7";
  var $seed2 = "wcKs0f7!";
  var $seed3 = "s3KuRd@T4#";
  
  function __construct ($mode="test", &$db=FALSE, $seed1=FALSE, $seed2=FALSE, $seed3=FALSE) {
   if ($mode == "test") {
    $this->test = false;
   } else {
    if ($db) { $this->db = &$db; }
    if ($seed1) { $this->seed1 = $seed1; }
    if ($seed2) { $this->seed2 = $seed2; }
    if ($seed3) { $this->seed3 = $seed3; }
   }
  }

  function __destruct() {
   //$this->close($this->sql);
   //$this->close($this->result);
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

  function islogged() {
  	return $this->islogged;
  }

  function passhash($user, $pass) {
  	$tmp = str_split($pass, (strlen($pass)/2)+1);
  	if (!isset($tmp[1])) { $tmp[1] = ""; }
  	$pass = hash('sha512', $this->seed1.".Z]".$tmp[0]."*@^".$this->seed2."]-_".$user."$!#".$tmp[1]."%'|".$this->seed3);
  	$tmp[0] = ""; $tmp[1] = "";
  	unset($tmp);
  	return $pass;
  }
  
  function login($user, $pass) {
  	$pass = $this->passhash($user, $pass);
  	$sql = "SELECT * FROM `".$this->dbtable."` WHERE name = :user AND pass = :pass LIMIT 1;";
   $this->db->prepare($sql);
   $this->db->bind(":user", $user, "string");
   $this->db->bind(":pass", $pass, "string");
   $this->db->exec();
   $sql = $this->db->fetch_array();
   if ($sql[0][0]) {
    $this->islogged = TRUE;
    $this->failedattempt = 0;
    $this->lastattempt = 0;
    $this->userid = $sql[1]['id'];
    $this->username = $sql[1]['name'];
    return TRUE;
   } else {
    $this->islogged = FALSE;
    $this->failedattempt += 1;
    $this->lastattempt = time();
    return FALSE;
   }
  }
  
  function exist($user) {
   $sql = "SELECT * FROM `".$this->dbtable."` WHERE name = :user LIMIT 1;";
   $this->db->prepare($sql);
   $this->db->bind(":user", $user, "string");
   $this->db->exec();
   $sql = $this->db->fetch_array();  
   if ($sql[0][0]) {
    return TRUE;
   } else {
    return FALSE;
   }
  }  
  
  function logout () {
   $this->islogged = FALSE;  
   $this->userid = FALSE;
   $this->username = FALSE;
   return TRUE;   
  }  
  
  function showName() {
  	if ($this->islogged) {
  	 return $this->username;
  	} else {
  	 return FALSE;
  	}
  }
  
  function showID() {
  	if ($this->islogged) {
  	 return $this->userid;
  	} else {
  	 return FALSE;
  	}
  }  	
  
//  function newaccount($fromdead="") {
//   $db =& $this->db;
//   if ($fromdead != "") {
//    $sql = "INSERT INTO `".$this->dbtable."` ( `name` , `pass` , `email`, `points` , `superaccess` , `hasforum` , `hasirc` , `hasmail` )";
//    $sql .= " VALUES ( '".$fromdead."',  '".$this->pass."',  '".$this->email."',  '".$this->points."',  '".$this->superaccess."',  '".$this->hasforum."',  '".$this->hasirc."',  '".$this->hasmail."' );"; 
//    $this->newaccount = $fromdead;
//    $this->save("newaccount");
//    $db->query($sql); 
//    return mysql_insert_id($db->dblink());
//   } else {
//    return FALSE;  
//   }
//  }  

 }
?>