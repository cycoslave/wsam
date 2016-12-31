<?php
 // Wicked Software Application Manager class 0.4.3
 // --> Desc.: This is the god of all classes, it manages all the other ones.
 // --> Dependencies: Display 0.5.5
 //                   dbMySQL 1.0.1
 //                   log 0.3.5
 //                   file 0.4.4
 // (c) 2011-2016 Wicked Software
 
 // Note: The WSAM packages needs to be in the src/class/wsam folder of your application.
 
 class wsam { 
  private $ver = "0.4.3";
  private $test = TRUE;   // works backward, false = test mode 
  
  private $pkg_wsam = TRUE; // Compatible with the wcksoft suite.
  private $pkg_wsamver = "2.3";
  private $pkg_name = "wsam";
  private $pkg_fullname = "Wicked Software Application Manager";
  private $pkg_desc = "Manages packages from an application built with the framework.";
  private $pkg_location = "/src/class/wsam/";  
  private $pkg_upgradable  = FALSE;
  private $pkg_priority = 1;
  private $pkg_dependencies = array ( 0 => array ( 0 => 5, "name" => "", "ver" => "", "sublocation" => "", "hash" => "" ),
                                      1 => array ( 0 => 0, "name" => "dbmysql", "ver" => "1.0.1", "sublocation" => "",
                                                   "hash" => "" ),
                                      2 => array ( 0 => 0, "name" => "display", "ver" => "0.5.5", "sublocation" => "",
                                                   "hash" => "" ),
                                      3 => array ( 0 => 0, "name" => "log", "ver" => "0.3.5", "sublocation" => "",
                                                   "hash" => "" ),
                                      4 => array ( 0 => 0, "name" => "file", "ver" => "0.4.4", "sublocation" => "",
                                                   "hash" => "" ),
                                      5 => array ( 0 => 0, "name" => "session", "ver" => "0.4.3", "sublocation" => "",
                                                   "hash" => "" ) );
  private $pkg_lastver = "0.4.2";    
  private $pkg_released = "04/10/2016";  
  private $pkg_copyright = "(c) 2016 Wicked Software";
  private $pkg_hasdatabase = FALSE;
  private $pkg_dbstruct = array ( 0 => array( 0 => 10, "Field" => "", "Type" => "", "Null" => "NO", "Key" => "", "Default" => "", "Extra" => "" ),
                                  1 => array( 0 => 0, "Field" => "id", "Type" => "tinyint(4)", "Null" => "NO", "Key" => "PRI", "Default" => "", "Extra" => "auto_increment" ),
                                  2 => array( 0 => 0, "Field" => "status", "Type" => "enum('uninstalled','offline','online')", "Null" => "NO", "Key" => "", "Default" => "uninstalled", "Extra" => "" ),
                                  3 => array( 0 => 0, "Field" => "mainpkg", "Type" => "varchar(64)", "Null" => "YES", "Key" => "", "Default" => NULL, "Extra" => "" ),
                                  4 => array( 0 => 0, "Field" => "major", "Type" => "int(10) unsigned", "Null" => "NO", "Key" => "", "Default" => "0", "Extra" => "" ),
                                  5 => array( 0 => 0, "Field" => "minor", "Type" => "int(10) unsigned", "Null" => "NO", "Key" => "", "Default" => "0", "Extra" => "" ),
                                  6 => array( 0 => 0, "Field" => "revision", "Type" => "int(10) unsigned", "Null" => "NO", "Key" => "", "Default" => "0", "Extra" => "" ),
                                  7 => array( 0 => 0, "Field" => "location", "Type" => "varchar(512)", "Null" => "YES", "Key" => "", "Default" => NULL, "Extra" => "" ),
                                  8 => array( 0 => 0, "Field" => "useajax", "Type" => "tinyint(1)", "Null" => "NO", "Key" => "", "Default" => "0", "Extra" => "" ),
                                  9 => array( 0 => 0, "Field" => "usejsinjector", "Type" => "tinyint(1)", "Null" => "NO", "Key" => "", "Default" => "0", "Extra" => "" ),
                                  10 => array( 0 => 0, "Field" => "pkgtable", "Type" => "varchar(64)", "Null" => "YES", "Key" => "", "Default" => NULL, "Extra" => "" )                         
                                 );
  private $dbtables = array ( 0 => array( 0 => 2, "dbname" => ""),
                              1 => array( 0 => 0, "dbname" => "wsam_conf" ),
                              2 => array( 0 => 0, "dbname" => "wsam_loaded" )
                             );
  private $dbtable_loaded = "wsam_loaded";                                   
  private $dbtable_conf = "wsam_conf"; 
  private $log;
  private $options = array ( "debug" => FALSE,
                             "depsec" => FALSE,
                             "memlog" => FALSE
                            );
  
  // Software Properties
  private $id;
  private $name;
  private $desc;
  private $version;
  private $major;
  private $minor;
  private $revision;
  private $upgradable;
  private $priority; // if null then it will pull the item as they lay in the db after those with this setted. (from 255 to 0 then null)
  private $dependencies; // this is a list of packages separated with spaces. The are like this: packagename-0.1.2
  private $lastver;  
  private $location; // starts with / which is the root directory of the application.
  private $opened = FALSE;
  
  // General properties
  private $directory = "/default-app/"; 
  private $webaddress = "http://www.default-app.wck/";
  private $installed = FALSE;
  private $loadedpkg = array ( 0 => array ( 0 => 0, "name" => "", "ver" => "", "hash" => "" ) );
  
  private $myfilename = FALSE;
  private $mydir = FALSE;
  
  // MySQL stuff.
  private $db = FALSE;
  private $dhost = "127.0.0.1";
  private $dport = 3306;
  private $duser = "root";
  private $dpass = "";
  private $dname = "WSAM";
  private $mainpkg = FALSE;
  private $pkgobj = FALSE;
  
  private $display;
  private $ajax = FALSE;
  private $ajaxobj = FALSE;
  private $sessions = FALSE;
  private $session = FALSE; 
                                                
  
  function __construct($mode="test", $mainpkg=FALSE, $ajax=FALSE, $sessions=FALSE) {
 	if ($mode == "test") {
    $this->test = FALSE;
   } else {
    $this->mainpkg = $mainpkg;
    if ($ajax) { $this->ajax = TRUE; }
    if ($sessions) { $this->sessions = TRUE; } 
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
  function pkg_createdb() { 
   $tmp = "CREATE TABLE  `".$this->dbtable_conf."` (
    `id` TINYINT NOT NULL ,
    `status` ENUM(  'uninstalled',  'offline',  'online' ) NOT NULL DEFAULT  'uninstalled',
    `mainpkg` VARCHAR( 64 ) NULL DEFAULT NULL ,
    `major` INT UNSIGNED NOT NULL DEFAULT  '0',
    `minor` INT UNSIGNED NOT NULL DEFAULT  '0',
    `revision` INT UNSIGNED NOT NULL DEFAULT  '0',
    `location` VARCHAR( 512 ) NULL DEFAULT NULL ,
    `useajax` BOOLEAN NOT NULL DEFAULT  '0',
    `usejsinjector` BOOLEAN NOT NULL DEFAULT  '0',
    `pkgtable` VARCHAR( 64 ) NOT NULL ,
    PRIMARY KEY (  `id` )
   ) ENGINE = MYISAM ;";
   return $this->db->query($tmp);   
  }
  
  private function start() {
  	$this->selfstart();
  	if ($this->mainpkg) {
    $this->depstart();
    $this->startsql();
    $this->checkdb();
   }
  }
  
  function getHome() { return $this->directory; }
  
  private function selfstart() {
   $this->log("Starting WSAM..");
   if ($this->options['memlog']) { $this->log("Memory usage: ".memory_get_usage(FALSE)." / ".memory_get_usage(TRUE)); }
   $fileloc = $_SERVER['SCRIPT_FILENAME'];
   if (substr_count($fileloc, "root/index.php")) {
    // Launched from the application launcher.
    $this->directory = substr($fileloc, 0, -14);
   } elseif (substr_count($fileloc, "root/load.php")) {
    // Launched from the ajax interface.
    $this->directory = substr($fileloc, 0, -13);
   } elseif (substr_count($fileloc, "root/download.php")) {
    // Launched from the download interface.
    $this->directory = substr($fileloc, 0, -17);    
   } else {
    die(":WSAM: Application launched incorrectly.");
   }
   $this->log(":WSAM: My hash is ".$this->hash());
   $this->log(":WSAM: Loading dependencies..");
   $this->loaddependencies($this->pkg_dependencies, TRUE, TRUE, TRUE);
   
   $this->startdisplay();
   if ($this->loadconfig()) {
    $this->log(":WSAM: Configuration file loaded!");
    if ($this->options['memlog']) { $this->log("Memory usage: ".memory_get_usage(FALSE)." / ".memory_get_usage(TRUE)); }
    global $wsam_conf;   
    if ($wsam_conf['app']['installed']) {
     $this->initsql($wsam_conf['db']['host'], $wsam_conf['db']['user'], $wsam_conf['db']['pass'], $wsam_conf['db']['name'], $wsam_conf['db']['port']);
     $this->startsql();
     $this->checkmasterdb();
     $this->db->query("SELECT * FROM `".$this->dbtable_conf."` LIMIT 1;");
     $sql = $this->db->fetch_array();
     $this->log(":WSAM: Loading ".$sql[1]['mainpkg']." ".$sql[1]['major'].".".$sql[1]['minor'].".".$sql[1]['revision']."..");
     $pkg = FALSE;
     $tmphash = $this->hash($sql[1]['mainpkg']."-".$sql[1]['major'].".".$sql[1]['minor'].".".$sql[1]['revision']);
  	  $this->log(":WSAM: Hash generated ".$tmphash);
  	  //need to add hashes to masterdb
  	  //if (($this->options['depsec']) && ($tmphash !== $deplist[$i]['hash'])) { die(":WSAM: ".$deplist[$i]['name']."Failed checksum check!"); }
  	  $this->log(":WSAM: Checksum succeded for ".$sql[1]['mainpkg']."!");
     if (!file_exists($this->directory."src/class/".$sql[1]['mainpkg']."-".$sql[1]['major'].".".$sql[1]['minor'].".".$sql[1]['revision'].".php")) { die(":WSAM: Failed to load project: ".$sql[1]['mainpkg'].". (".$this->directory."src/class/".$sql[1]['mainpkg']."-".$sql[1]['major'].".".$sql[1]['minor'].".".$sql[1]['revision'].".php)"); }
     $this->log(":WSAM: Project file found! (".$this->directory."src/class/".$sql[1]['mainpkg']."-".$sql[1]['major'].".".$sql[1]['minor'].".".$sql[1]['revision'].".php)");
     require_once $this->directory."src/class/".$sql[1]['mainpkg']."-".$sql[1]['major'].".".$sql[1]['minor'].".".$sql[1]['revision'].".php";
     $this->log(":WSAM: Loaded the file, now trying to load the object..");
     //if ($sql[1]['mainpkg'] != NULL) { eval("\$pkg = new ".$sql[1]['mainpkg']."(\"start\", \$this->db);"); }
     if ($sql[1]['mainpkg'] != NULL) { 
      $pkg = new $sql[1]['mainpkg']("start", $this->db); 
      $this->pkgobj = &$pkg;
     }
     if ($pkg === FALSE) { $this->log(":WSAM: Failed to load the object!"); }
     $this->log(":WSAM: Loading the project's dependencies..");
     $this->loaddependencies($pkg->pkg_dependencies(), TRUE, TRUE, TRUE); 
     //if ($this->sessions) { 
     // $this->session = new session("start");
     // $_SESSION['app'] = &$this; 
     //}
     if ($this->sessions) {
     	$this->session = new session("start");
      if (isset($_SESSION['app'])) {
       throw new Exception("wsam-session-object-exists");
      } else {
       $_SESSION['app'] = &$this;
       $this->pkgobj->start();
      }
     }
//     if ($this->ajax) {

//     } else {
      if ($sql[1]['status'] == "offline") {
       $pkg->offline();
      } elseif ($sql[1]['status'] == "online") {
       echo $pkg->online();
      } else {
       $pkg->installer();
      }
//     }
     return TRUE;
    }
   }
   return FALSE;
  } 
  
  private function loaddependencies($deplist=FALSE, $checkint=FALSE, $checkdb=FALSE, $checkdep=FALSE) {
  	$num = 0;
  	if (is_array($deplist)) {
  	 for ($i = 1; $i <= $deplist[0][0]; $i++) {
  	  $this->log(":WSAM: Loading ".$deplist[$i]['name']."..");
  	  if ($this->options['memlog']) { $this->log("Memory usage: ".memory_get_usage(FALSE)." / ".memory_get_usage(TRUE)); }
  	  $tmphash = $this->hash($deplist[$i]['name']."-".$deplist[$i]['ver'], $deplist[$i]['sublocation']);
  	  $this->log(":WSAM: Hash generated ".$tmphash);
  	  if (($this->options['depsec']) && ($tmphash !== $deplist[$i]['hash'])) { die(":WSAM: ".$deplist[$i]['name']."Failed checksum check!"); }
  	  $this->log(":WSAM: Checksum succeded for ".$deplist[$i]['name']."!");
     if ($deplist[$i]['sublocation']) {
      if (!file_exists($this->directory."src/class/".$deplist[$i]['sublocation'].$deplist[$i]['name']."-".$deplist[$i]['ver'].".php")) { die(":WSAM: Failed to load dependency: ".$deplist[$i]['name'].". (".$this->directory."src/class/".$deplist[$i]['name']."-".$deplist[$i]['ver'].".php)"); }
      require_once $this->directory."src/class/".$deplist[$i]['sublocation'].$deplist[$i]['name']."-".$deplist[$i]['ver'].".php";
     } else {
      if (!file_exists($this->directory."src/class/".$deplist[$i]['name']."-".$deplist[$i]['ver'].".php")) { die(":WSAM: Failed to load dependency: ".$deplist[$i]['name'].". (".$this->directory."src/class/".$deplist[$i]['name']."-".$deplist[$i]['ver'].".php)"); }
      require_once $this->directory."src/class/".$deplist[$i]['name']."-".$deplist[$i]['ver'].".php";
     }
     if ($checkint) { 
  	   $this->log(":WSAM: Checking the integrity of the package ".$deplist[$i]['name']."-".$deplist[$i]['ver']."..");
  	   if ($deplist[$i]['sublocation']) {
  	    $pkgfile = file_get_contents($this->directory."src/class/".$deplist[$i]['sublocation'].$deplist[$i]['name']."-".$deplist[$i]['ver'].".php");
  	   } else {
  	    $pkgfile = file_get_contents($this->directory."src/class/".$deplist[$i]['name']."-".$deplist[$i]['ver'].".php");
  	   }
  	   if (!substr_count($pkgfile, "class ")) { die(":WSAM: This package is not a valid PHP class. (".$deplist[$i]['name'].")"); }
  	   if (!substr_count($pkgfile, "private \$pkg_wsam = TRUE;")) { die(":WSAM: This package is not a valid WSAM package. (".$deplist[$i]['name'].")"); }
  	   eval("\$pkgobj = new ".$deplist[$i]['name']."();");
  	   $this->log(":WSAM: Version of ".$deplist[$i]['name']." has been confirmed to be v.".$pkgobj->ver().".");
  	   $this->log(":WSAM: ".$deplist[$i]['name']." uses WSAM version ".$pkgobj->pkg_wsamver());
  	   if ($pkgobj->pkg_hasdb()) { 
  	    $this->log(":WSAM: ".$deplist[$i]['name']." has a database."); 
  	    if ($checkdb) { if (!$pkgobj->pkg_checkdb()) { die(":WSAM: The database for ".$deplist[$i]['name']."-".$deplist[$i]['ver']." has been found invalid."); } } 
  	   }	 
  	   $this->log(":WSAM: No problems found with ".$deplist[$i]['name']."-".$deplist[$i]['ver']."!");
     }
  	  if ($checkdep) {
  	   $pkgdep = $pkgobj->pkg_dependencies();
  	   if ($pkgdep[0][0]) {
  	    $this->log(":WSAM: Loading dependencies for package ".$deplist[$i]['name']."-".$deplist[$i]['ver']."..");
  	    $this->loaddependencies($pkgobj->pkg_dependencies(), $checkint, $checkdb, $checkdep);
  	    $this->log(":WSAM: Dependencies loaded for package ".$deplist[$i]['name']."-".$deplist[$i]['ver']."!");
  	   } else {
  	    $this->log(":WSAM: Package ".$deplist[$i]['name']."-".$deplist[$i]['ver']." does not have any dependencies.");
  	   }
  	  }       
     $num++;
     if ($deplist[$i]['sublocation']) {
      $this->log(":WSAM: Loaded ".$deplist[$i]['name']."! (".$this->directory."src/class/".$deplist[$i]['sublocation'].$deplist[$i]['name']."-".$deplist[$i]['ver'].".php)");
     } else {
      $this->log(":WSAM: Loaded ".$deplist[$i]['name']."! (".$this->directory."src/class/".$deplist[$i]['name']."-".$deplist[$i]['ver'].".php)");
     }
     if ($this->options['memlog']) { $this->log("Memory usage: ".memory_get_usage(FALSE)." / ".memory_get_usage(TRUE)); }
    }
   }
   return $num;
  }
  
  private function initsql($dhost=FALSE, $duser=FALSE, $dpass=FALSE, $dname=FALSE, $dport=FALSE) {
  	$this->log(":WSAM: Initializing SQL database.. (sql://".$duser."@".$dhost.":".$dport.")");
  	if ($dhost) { $this->dhost = $dhost; }
  	if ($duser) { $this->duser = $duser; }
  	if ($dpass) { $this->dpass = $dpass; }
  	if ($dname) { $this->dname = $dname; }
  	if ($dport) { $this->dport = $dport; }
  }
  
  private function startsql() {
   $this->log(":WSAM: Starting the MySQL driver.");  
   if ($this->options['memlog']) { $this->log("Memory usage: ".memory_get_usage(FALSE)." / ".memory_get_usage(TRUE)); }
   $this->db = new dbmysql("start", $this->dhost, $this->duser, $this->dpass, $this->dname, $this->dport);
   $this->log(":WSAM: MySQL package v".$this->db->ver());
   $sqlver = $this->db->sqlver();
   if ($sqlver) {
    $this->log(":WSAM: This server is running MySQL version ".$this->db->sqlver());
    $this->log(":WSAM: MySQL driver ready.");
   } else {
    $this->log(":WSAM: Failed to load MySQL driver.");
    die(":WSAM: Cannot load required data from database to continue.");
   }
   if ($this->options['memlog']) { $this->log("Memory usage: ".memory_get_usage(FALSE)." / ".memory_get_usage(TRUE)); }
  }
  
  private function startdisplay() {
  	$this->log(":WSAM: Starting the Display driver.");
  	if ($this->options['memlog']) { $this->log("Memory usage: ".memory_get_usage(FALSE)." / ".memory_get_usage(TRUE)); }
  	$this->display = new display("start");
  	$this->log(":WSAM: Display version ".$this->display->ver());
  	$this->log(":WSAM: Display driver ready.");
  	if ($this->options['memlog']) { $this->log("Memory usage: ".memory_get_usage(FALSE)." / ".memory_get_usage(TRUE)); } 
  }
  
  private function hash($pkg=FALSE, $subloc=FALSE) {
  	if ($pkg) {
  	 if ($subloc) {
  	  return hash_file("sha512", $this->directory."src/class/".$subloc.$pkg.".php");
  	 } else {
  	  return hash_file("sha512", $this->directory."src/class/".$pkg.".php");
  	 }
  	} else {
  	 if ($subloc) {
  	  return hash_file("sha512", $this->directory."src/class/wsam/".$subloc.$this->pkg_name."-".$this->ver.".php");;
  	 } else {
  	  return hash_file("sha512", $this->directory."src/class/wsam/".$this->pkg_name."-".$this->ver.".php");;
  	 }
  	}
  }  
  
  function show() { if ($this->test) { return $this->display->show(); } return FALSE; }
  function add($data) { if ($this->test) { return $this->display->add($data); } return FALSE; }
  function clear() { if ($this->test) { return $this->display->clear(); } return FALSE; }
  function addDoctype($type, $ver, $doctype) { if ($this->test) { $this->display->addDoctype($type, $ver, $doctype); } return FALSE; }
  function startHTML() { if ($this->test) { return $this->display->startHTML(); } return FALSE; }
  function endHTML() { if ($this->test) { return $this->display->endHTML(); } return FALSE; }  
  function startHead() { if ($this->test) { return $this->display->startHead(); } return FALSE; }
  function endHead() { if ($this->test) { return $this->display->endHead(); } return FALSE; }
  function startBody() { if ($this->test) { return $this->display->startBody(); } return FALSE; }
  function endBody() { if ($this->test) { return $this->display->endBody(); } return FALSE; }  
  function addTitle($title=FALSE) { if ($this->test) { return $this->display->addTitle($title); } return FALSE; } 
  function addCharset($charset="ISO-8859-1") { if ($this->test) { return $this->display->addCharset($charset); } return FALSE; }
  function addContentlang($lang="en-US") { if ($this->test) { return $this->display->addContentlang($lang); } return FALSE; }
  function addLink($type=false, $arg=false) { if ($this->test) { return $this->display->addLink($type, $arg); } return FALSE; }
  function addScript($type=false,$arg=false) { if ($this->test) { return $this->display->addScript($type, $arg); } return FALSE; }
  function addMeta($name=false,$arg) { if ($this->test) { return $this->display->addMeta($name, $arg); } return FALSE; }  
   
  private function loadconfig() {
  	$this->log(":WSAM: Checking for configuration file..");
  	if (file_exists($this->directory."etc/wsamconf.php")) {
  	 $this->log(":WSAM: Loading configuration file..");
  	 require_once $this->directory."etc/wsamconf.php";
  	 return TRUE;
  	} else {
  	 $this->log(":WSAM: No configuration file found!");
  	}
  	return FALSE;
  }  

  private function log($text) {
   if ($this->options['debug']) { echo $text."<br />"; }  
  }
  
  private function checkmasterdb() {
   $this->log(":WSAM: Checking the master database..");
   if (!$this->existmasterdb()) { $this->createmasterdb(); }
   $this->db->query("SELECT * FROM `".$this->dbtable_conf."` LIMIT 1;");
   $mdb = $this->db->fetch_array();
   if ($mdb[0][0] < 1) { $this->addtomasterdb(); } 
   $this->checkmasterdbstructure();
   return TRUE;
  }
  
  private function addtomasterdb() {
  	$this->log(":WSAM: No content in the master db, inserting default..");
  	$ver = explode(".", $this->ver());
   return $this->db->query("INSERT INTO `".$this->dbtable_conf."` ( `major` , `minor` , `revision` ) VALUES ( '".$ver[0]."',  '".$ver[1]."',  '".$ver[2]."' );");
  }
  
  private function existmasterdb() {
   $this->log(":WSAM: Checking if the master database exist..");
   if ($this->db->exist_table($this->dbtable_conf)) {
    $this->log(":WSAM: The Master table exist.");
    return TRUE;
   } 
   $this->log(":WSAM: The master db does not exist.");
   return FALSE;
  }  
  
  private function createmasterdb() {
   $this->log(":WSAM: Creating master database..");
   $this->pkg_createdb();
   $this->log(":WSAM: Master database created!");
  }
  
  private function checkmasterdbstructure() {
  	$this->log(":WSAM: Checking the integrity of the master database..");
   $dbstruct = &$this->pkg_dbstruct;
   $retcode = 1;
   $this->db->query("DESCRIBE ".$this->dbtable_conf.";");   
   $sql = $this->db->fetch_array();
   $result = array ( array() );
   for ($i = 1; $i <= $dbstruct[0][0]; $i++) {
    $this->log("Checking table: ".$dbstruct[$i]['Field']."...");
    $result[$i]['name'] = $dbstruct[$i]['Field'];
    $result[$i]['found'] = FALSE;
    $result[$i]['match'] = FALSE;
    for ($j = 1; $j <= $sql[0][0]; $j++) {
     if ($dbstruct[$i]['Field'] == $sql[$j]['Field']) {
      $result[$i]['found'] = TRUE;
      if (($dbstruct[$i]['Type'] == $sql[$j]['Type']) && ($dbstruct[$i]['Null'] == $sql[$j]['Null']) && ($dbstruct[$i]['Key'] == $sql[$j]['Key']) && ($dbstruct[$i]['Default'] == $sql[$j]['Default']) && ($dbstruct[$i]['Extra'] == $sql[$j]['Extra'])) {
       $result[$i]['match'] = TRUE;      
      } 
     }
    }
    if ($result[$i]['found']) {
     $this->log("Found the table in the database!");
     if ($result[$i]['match']) {
      $this->log("The table matched the required structure!");
     } else {
      $this->log("The table did not match the required structure!");
      $retcode = 0;
     }       
    } else {
     $this->log("The table not found in the database!");
     $this->log("The table did not match the required structure!");
     $retcode = 1;
    }  
   }
   return $retcode;
  }
  
  function pkgobj() { return $this->pkgobj; }
  
  function sessions() { return $this->sessions; }
  
  function getsession() { return $this->session; }
 
 }
?>