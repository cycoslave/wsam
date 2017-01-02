<?php
 define('WSAM_LAUNCH', microtime(true));
 
 function __autoload($name) {
  $file = "";
  $namespace = "";

  $path = __DIR__;

  if (($lastpos = strripos($name, "\\")) !== FALSE) {
   $namespace = substr($name, 0, $lastpos);
   $name = substr($name, $lastpos + 1);
   $file = str_replace("\\", DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
  }
  $file .= str_replace("_", DIRECTORY_SEPARATOR, $name).".php";
  $filename = $path.DIRECTORY_SEPARATOR.$file;
        
  if (file_exists($filename)) {
   require $filename;
  } else {
   echo "Class ".$name." does not exist.<br>";
   echo " -filename: ".$filename."<br>";
   echo " -path: ".$path."<br>";
   echo " -file: ".$file."<br>";
   echo " -namespace: ".$namespace."<br>";
  }
 }
 spl_autoload_register('__autoload'); // Registers the autoloader
?>