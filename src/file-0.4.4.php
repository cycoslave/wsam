<?php
 // File class 0.4.4
 // --> Desc.: Handles the communication with a file.
 // (c) 2008-2015 Wicked Software

 class file {
  private $ver = "0.4.4";
  private $test = true;   // works backward, false = test mode
  
  private $pkg_wsam = TRUE;
  private $pkg_wsamver = "2.2";
  private $pkg_name = "file";
  private $pkg_fullname = "File";
  private $pkg_desc ="Handles the communication with a file.";
  private $pkg_location = "/src/class/";  
  private $pkg_upgradable  = 1;
  private $pkg_priority = 1;
  private $pkg_dependencies = array ( 0 => array ( 0 => 0, "name" => "", "ver" => "", "sublocation" => FALSE, "hash" => "" ) );
  private $pkg_lastver = "0.4.3";    
  private $pkg_released = "20/01/2014";  
  private $pkg_copyright = "(c) 2015 Wicked Software";
  private $pkg_hasdatabase = FALSE;
  private $pkg_dbstruct = array ( 0 => array( 0 => 0, "Field" => "", "Type" => "", "Null" => "NO", "Key" => "", "Default" => "", "Extra" => "" ) );       
  private $dbtable = "";   

  var $varfolder = "";
  var $filename = "";
  var $content = "";
  var $flag = "";
  var $file = FALSE;


  function __construct ($filename="test",$flag="w") {
   if (($filename == "test") && ($flag == "test")) {

   } 
   $this->filename = $filename;
   $this->flag = $flag;   
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

  //Check the file exists
  function exists() {
   if ($this->test) {
    if (file_exists($this->fullfilename())) { return TRUE; }
    return FALSE;
   }
  }

  //Creates the file if it doesn't exists
  function create() {
   if ($this->test) {   
    if ($this->exists() == 0) {
     $this->file = fopen($this->fullfilename(), $this->flag);
     return TRUE;
    }
    return FALSE;
   }
  }

  // Used to change the $varfolder
  function setFolder($folder) {
   if ($this->test) {   
    $this->varfolder = $folder;
   }
  }

  // Used to add content to our file object
  function addContent($data) {
   if ($this->test) {
    $this->content .= "{$data}\n";
   }
  }

  // Used to set the content to our file object
  function setContent($data) {
   if ($this->test) {
    $this->content = $data;
   }
  }

  //Returns the full filename
  function fullfilename() {
   if ($this->test) {
    return $this->varfolder.$this->filename;
   }
  }

  //Writes our variables to the file.
  function write() {
   if ($this->test) {
    if ($this->file === FALSE) { $this->file = fopen($this->filename, $this->flag); }
    if ((is_writable($this->filename)) && ($this->content != "")) {
     fwrite($this->file, $this->content);
     return TRUE;
    } else {
     return FALSE;
    }
   }
  }

  //Removes a file.
  function removeFile() {
   if ($this->test) {
   }
  }

  //Renames a file.
  function renameFile() {
   if ($this->test) {
   }  
  }

  //Copies the files to another location.
  function copyFile($newname) {
   if ($this->test) {
   }  
  }

  //Creates a folder.
  function createFolder() {
   if ($this->test) {
   }  
  }

 }

?>