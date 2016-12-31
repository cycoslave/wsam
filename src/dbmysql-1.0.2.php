<?php
 // MySQL database class 1.0.2
 // --> Desc.: Creates an object with direct access to a MySQL backend.
 // --> Req.: pdo_mysql
 // --> WARNING!: This class is not backward compatible with dbmysql below version 1.0.0.
 // (c) 2008-2016 Wicked Software

 class dbmysql {
  private $ver = "1.0.2";
  private $test = TRUE;   // works backward, false = test mode

  private $pkg_wsam = TRUE;
  private $pkg_wsamver = "2.3";
  private $pkg_name = "dbmysql";
  private $pkg_fullname = "MySQL database";
  private $pkg_desc ="Creates an object with direct access to a MySQL backend.";
  private $pkg_location = "/src/class/";  
  private $pkg_upgradable  = FALSE;
  private $pkg_priority = 1;
  private $pkg_dependencies = array ( 0 => array ( 0 => 0, "name" => "", "ver" => "", "sublocation" => FALSE, "hash" => "" ) );
  private $pkg_lastver = "1.0.1";    
  private $pkg_released = "09/10/2016";  
  private $pkg_copyright = "(c) 2016 Wicked Software";
  private $pkg_hasdatabase = FALSE;
  private $pkg_dbstruct = array ( 0 => array( 0 => 0, "Field" => "", "Type" => "", "Null" => "NO", "Key" => "", "Default" => "", "Extra" => "" ) );        
  private $dbtable = "";    

  private $mode = "standard";  
  
  //PDO objects
  private $pdo = FALSE;  
  private $st = FALSE;  
  
  private $db = FALSE;
  private $host = FALSE;
  private $user = FALSE;
  private $pass = FALSE;
  private $port = FALSE;
  private $farray = FALSE;
  
  private $log = FALSE;  
  

  function __construct ($mode="test", $host="127.0.0.1", $user="root", $pass="", $db="ws_dbmysql", $port=3306) {
   if ($mode == "test") {
    $this->test = FALSE;
   //} elseif ($mode = "useresult") {
   } else {
    $this->host = $host;
    $this->db = $db;
    $this->user = $user;
    $this->pass = $pass;
    $this->port = $port;      
    $this->init();
   }
  }

  function __destruct() {
   //$this->close($this->sql);
   //$this->close($this->result);
  }
  
  function __sleep() {
  	$this->reset();
  	return array('db', 'host', 'user', 'pass', 'port');
  }
  
  function __wakeup() {
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

  
  private function init() {
   if ($this->test) {
    try {
     $this->pdo = new PDO("mysql:host=".$this->host.";port=".$this->port.";dbname=".$this->db, $this->user, $this->pass);
    } catch (PDOException $e) {
     $this->pdo = FALSE;
     //echo $e->getMessage();
     $this->connect_exception($e);
    }   
    //$this->sql = new mysqli($this->host, $this->user, $this->pass, $this->db, $this->port);
    //if (($this->sql->connect_errno) || ($this->sql->connect_error)) { die("Connect Error (".$this->sql->connect_errno.") -".$this->sql->connect_error); }   
    return TRUE;
   }
   return FALSE;
  }
  
  private function connect_exception($ex=FALSE) {
  	if ($this->test) {
  	 if ($this->log) {
  	  //echo $ex->getMessage();
  	  return;
  	 }
  	}
  	return FALSE;
  }
  
  function reset() { 
   if ($this->test) {
    $this->st = NULL; $this->st = FALSE; 
    $this->pdo = NULL; $this->pdo = FALSE;
    return TRUE; 
   } 
   return FALSE;
  }
  
  function query($query=FALSE) {
   if ($this->test) {
    $this->init();
    if ($this->pdo !== FALSE) {
     if ($query) {
      $this->st = $this->pdo->query($query);
      return TRUE;
     }
    }
   }
   return FALSE;
  }
  
  function prepare($query=FALSE) {
  	if ($this->test) {
  	 $this->init();
  	 if ($this->pdo !== FALSE) {
  	  $this->st = $this->pdo->prepare($query);
  	  return TRUE;
  	 }
  	}
  	return FALSE;
  }
  
  function bind($param=FALSE, $value=FALSE, $datatype=FALSE, $length=FALSE) {
  	if ($this->test) {
  	 if ($this->st !== FALSE) {
  	  if ($param[0] == ":") {
  	   if ($datatype == "bool") {
  	    $datatype = PDO::PARAM_BOOL;
  	   } elseif ($datatype == "null") {
  	   	$datatype = PDO::PARAM_NULL;
  	   } elseif ($datatype == "int") {
  	   	$datatype = PDO::PARAM_INT;
  	   } elseif ($datatype == "string") {
  	   	$datatype = PDO::PARAM_STR;
  	   } elseif ($datatype == "lob") {
  	   	$datatype = PDO::PARAM_LOB;
  	   } elseif ($datatype == "stmt") {
  	   	$datatype = PDO::PARAM_STMT;
  	   } else {
  	   	$datatype = FALSE;
  	   }
  	   if ($datatype) {
  	    if ($length) {
  	     return $this->st->bindParam($param, $value, $datatype, $length);
  	    } else {
  	     return $this->st->bindParam($param, $value, $datatype);
  	    }
  	   } else {
  	    return $this->st->bindParam($param, $value);
  	   }
  	  } 
  	 }
  	}
  	return FALSE;
  }
  
  function exec() {
  	if ($this->test) {
  	 if ($this->st !== FALSE) {
  	  return $this->st->execute();
  	 }
  	}
  	return FALSE;  	
  }
  
  function fetch_array($max=FALSE) {
   if ($this->test) {
    if ($this->st !== FALSE) {
     $tmp = $this->st->fetchAll();
     array_unshift($tmp, 0);
     $tmp[0] = array();
     $tmp[0][0] = $this->st->rowCount();
     return $tmp;
    }
   }
   return FALSE;
  }
  function fetch_all_array($max=FALSE) { return $this->fetch_array($max); }
  
    
  
  function sqlver() {
  	if ($this->test) {
    $this->query("SELECT VERSION() as ver;");
    $tmp = $this->fetch_array(); 
    return $tmp[1]['ver'];   	
  	}
  	return FALSE;
  }    
  
  function getlastidadded() {
  	if ($this->test) {
  	 $this->init();
  	 return $this->pdo->lastInsertId();
  	}
  	return FALSE;
  }
  
  function exist_table($table=FALSE) {
   if ($this->test) {
    if ($table) {
     $this->query("SHOW TABLES LIKE '".$table."';");
     $sql = $this->fetch_array();
     if ($sql[0][0]) { return TRUE; }
    }
   }
   return FALSE;
  }

  function escape($string=FALSE, $type=FALSE) { 
   if ($this->test) { 
    if ($this->pdo !== FALSE) {
     $datatype = FALSE;
     if ($type == "bool") {
  	   $datatype = PDO::PARAM_BOOL;
  	  } elseif ($type == "null") {
  	  	$datatype = PDO::PARAM_NULL;
  	  } elseif ($type == "int") {
  	  	$datatype = PDO::PARAM_INT;
  	  } elseif ($type == "string") {
  	  	$datatype = PDO::PARAM_STR;
  	  } elseif ($type == "lob") {
  	  	$datatype = PDO::PARAM_LOB;
  	  } elseif ($type == "stmt") {
  	  	$datatype = PDO::PARAM_STMT;
  	  } else {
  	  	$datatype = FALSE;
  	  }
  	  if ($datatype) {
  	  	return $this->pdo->quote($string, $datatype);
  	  } else {
      return $this->pdo->quote($string);
     }
    } 
   } 
   return FALSE;
  }
  
  function affected_rows() { 
   if ($this->test) { 
    if ($this->st !== FALSE) {
     return $this->st->rowCount();
    } 
   } 
   return FALSE;
  }  
  
  function commit() {
  	if ($this->test) { 
    if ($this->pdo !== FALSE) {
     return $this->pdo->commit();
    }
   }
   return FALSE;
  }
  
  function rollback() {
  	if ($this->test) { 
    if ($this->pdo !== FALSE) {
     return $this->pdo->rollBack();
    }
   }
   return FALSE;  	
  }
  
 }
?>